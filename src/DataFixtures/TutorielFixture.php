<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Style;
use App\Entity\Tutoriel;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TutorielFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $style = $manager->getRepository(Style::class)->findAll();
        $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();
        $cate = $manager->getRepository(Category::class)->findAll();
        $cat = count($cate);
        $sty = count($style);
        for($i = 1 ; $i <= 25; $i++){
            $videoId = $faker->regexify('[A-Za-z0-9_-]{11}');
            $videoLink = 'https://www.youtube.com/embed/' . $videoId;
            $tuto = new Tutoriel();
            $title = $faker->words(3,true);
            $tuto->setTitle($title)
                ->setDescription($faker->paragraphs(2, true))
                ->setImage($i.'.jpg')
                ->setCategory($cate[$faker->numberBetween(0, $cat - 1)])
                ->setStyleId($style[$faker->numberBetween(0, $sty - 1)])
                ->setSlug($slugify->slugify($title))
                ->setContent($videoLink);
            $manager->persist($tuto);

        }
        $manager->flush();

    }
}
