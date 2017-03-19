<?php

namespace cseries\Utils\T411\Config;

/**
 * Class BaseConfig
 */
abstract class BaseConfig
{
    /**
     * @var string
     */
    private $baseUrl = "https://api.t411.ch";

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}