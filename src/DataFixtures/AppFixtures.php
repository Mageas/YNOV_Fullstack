<?php

namespace App\DataFixtures;

use App\Entity\Song;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $song = new Song();
        $song->setName("Fury");
        $song->setArtiste("Muse");

        $manager->persist($song);

        $manager->flush();
    }
}
