<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Service\Slugify;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }
    
    public function load(ObjectManager $manager)
    {
        $count =0;
        for ($i=0 ; $i < 25 ;$i++){
            $count += 1;
            for ($o=0 ; $o < 5 ;$o++){
                
            $title = 'episode';
            $episode = new episode();
            $episode->setNumber(0 + $o);
            $episode->setTitle($title . $o);
            $slug = $this->slugify->generate($title. $o);
            $episode->setSlug($slug);
            $episode->setSynopsis('vraiment fou cet épisode' . $o);
            $episode->setSeason($this->getReference('season_' . $count));
            $manager->persist($episode);
            }
            $manager->flush();
        }

    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont EpisodeFixtures dépend
        return [
            SeasonFixtures::class,
          ];
    }
}
