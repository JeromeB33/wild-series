<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $count = 0;
        for ($i=0 ; $i < 5 ;$i++){
            for ($o=0 ; $o < 5 ; $o ++){
            $count += 1;
            $season = new Season();
            $season->setNumber(0 + $o);
            $season->setYear(2020 + $o);
            $season->setDescription('Petites description sans prétention' . $o);
            $season->setProgram($this->getReference('program_'.$i));
            $this->addReference('season_'.$count, $season);
            $manager->persist($season);
            }
            $manager->flush();
        }

    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
        return [
            ProgramFixtures::class,
          ];
    }
}
