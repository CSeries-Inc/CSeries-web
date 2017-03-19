<?php

use Silex\Application;
use Silex\Provider;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();
$app['debug'] = true;
$app['url'] = 'https://cseries.tk/';
$app['cdn_url'] = 'https://cdn.cseries.tk/';

$app->register(new Silex\Provider\SessionServiceProvider());
// Mot de passe caché
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__ . '/views'));
$app->extend('twig', function ($twig, $app) {
    $twig->addGlobal('url', 'https://cseries.tk/');
    $admin = $app['session']->get('cseries_user');
    $twig->addGlobal('isAdmin', $admin);
    return $twig;
});
$app->register(new Silex\Provider\AssetServiceProvider(), array('assets.version' => md5(time()), 'assets.version_format' => '%s?v=%s', 'assets.named_packages' => array('css' => array('version' => 'css2', 'base_path' => __DIR__ . '/../assets/css/'), 'img' => array('base_path' => __DIR__ . '/../assets/img/'), 'img' => array('base_path' => __DIR__ . '/../assets/img/'))));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array('translator.messages' => array()));

$app->register(new cseries\Provider\WebsiteProvider);

$app->run();
