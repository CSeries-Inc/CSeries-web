<?php

namespace cseries\Utils\T411\Config;

/**
 * Class TokenConfig
 * @package Rosello\T411\Config
 */
class TokenConfig extends BaseConfig
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}