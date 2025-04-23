<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Pool;
use App\Entity\Song;
use Faker\Generator;
use App\Enums\Status;
use App\Faker\Provider\Goat;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

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
        $pools = [];
        foreach(range(1, 100) as $i) {
            $pool = new Pool();
            $pool->setName($this->faker->goatName());
            $pool->setShortName("Rock");
            $pool->setStatus(Status::Active->value);

            $manager->persist($pool);
            $pools[] = $pool;
        }

        foreach(range(1, 100) as $i) {
            $song = new Song();
            $song->setName($this->faker->goatName());
            $song->setArtiste("Muse");
            $song->setStatus(Status::Active->value);
            $song->addPool($pools[array_rand($pools)]);

            $manager->persist($song);
        }

        $manager->flush();
    }
}
