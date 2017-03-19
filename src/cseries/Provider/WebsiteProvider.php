<?php

namespace cseries\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

use cseries\Controller;

class WebsiteProvider implements ServiceProviderInterface{

    public function register(Container $app) {
        $app->mount('/', new Controller\WebsiteControllerProvider());
    }

}
