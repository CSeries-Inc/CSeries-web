<?php

namespace cseries\Service;

class UserManager {
    private $app;
    private $db_name = 'CSeries_users';

    public function __construct($app) {
        $this->app = $app;
    }

    public function create($full_name, $email, $username, $password, $invitation_code) {
        $this->app['db']->insert($this->db_name, array('full_name' => $full_name, 'email' => $email, 'username' => $username, 'password' => sha1(md5($password)), 'invitation_code' => $invitation_code, 'admin' => '0', 'date' => time()));
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE ID = ?";
        $post = $this->app['db']->fetchAssoc($sql, array($id));

        return $post;
    }

    public function getByUsername($username) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE username = ?";
        $post = $this->app['db']->fetchAssoc($sql, array($username));

        return $post;
    }

    public function getAll($user) {
        $sql = "SELECT * FROM " . $this->db_name . " ORDER BY ID DESC";
        $post = $this->app['db']->fetchAll($sql, array());

        return $post;
    }

    public function checkRegister($email, $username){
        $sql = "SELECT * FROM " . $this->db_name . " WHERE (email = '".$email."' AND username = '".$username."') OR (email = '".$email."' OR username = '".$username."')";
        $post = $this->app['db']->fetchAll($sql, array());

        return (count($post) < 1?true:false);
    }
}
