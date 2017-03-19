<?php

namespace cseries\Service;

class InvitationCodeManager {

    private $app;
    private $db_name = 'CSeries_invitationcode';

    public function __construct($app) {
        $this->app = $app;
    }

    public function create($code) {
        $this->app['db']->insert($this->db_name, array('code' => $code));
    }

    public function delete($code) {
        $this->app['db']->delete($this->db_name, array('code' => $code));
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE ID = ?";
        $post = $this->app['db']->fetchAssoc($sql, array($id));

        return $post;
    }

    public function getAll() {
        $sql = "SELECT * FROM " . $this->db_name . " ORDER BY ID DESC";
        $post = $this->app['db']->fetchAll($sql, array());

        return $post;
    }

    public function checkUsed($code){
        $sql = "SELECT * FROM CSeries_users WHERE invitation_code = '".$code."'";
        $post = $this->app['db']->fetchAll($sql, array());

        return count($post) > 0;
    }

    public function checkExist($code){
        $sql = "SELECT * FROM ".$this->db_name." WHERE code = '".$code."'";
        $post = $this->app['db']->fetchAll($sql, array());

        return count($post) > 0;
    }
}
