<?php

namespace cseries\Service;

class SeriesManager {
    private $app;
    private $db_name = 'CSeries_series';

    public function __construct($app) {
        $this->app = $app;
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
}
