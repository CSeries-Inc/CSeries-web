<?php

namespace cseries\Controller;

use cseries\Service\FilmsManager;
use cseries\Service\InvitationCodeManager;
use cseries\Service\SeriesManager;
use cseries\Service\UserManager;
use cseries\Service\WatchManager;
use cseries\Utils\UtilTime;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class WebsiteControllerProvider implements ControllerProviderInterface {
    public function connect(Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->match('/', function (Request $request) use ($app) {
            return $app->redirect('/home');
        });
        $controllers->match('/home', function (Request $request) use ($app) {
            if (null === $user = $app['session']->get('cseries_user')) {
                return $app->redirect('/login');
            }

            $userManager = new UserManager($app);
            $userGet = $userManager->get($user);
            $userGet['gravatar'] = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($userGet['email'])));
            $utilTime = new UtilTime();
            $register_date = $utilTime::timestampToString($userGet['date']);
            $userGet['date'] = $register_date;

            $token = new \Tmdb\ApiToken('1f5a707eebaff5c4a425834d7b2c144f');
            $client = new \Tmdb\Client($token, ['cache' => ['path' => '/tmp/php-tmdb']]);

            $watchManager = new WatchManager($app);
            $watchedSeries = $watchManager->getWatchedSeries($user);
            $watched_nothing = false;
            if(count($watchedSeries) < 1) $watched_nothing = true;
            $counter0 = 0;
            foreach ($watchedSeries as $watch){
                $api0 = $client->getTvApi()->getTvshow($watch['serie_id'], array('language' => 'fr'));
                $watchedSeries[$counter0]['id'] = $watch['serie_id'];
                $watchedSeries[$counter0]['title'] = $api0['name'];
                $watchedSeries[$counter0]['poster'] = 'https://image.tmdb.org/t/p/w300_and_h450_bestv2/'.$api0['poster_path'];
                $watchedSeries[$counter0]['background'] = 'https://image.tmdb.org/t/p/w300_and_h450_bestv2/'.$api0['backdrop_path'];
                $watchedSeries[$counter0]['time'] = date("i:s", $watch['time']);
                $counter0++;
            }

            $serieManager = new SeriesManager($app);
            $series = $serieManager->getAll();
            $counter = 0;
            foreach ($series as $serie) {
                $api = $client->getTvApi()->getTvshow($serie['tmdb_id'], array('language' => 'fr'));
                $series[$counter]['id'] = $api['id'];
                $series[$counter]['title'] = $api['name'];
                $series[$counter]['rating'] = intval($api['vote_average'] / 2);
                $series[$counter]['rating_loss'] = 5 - intval($api['vote_average'] / 2);
                $series[$counter]['date'] = $api['first_air_date'];
                $series[$counter]['description'] = substr($api['overview'], 0, 200) . '...';
                $series[$counter]['description_full'] = $api['overview'];
                $series[$counter]['cover'] = $api['poster_path'];
                $counter++;
            }

            $filmsManager = new FilmsManager($app);
            $films = $filmsManager->getAll();
            $counter = 0;
            foreach ($films as $film) {
                $api2 = $client->getMoviesApi()->getMovie($film['tmdb_id'], array('language' => 'fr'));
                $films[$counter]['title'] = $api2['title'];
                $films[$counter]['rating'] = intval($api2['vote_average'] / 2);
                $films[$counter]['rating_loss'] = 5 - intval($api2['vote_average'] / 2);
                $films[$counter]['date'] = $api2['release_date'];
                $films[$counter]['description'] = substr($api2['overview'], 0, 200) . '...';
                $films[$counter]['description_full'] = $api2['overview'];
                $films[$counter]['cover'] = $api2['poster_path'];
                $counter++;
            }

            return $app['twig']->render('home.twig', array('title' => 'Regardez vos séries/films facilement', 'watched' => $watchedSeries, 'nothing_watched' => $watched_nothing, 'user' => $userGet, 'series' => $series, 'films' => $films));
        });

        ######## User #########

        $controllers->match('/login', function (Request $request) use ($app) {
            if (null !== $user = $app['session']->get('cseries_user')) {
                return $app->redirect('/home');
            }

            $form = $app['form.factory']->createBuilder(FormType::class, array('username' => '', 'password' => '',))->add('username', TextType::class, array('label' => false, 'attr' => array('placeholder' => 'Nom d\'utilisateur')))->add('password', PasswordType::class, array('label' => false, 'attr' => array('placeholder' => 'Mot de passe'), 'auto_initialize' => false))->getForm();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $userManager = new UserManager($app);
                $user = $userManager->getByUsername($data['username']);
                if (isset($user['password'])) {
                    if ($user['password'] == sha1(md5($data['password']))) {
                        $app['session']->set('cseries_user', $user['ID']);
                        $app['session']->set('cseries_user_admin', ($data['admin'] == 1 || $data['admin'] == '1'?true:false));
                        return $app->redirect('/home');
                    } else {
                        return $app['twig']->render('login.twig', array('title' => 'Connexion', 'form' => $form->createView(), 'error' => 'Votre mot de passe est incorrect !'));
                    }
                } else {
                    return $app['twig']->render('login.twig', array('title' => 'Connexion', 'form' => $form->createView(), 'error' => 'Cette utilisateur n\'existe pas !'));
                }
            }

            return $app['twig']->render('login.twig', array('title' => 'Connexion', 'form' => $form->createView()));
        });

        $controllers->match('/register', function (Request $request) use ($app) {
            if (null !== $user = $app['session']->get('cseries_user')) {
                return $app->redirect('/login');
            }

            $form = $app['form.factory']->createBuilder(FormType::class, array('full_name' => '', 'email' => '', 'username' => '', 'password' => '', 'invitation_code' => ''))->add('full_name', TextType::class, array('label' => false, 'attr' => array('placeholder' => 'Nom complet')))->add('email', EmailType::class, array('label' => false, 'attr' => array('placeholder' => 'Adresse mail')))->add('username', TextType::class, array('label' => false, 'attr' => array('placeholder' => 'Nom d\'utilisateur')))->add('password', PasswordType::class, array('label' => false, 'attr' => array('placeholder' => 'Mot de passe')))->add('invitation_code', TextType::class, array('label' => false, 'attr' => array('placeholder' => 'Code d\'invitation')))->getForm();

            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $userManager = new UserManager($app);
                $invitationCodeManager = new InvitationCodeManager($app);

                if ($userManager->checkRegister($data['email'], $data['username'])) {
                    if ($invitationCodeManager->checkExist($data['invitation_code'])) {
                        if (!$invitationCodeManager->checkUsed($data['invitation_code'])) {
                            $userManager->create($data['full_name'], $data['email'], $data['username'], $data['password'], $data['invitation_code']);
                            return $app['twig']->render('register.twig', array('title' => 'Inscription', 'success' => 'Vous êtes désormais inscrit, félicitation ! Connectez-vous maintenant !'));
                        } else {
                            return $app['twig']->render('register.twig', array('title' => 'Inscription', 'form' => $form->createView(), 'error' => 'Ce code d\'inscription est déjà utilisé !'));
                        }
                    } else {
                        return $app['twig']->render('register.twig', array('title' => 'Inscription', 'form' => $form->createView(), 'error' => 'Ce code d\'inscription n\'existe pas !'));
                    }
                } else {
                    return $app['twig']->render('register.twig', array('title' => 'Inscription', 'form' => $form->createView(), 'error' => 'Ce nom d\'utilisateur ou cette adresse mail est déjà utilisé !'));
                }
            }

            return $app['twig']->render('register.twig', array('title' => 'Connexion', 'form' => $form->createView()));
        });

        $controllers->match('/logout', function (Request $request) use ($app) {
            $app['session']->remove('cseries_user');
            $app['session']->remove('cseries_user_admin');
            return $app->redirect('/login');
        });

        ######## Série ########

        $controllers->match('/serie/{id}', function (Request $request, $id) use ($app) {
            if (null === $user = $app['session']->get('cseries_user')) {
                return $app->redirect('/login');
            }

            $token = new \Tmdb\ApiToken('1f5a707eebaff5c4a425834d7b2c144f');
            $client = new \Tmdb\Client($token, ['cache' => ['path' => '/tmp/php-tmdb']]);

            $serieManager = new SeriesManager($app);
            $serie = $serieManager->get($id);
            $api = $client->getTvApi()->getTvshow($serie['tmdb_id'], array('language' => 'fr'));
            $serie['id'] = $api['id'];
            $serie['title'] = $api['name'];
            $serie['rating'] = intval($api['vote_average'])/2;
            $serie['date'] = explode('-', $api['first_air_date'])[0];
            $serie['description'] = $api['overview'];
            $serie['cover'] = $api['poster_path'];
            $serie['background'] = $api['backdrop_path'];

            $saisons = array('saisons' => array());
            $counter = 0;
            foreach ($api['seasons'] as $saison){
                if($counter == 0){
                    $counter++;
                }else{
                    $episodes = $client->getTvSeasonApi()->getSeason($serie['tmdb_id'], $counter, array('language' => 'fr'));
                    $saisons['saisons'][$counter] = $episodes;
                    $counter++;
                }
            }

            return $app['twig']->render('serie.twig', array('title' => 'Regardez vos séries/films facilement', 'serie' => $serie, 'saisons' => $saisons));
        });

        $controllers->match('/serie/watch/{serie}/{saison}/{episode}', function (Request $request, $serie, $saison, $episode) use ($app) {
            if (null === $user = $app['session']->get('cseries_user')) {
                return $app->redirect('/login');
            }

            $token = new \Tmdb\ApiToken('1f5a707eebaff5c4a425834d7b2c144f');
            $client = new \Tmdb\Client($token, ['cache' => ['path' => '/tmp/php-tmdb']]);

            $watchManager = new WatchManager($app);
            $watch = $watchManager->getForUserAndSerie($user, $serie);
            $time = 0;
            $watchManager->createOrUpdate($user, $serie, $saison, $episode, $time);

            $se = $client->getTvApi()->getTvshow($serie, array('language' => 'fr'));
            $api = $client->getTvEpisodeApi()->getEpisode($serie, $saison, $episode, array('language' => 'fr'));

            return $app['twig']->render('watch-serie.twig', array('title' => 'Regardez vos séries/films facilement', 'serie' => $se, 'episode' => $api, 'episode_link' => $app['url'].'series/'.$serie.'/Saison_'.$saison.'/Episode_'.($episode < 10?'0'.$episode:$episode).'.mp4', 'time' => $time));
        });

        ######## Films ########

        return $controllers;
    }
}
