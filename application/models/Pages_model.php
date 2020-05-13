<?php

class Pages_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'pages';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $pages_table = $this->db->dbprefix('pages');

        $where = "";

        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $pages_table.id=$id";
        }



        $slug = get_array_value($options, "slug");
        $slug = $this->db->escape_str($slug);

        if ($slug) {
            $where = " AND $pages_table.slug='$slug'";
        }

        $sql = "SELECT $pages_table.*
        FROM $pages_table
        WHERE $pages_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function is_slug_exists($slug, $id = 0) {
        $result = $this->get_all_where(array("slug" => $slug, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

}
