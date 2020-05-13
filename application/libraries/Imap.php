<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Imap {

    private $ci;

    public function __construct() {
        $this->ci = & get_instance();

        //load EmailReplyParser resources
        require_once(APPPATH . "third_party/Imap/EmailReplyParser/vendor/autoload.php");

        //load ddeboer-imap resources
        require_once(APPPATH . "third_party/Imap/ddeboer-imap/vendor/autoload.php");
    }

    function authorize_imap_and_get_inbox() {
        $host = get_setting("imap_host");
        $port = get_setting("imap_port");
        $ssl = get_setting('imap_ssl_enabled') ? "/imap/ssl/validate-cert" : "";
        $email_address = get_setting("imap_email");
        $password = decode_password(get_setting('imap_password'), "imap_password");

        $server = new \Ddeboer\Imap\Server($host, $port, $ssl);

        try {
            $connection = $server->authenticate($email_address, $password);

            //the credentials is valid. store to settings that it's authorized
            $this->ci->Settings_model->save_setting("imap_authorized", 1);
            return $connection;
        } catch (Exception $exc) {
            log_message('error', $exc);
            $this->ci->Settings_model->save_setting("imap_authorized", 0);
            return false;
        }
    }

    public function run_imap() {
        $connection = $this->authorize_imap_and_get_inbox();
        $mailbox = $connection->getMailbox('INBOX'); //get mails of inbox only
        $messages = $mailbox->getMessages();

        foreach ($messages as $message) {
            //create tickets for unread mails
            if (!$message->isSeen()) {
                $this->_create_ticket_from_imap($message);

                //mark the mail as read
                $message->markAsSeen();
            }
        }
    }

    private function _create_ticket_from_imap($message_info = "") {
        if ($message_info) {
            $email = $message_info->getFrom()->getAddress();
            $subject = $message_info->getSubject();

            //check if there has any client containing this email address
            //if so, go through with the client id
            $client_info = $this->ci->Users_model->get_one_where(array("email" => $email, "user_type" => "client", "deleted" => 0));

            if (get_setting("create_tickets_only_by_registered_emails") && !$client_info->id) {
                return false;
            }

            $ticket_id = $this->_get_ticket_id_from_subject($subject);

            //check if the ticket is exists on the app
            //if not, that will be considered as a new ticket
            //but for this case, it's a replying email. we've to parse the message
            $replying_email = false;
            if ($ticket_id) {
                $existing_ticket_info = $this->ci->Tickets_model->get_one_where(array("id" => $ticket_id, "deleted" => 0));
                if (!$existing_ticket_info->id) {
                    $ticket_id = "";
                    $replying_email = true;
                }
            }

            if ($ticket_id) {
                //if the message have ticket id, we have to assume that, it's a reply of the specific ticket
                $ticket_comment_id = $this->_save_tickets_comment($ticket_id, $message_info, $client_info, true);

                if ($ticket_id && $ticket_comment_id) {
                    log_notification("ticket_commented", array("ticket_id" => $ticket_id, "ticket_comment_id" => $ticket_comment_id, "exclude_ticket_creator" => true), $client_info->id ? $client_info->id : "0");
                }
            } else {

                $creator_name = $message_info->getFrom()->getName();
                $now = get_current_utc_time();
                $ticket_data = array(
                    "title" => $subject ? $subject : $email, //show creator's email as ticket's title, if there is no subject
                    "created_at" => $now,
                    "creator_name" => $creator_name ? $creator_name : "",
                    "creator_email" => $email ? $email : "",
                    "client_id" => $client_info->id ? $client_info->client_id : 0,
                    "created_by" => $client_info->id ? $client_info->id : 0,
                    "last_activity_at" => $now
                );

                $ticket_id = $this->ci->Tickets_model->save($ticket_data);

                if ($ticket_id) {
                    //save email message as the ticket's comment
                    $ticket_comment_id = $this->_save_tickets_comment($ticket_id, $message_info, $client_info, $replying_email);

                    if ($ticket_id && $ticket_comment_id) {
                        log_notification("ticket_created", array("ticket_id" => $ticket_id, "ticket_comment_id" => $ticket_comment_id, "exclude_ticket_creator" => true), $client_info->id ? $client_info->id : "0");
                    }
                }
            }
        }
    }

    private function _prepare_replying_message($message = "") {
        $reply_parser = new \EmailReplyParser\EmailReplyParser();
        return $reply_parser->parseReply($message);
    }

    //save tickets comment
    private function _save_tickets_comment($ticket_id, $message_info, $client_info, $is_reply = false) {
        if ($ticket_id) {
            $description = $message_info->getBodyText();
            if ($is_reply) {
                $description = $this->_prepare_replying_message($description);
            }

            $comment_data = array(
                "description" => $description,
                "ticket_id" => $ticket_id,
                "created_by" => $client_info->id ? $client_info->id : 0,
                "created_at" => get_current_utc_time()
            );

            $comment_data = clean_data($comment_data);

            $files_data = $this->_prepare_attachment_data_of_mail($message_info);
            $comment_data["files"] = serialize($files_data);

            $ticket_comment_id = $this->ci->Ticket_comments_model->save($comment_data);

            return $ticket_comment_id;
        }
    }

    //get ticket id
    private function _get_ticket_id_from_subject($subject = "") {
        if ($subject) {
            $find_hash = strpos($subject, "#");
            if ($find_hash) {
                $rest_from_hash = substr($subject, $find_hash + 1); //get the rest text from ticket's #
                $ticket_id = (int) substr($rest_from_hash, 0, strpos($rest_from_hash, " "));

                if ($ticket_id && is_int($ticket_id)) {
                    return $ticket_id;
                }
            }
        }
    }

    //download attached files to local
    private function _prepare_attachment_data_of_mail($message_info = "") {
        if ($message_info) {
            $files_data = array();
            $attachments = $message_info->getAttachments();

            foreach ($attachments as $attachment) {
                //move files to the directory
                $file_data = move_temp_file($attachment->getFilename(), get_setting("timeline_file_path"), "imap_ticket", NULL, "", $attachment->getDecodedContent());

                array_push($files_data, $file_data);
            }

            return $files_data;
        }
    }

}
