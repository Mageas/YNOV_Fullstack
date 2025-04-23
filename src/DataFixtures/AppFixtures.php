<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Song;
use App\Enums\Status;
use App\Faker\Provider\Goat;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
        $this->faker->addProvider(new Goat($this->faker));
    }

    public function load(ObjectManager $manager): void
    {
        foreach(range(1, 100) as $i) {
            $song = new Song();
            $song->setName($this->faker->goatName());
            $song->setArtiste("Muse");
            $song->setStatus(Status::Active->value);

            $manager->persist($song);
        }

        $manager->flush();
    }
}
