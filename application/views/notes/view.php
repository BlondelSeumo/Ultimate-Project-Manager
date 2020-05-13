<div class="modal-body clearfix general-form">
    <div class="form-group">
        <div  class="col-md-12 notepad-title">
            <strong><?php echo $model_info->title; ?></strong>
            <?php
            if ($model_info->is_public) {
                echo "<div class='text-off font-11'>";
                echo "<i class='fa fa-globe text-off mr5''></i>";
                if ($model_info->created_by == $this->login_user->id) {
                    echo lang("marked_as_public");
                } else {
                    echo lang("public_note_by") . ": " . get_team_member_profile_link($model_info->created_by, $model_info->created_by_user_name);
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>
    <div class="col-md-12 mb15 notepad">
        <?php
        echo nl2br(link_it($model_info->description));
        ?>
    </div>

    <div class="col-md-12">
        <?php
        $note_labels = "";
        $labels = explode(",", $model_info->labels);
        foreach ($labels as $label) {
            $note_labels .= "<span class='label label-info'>" . $label . "</span> ";
        };
        echo $note_labels;
        ?>
    </div>

    <div class="col-md-12 mt15">
        <?php
        $files = unserialize($model_info->files);
        $total_files = count($files);
        $this->load->view("includes/timeline_preview", array("files" => $files));
        ?>
    </div>

</div>

<div class="modal-footer">
    <?php
    if ($model_info->created_by == $this->login_user->id || $this->login_user->is_admin) {
        echo modal_anchor(get_uri("notes/modal_form/"), "<i class='fa fa-pencil'></i> " . lang('edit_note'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => lang('edit_note')));
    }
    ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>