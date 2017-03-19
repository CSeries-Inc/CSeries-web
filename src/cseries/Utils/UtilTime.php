<?php
namespace cseries\Utils;

class UtilTime {
    public static function timestampToString($time) {
        $periods = array("seconde", "minute", "heure", "jour", "semaine", "mois", "année", "siècle");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();
        $unix_date = $time;

        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            $tense = "Il y a";
        } else {
            $difference = $unix_date - $now;
            $tense = "Il y a quelque secondes";
        }
        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        if ($difference != 1) {
            $periods[$j].= "s";
        }
        return "{$tense} $difference $periods[$j]";
    }
}