<?php

namespace App\DataFixtures;

use App\Entity\Snowflake;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SnowflakeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $snowflake1 = new Snowflake();

        $snowflake1->setName('Pacun')
            ->setLuckyNumber(4)
            ->setDescription('The sweetest snowflake, very needy.')
            ->setCreatedAt(new \DateTime());

        $manager->persist($snowflake1);

        $snowflake2 = new Snowflake();

        $snowflake2->setName('Tifa')
            ->setLuckyNumber(7)
            ->setDescription('The funniest snowflake, nervous and easily scared.')
            ->setCreatedAt(new \DateTime());

        $manager->persist($snowflake2);

        $snowflake3 = new Snowflake();

        $snowflake3->setName('Thelma')
            ->setLuckyNumber(11)
            ->setDescription('The original snowflake, kind of a couch potato.')
            ->setCreatedAt(new \DateTime());

        $manager->persist($snowflake3);

        $manager->flush();
    }
}
