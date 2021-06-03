<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($j=0 ; $j < 5 ;$j++){
        $program = new Program();
        $program->setTitle('Walking dead'. $j);
        $program->setSummary('Des zombies envahissent la terre'.$j);
        $program->setCountry('USA');
        $program->setYear(2010);
        $program->setPoster('https://picsum.photos/seed/picsum/200/300');
        $program->setCategory($this->getReference('category_0'));
        $this->addReference('program_'.$j, $program);
        //ici les acteurs sont insérés via une boucle pour être DRY mais ce n'est pas obligatoire
        for ($i=0; $i < count(ActorFixtures::ACTORS); $i++) {
            $program->addActor($this->getReference('actor_' . $i));
        }
        $manager->persist($program);
        $manager->flush();
    }
}

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            ActorFixtures::class,
            CategoryFixtures::class,
          ];
    }
}
