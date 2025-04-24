<?php

namespace App\Controller\Api\V3;

use App\Controller\Api\V2\SongController as V2SongController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/v3/song', name: 'api_v3_song_')]
class SongController extends V2SongController
{
    protected const VERSION = 'v3';
}
