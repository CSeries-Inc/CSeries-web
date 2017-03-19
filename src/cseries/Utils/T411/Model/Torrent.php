<?php

namespace cseries\Utils\T411\Model;

/**
 * Class Torrent
 * @package Rosello\T411\Model
 */
class Torrent
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $seeders;

    /**
     * @var int
     */
    private $leechers;

    /**
     * @var integer
     */
    private $category;

    /**
     * @var \DateTime
     */
    private $addedAt;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var integer
     */
    private $isVerified;

    /**
     * @param $id
     * @param $name
     * @param $category
     * @param $seeders
     * @param $leechers
     * @param $addedAt
     * @param $size
     * @param $isVerified
     */
    public function __construct($id, $name, $category, $seeders, $leechers, $addedAt, $size, $isVerified)
    {
        $this->id = $id;
        $this->name = $name;
        $this->seeders = $seeders;
        $this->leechers = $leechers;
        $this->category = $category;
        $this->addedAt = new \DateTime($addedAt);
        $this->size = $size;
        $this->isVerified = $isVerified;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSeeders()
    {
        return $this->seeders;
    }

    /**
     * @return int
     */
    public function getLeechers()
    {
        return $this->leechers;
    }

    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return \DateTime
     */
    public function getAddedAt()
    {
        return $this->addedAt;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return $this->isVerified == 1;
    }
}

