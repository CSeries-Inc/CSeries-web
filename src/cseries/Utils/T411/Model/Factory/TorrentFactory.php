<?php

namespace cseries\Utils\T411\Model\Factory;

use cseries\Utils\T411\Model\Torrent;

/**
 * Class TorrentFactory
 * @package AppBundle\Providers\T411\Model\Factory
 */
class TorrentFactory
{
    /**
     * @param array $row
     *
     * @return Torrent
     */
    public function create(array $row)
    {
        return new Torrent(
            $row['id'],
            $row['name'],
            $row['category'],
            $row['seeders'],
            $row['leechers'],
            $row['added'],
            $row['size'],
            $row['isVerified']
        );
    }
}