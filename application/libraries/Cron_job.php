<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron_job {

    private $today = null;
    private $ci = null;

    function run() {
        $this->today = get_today_date();
        $this->ci = get_instance();
        $current_time = strtotime(get_current_utc_time());

        //wait 1 hour for invoice creation
        $last_cron_job_time_of_invoice_creation = get_setting('last_cron_job_time_of_invoice_creation');
        if ($last_cron_job_time_of_invoice_creation == "" || ($current_time > ($last_cron_job_time_of_invoice_creation * 1 + 3600))) {
            $this->create_recurring_invoices();

            $this->send_invoice_due_pre_reminder();
            $this->send_invoice_due_after_reminder();
            $this->send_recurring_invoice_creation_reminder();

            //wait 1 hour for recurring task creation too
            if (get_setting("enable_recurring_option_for_tasks")) {
                $this->create_recurring_tasks();
            }

            $this->send_task_reminder_notifications();

            $this->ci->Settings_model->save_setting("last_cron_job_time_of_invoice_creation", $current_time);
        }

        //wait 10 minutes for imap operation
        $last_cron_job_time_of_imap = get_setting('last_cron_job_time_of_imap');
        if ($last_cron_job_time_of_imap == "" || ($current_time > ($last_cron_job_time_of_imap * 1 + 600))) {
            $this->run_imap();

            $this->ci->Settings_model->save_setting("last_cron_job_time_of_imap", $current_time);
        }

        $this->get_google_calendar_events();

        //run inactive ticket closing operation only once in a day
        $inactive_ticket_closing_date = get_setting("inactive_ticket_closing_date");
        if ($inactive_ticket_closing_date == "" || ($inactive_ticket_closing_date != $this->today)) {
            $this->close_inactive_tickets();

            $this->ci->Settings_model->save_setting("inactive_ticket_closing_date", $this->today);
        }
    }

    private function send_invoice_due_pre_reminder() {

        $reminder_date = get_setting("send_invoice_due_pre_reminder");

        if ($reminder_date) {

            //prepare invoice due date accroding to the setting
            $start_date = add_period_to_date($this->today, get_setting("send_invoice_due_pre_reminder"), "days");

            $invoices = $this->ci->Invoices_model->get_details(array(
                        "status" => "not_paid", //find all invoices which are not paid yet but due date not expired
                        "start_date" => $start_date,
                        "end_date" => $start_date, //both should be same
                        "exclude_due_reminder_date" => $this->today //don't find invoices which reminder already sent today
                    ))->result();

            foreach ($invoices as $invoice) {
                log_notification("invoice_due_reminder_before_due_date", array("invoice_id" => $invoice->id), "0");
            }
        }
    }

    private function send_invoice_due_after_reminder() {

        $reminder_date = get_setting("send_invoice_due_after_reminder");

        if ($reminder_date) {

            //prepare invoice due date accroding to the setting
            $start_date = subtract_period_from_date($this->today, get_setting("send_invoice_due_after_reminder"), "days");

            $invoices = $this->ci->Invoices_model->get_details(array(
                        "status" => "overdue", //find all invoices where due date has expired
                        "start_date" => $start_date,
                        "end_date" => $start_date, //both should be same
                        "exclude_due_reminder_date" => $this->today //don't find invoices which reminder already sent today
                    ))->result();

            foreach ($invoices as $invoice) {
                log_notification("invoice_overdue_reminder", array("invoice_id" => $invoice->id), "0");
            }
        }
    }

    private function send_recurring_invoice_creation_reminder() {

        $reminder_date = get_setting("send_recurring_invoice_reminder_before_creation");

        if ($reminder_date) {

            //prepare invoice due date accroding to the setting
            $start_date = add_period_to_date($this->today, get_setting("send_recurring_invoice_reminder_before_creation"), "days");

            $invoices = $this->ci->Invoices_model->get_details(array(
                        "status" => "not_paid", //non-draft invoices
                        "recurring" => 1,
                        "next_recurring_start_date" => $start_date,
                        "next_recurring_end_date" => $start_date, //both should be same
                        "exclude_recurring_reminder_date" => $this->today //don't find invoices which reminder already sent today
                    ))->result();

            foreach ($invoices as $invoice) {
                log_notification("recurring_invoice_creation_reminder", array("invoice_id" => $invoice->id), "0");
            }
        }
    }

    private function create_recurring_invoices() {
        $recurring_invoices = $this->ci->Invoices_model->get_renewable_invoices($this->today);
        if ($recurring_invoices->num_rows()) {
            foreach ($recurring_invoices->result() as $invoice) {
                $this->_create_new_invoice($invoice);
            }
        }
    }

    //create new invoice from a recurring invoice 
    private function _create_new_invoice($invoice) {

        //don't update the next recurring date when updating invoice manually?
        //stop backdated recurring invoice creation.
        //check recurring invoice once/hour?
        //settings: send invoice to client


        $bill_date = $invoice->next_recurring_date;
        $diff_of_due_date = get_date_difference_in_days($invoice->due_date, $invoice->bill_date); //calculate the due date difference of the original invoice
        $due_date = add_period_to_date($bill_date, $diff_of_due_date, "days");



        $new_invoice_data = array(
            "client_id" => $invoice->client_id,
            "project_id" => $invoice->project_id,
            "bill_date" => $bill_date,
            "due_date" => $due_date,
            "note" => $invoice->note,
            "status" => "draft",
            "tax_id" => $invoice->tax_id,
            "tax_id2" => $invoice->tax_id2,
            "tax_id3" => $invoice->tax_id3,
            "recurring_invoice_id" => $invoice->id,
            "discount_amount" => $invoice->discount_amount,
            "discount_amount_type" => $invoice->discount_amount_type,
            "discount_type" => $invoice->discount_type
        );

        //create new invoice
        $new_invoice_id = $this->ci->Invoices_model->save($new_invoice_data);

        //create invoice items
        $items = $this->ci->Invoice_items_model->get_details(array("invoice_id" => $invoice->id))->result();
        foreach ($items as $item) {
            //create invoice items for new invoice
            $new_invoice_item_data = array(
                "title" => $item->title,
                "description" => $item->description,
                "quantity" => $item->quantity,
                "unit_type" => $item->unit_type,
                "rate" => $item->rate,
                "total" => $item->total,
                "invoice_id" => $new_invoice_id,
            );
            $this->ci->Invoice_items_model->save($new_invoice_item_data);
        }


        //update the main recurring invoice
        $no_of_cycles_completed = $invoice->no_of_cycles_completed + 1;
        $next_recurring_date = add_period_to_date($bill_date, $invoice->repeat_every, $invoice->repeat_type);


        $recurring_invoice_data = array(
            "next_recurring_date" => $next_recurring_date,
            "no_of_cycles_completed" => $no_of_cycles_completed
        );
        $this->ci->Invoices_model->save($recurring_invoice_data, $invoice->id);

        //finally send notification
        log_notification("recurring_invoice_created_vai_cron_job", array("invoice_id" => $new_invoice_id), "0");
    }

    private function get_google_calendar_events() {
        $this->ci->load->library("google_calendar");
        $this->ci->google_calendar->get_google_calendar_events();
    }

    private function run_imap() {
        if (get_setting("enable_email_piping") && get_setting("imap_authorized")) {
            $this->ci->load->library("imap");
            $this->ci->imap->run_imap();
        }
    }

    private function create_recurring_tasks() {
        $recurring_tasks = $this->ci->Tasks_model->get_renewable_tasks($this->today);
        if ($recurring_tasks->num_rows()) {
            foreach ($recurring_tasks->result() as $task) {
                $this->_create_new_task($task);
            }
        }
    }

    //create new task from a recurring task 
    private function _create_new_task($task) {

        //don't update the next recurring date when updating task manually
        //stop backdated recurring task creation.
        //check recurring task once/hour?

        $start_date = $task->next_recurring_date;
        $deadline = NULL;

        if ($task->deadline) {
            $task_start_date = $task->start_date ? $task->start_date : $task->created_date;
            $diff_of_deadline = get_date_difference_in_days($task->deadline, $task_start_date); //calculate the deadline difference of the original task
            $deadline = add_period_to_date($start_date, $diff_of_deadline, "days");
        }

        $new_task_data = array(
            "title" => $task->title,
            "description" => $task->description,
            "project_id" => $task->project_id,
            "milestone_id" => $task->milestone_id,
            "points" => $task->points,
            "status_id" => $task->status_id,
            "labels" => $task->labels,
            "points" => $task->points,
            "start_date" => $start_date,
            "deadline" => $deadline,
            "recurring_task_id" => $task->id,
            "assigned_to" => $task->assigned_to,
            "collaborators" => $task->collaborators,
            "created_date" => get_current_utc_time(),
            "activity_log_created_by_app" => true
        );

        //create new task
        $new_task_id = $this->ci->Tasks_model->save($new_task_data);

        //create checklist items
        $this->ci->load->model("Checklist_items_model");
        $checklist_item_options = array("task_id" => $task->id);
        $checklist_items = $this->ci->Checklist_items_model->get_details($checklist_item_options);
        if ($checklist_items->num_rows()) {
            foreach ($checklist_items->result() as $item) {
                $checklist_item_data = array(
                    "title" => $item->title,
                    "is_checked" => $item->is_checked,
                    "task_id" => $new_task_id,
                    "sort" => $item->sort
                );

                $this->ci->Checklist_items_model->save($checklist_item_data);
            }
        }

        //update the main recurring task
        $no_of_cycles_completed = $task->no_of_cycles_completed + 1;
        $next_recurring_date = add_period_to_date($start_date, $task->repeat_every, $task->repeat_type);

        $recurring_task_data = array(
            "next_recurring_date" => $next_recurring_date,
            "no_of_cycles_completed" => $no_of_cycles_completed
        );
        $this->ci->Tasks_model->save_reminder_date($recurring_task_data, $task->id);

        //send notification
        $notification_option = array("project_id" => $task->project_id, "task_id" => $new_task_id);
        log_notification("recurring_task_created_via_cron_job", $notification_option, "0");
    }

    private function send_task_reminder_notifications() {
        $notification_option = array("notification_multiple_tasks" => true);
        log_notification("project_task_deadline_pre_reminder", $notification_option, "0");
        log_notification("project_task_deadline_overdue_reminder", $notification_option, "0");
        log_notification("project_task_reminder_on_the_day_of_deadline", $notification_option, "0");
    }

    private function close_inactive_tickets() {
        $auto_close_ticket_after_days = get_setting("auto_close_ticket_after");

        if ($auto_close_ticket_after_days) {
            //prepare last activity date accroding to the setting
            $last_activity_date = subtract_period_from_date($this->today, get_setting("auto_close_ticket_after"), "days");

            $tickets = $this->ci->Tickets_model->get_details(array(
                        "status" => "open", //don't find closed tickets
                        "last_activity_date_or_before" => $last_activity_date
                    ))->result();

            foreach ($tickets as $ticket) {
                //make ticket closed
                $ticket_data = array(
                    "status" => "closed",
                    "closed_at" => get_current_utc_time()
                );

                $this->ci->Tickets_model->save($ticket_data, $ticket->id);

                //send notification
                log_notification("ticket_closed", array("ticket_id" => $ticket->id), "0");
            }
        }
    }

}
