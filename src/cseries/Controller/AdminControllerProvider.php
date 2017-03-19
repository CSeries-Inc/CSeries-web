<?php

namespace cseries\Controller;

use cseries\Service\FilmsManager;
use cseries\Service\SeriesManager;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ApiControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->match('/series', function (Request $request) use ($app) {
            $token  = new \Tmdb\ApiToken('1f5a707eebaff5c4a425834d7b2c144f');
            $client = new \Tmdb\Client($token);

            $seriesManager = new SeriesManager($app);
            $series = $seriesManager->getAll();
            $counter = 0;
            foreach ($series as $serie){
                $api = $client->getTvApi()->getTvshow($serie['tmdb_id'], array('language' => 'fr'));
                $films[$counter]['name'] = $api['name'];
                $films[$counter]['description'] = $api['overview'];
                $films[$counter]['picture'] = 'https://image.tmdb.org/t/p/w300_and_h450_bestv2/'.$api['poster_path'];
                $counter++;
            }

            return json_encode(array('series' => $films));
        });

        $controllers->match('/series/{id}/saisons', function (Request $request, $id) use ($app) {
            $token  = new \Tmdb\ApiToken('1f5a707eebaff5c4a425834d7b2c144f');
            $client = new \Tmdb\Client($token);

            $seriesManager = new SeriesManager($app);
            $serie = $seriesManager->get($id);
            $saisons = array();
            $api = $client->getTvApi()->getTvshow($serie['tmdb_id'], array('language' => 'fr'));
            $counter = 0;
            foreach ($api['seasons'] as $saison){
                $saisons[$counter] = $api['seasons'][$counter];
                $counter++;
            }

            return json_encode(array('saisons' => $saisons));
        });

        $controllers->match('/films', function (Request $request) use ($app) {
            $token  = new \Tmdb\ApiToken('1f5a707eebaff5c4a425834d7b2c144f');
            $client = new \Tmdb\Client($token);

            $filmManager = new FilmsManager($app);
            $films = $filmManager->getAll();
            $counter = 0;
            foreach ($films as $film){
                $api = $client->getMoviesApi()->getMovie($film['tmdb_id'], array('language' => 'fr'));
                $films[$counter]['title'] = $api['title'];
                $films[$counter]['cover'] = 'https://image.tmdb.org/t/p/w300_and_h450_bestv2/'.$api['poster_path'];
                $counter++;
            }

            return json_encode(array('films' => $films));
        });

        $controllers->match('/films/{id}', function (Request $request, $id) use ($app) {
            $token  = new \Tmdb\ApiToken('1f5a707eebaff5c4a425834d7b2c144f');
            $client = new \Tmdb\Client($token);

            $filmManager = new FilmsManager($app);
            $film = $filmManager->get($id);
            $api = $client->getMoviesApi()->getMovie($film['tmdb_id'], array('language' => 'fr'));
            $film['title'] = $api['title'];
            $film['cover'] = 'https://image.tmdb.org/t/p/w300_and_h450_bestv2/'.$api['poster_path'];
            $film['url'] = $app['cdn_url'].'films/'.$film['tmdb_id'].'.mp4';

            return json_encode($film);
        });

        ######## Films ########

        return $controllers;
    }


}
