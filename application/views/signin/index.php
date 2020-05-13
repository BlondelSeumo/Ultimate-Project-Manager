<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view('includes/head'); ?>
    </head>
    <body>
        <?php
        if (get_setting("show_background_image_in_signin_page") === "yes") {
            $background_url = get_file_from_setting("signin_page_background");
            ?>
            <style type="text/css">
                body {background-image: url('<?php echo $background_url; ?>'); background-size:cover}
            </style>
        <?php } ?>

        <?php if (get_setting("enable_footer")) { ?>
            <div class="scrollable-page">
            <?php } ?>

            <div class="signin-box">
                <?php
                if (isset($form_type) && $form_type == "request_reset_password") {
                    $this->load->view("signin/reset_password_form");
                } else if (isset($form_type) && $form_type == "new_password") {
                    $this->load->view('signin/new_password_form');
                } else {
                    $this->load->view("signin/signin_form");
                }
                ?>
            </div>

            <?php if (get_setting("enable_footer")) { ?>
            </div>
        <?php } ?>

        <?php $this->load->view("includes/footer"); ?>
    </body>
</html>