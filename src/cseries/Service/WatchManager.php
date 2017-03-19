<?php

namespace cseries\Service;

class WatchManager {
    private $app;
    private $db_name = 'CSeries_watch';

    public function __construct($app) {
        $this->app = $app;
    }

    public function createOrUpdate($user, $serie, $saison, $episode, $time) {
        if ($this->getForUserAndSerieCount($user, $serie) > 0) {
            $this->app['db']->update($this->db_name, array('saison' => $saison, 'episode' => $episode, 'time' => $time), array('user_id' => $user, 'serie_id' => $serie));
        } else {
            $this->app['db']->insert($this->db_name, array('user_id' => $user, 'serie_id' => $serie, 'saison' => $saison, 'episode' => $episode, 'time' => $time));
        }
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE ID = ?";
        $post = $this->app['db']->fetchAssoc($sql, array($id));

        return $post;
    }

    public function getForUser($id) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE user_id = ?";
        $post = $this->app['db']->fetchAssoc($sql, array($id));

        return $post;
    }

    public function getForUserAndSerie($id, $serie) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE user_id = ? AND serie_id = ?";
        $post = $this->app['db']->fetchAssoc($sql, array($id, $serie));

        return $post;
    }

    public function getForUserAndSerieCount($id, $serie) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE user_id = ? AND serie_id = ?";
        $post = $this->app['db']->fetchAll($sql, array($id, $serie));

        return count($post);
    }

    public function getWatchedSeries($user) {
        $sql = "SELECT * FROM " . $this->db_name . " WHERE user_id = '" . $user . "' ORDER BY ID DESC";
        $post = $this->app['db']->fetchAll($sql, array());

        return $post;
    }
}
