<?php

namespace App\DataFixtures;

use App\Entity\Painting;
use App\Entity\Style;
use Cocur\Slugify\Slugify;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaintFixture extends Fixture implements DependentFixtureInterface
{

    private array $height = [114, 27, 81, 89, 50, 54, 97, 65];
    private array $width = [195, 27, 81, 92, 50, 73, 100, 146];

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();
        $style = $manager->getRepository(Style::class)->findAll();
        $sty = count($style);
        $nbHeight = count($this->height);
        $nbWidth = count($this->width);
        for ($i = 1; $i <= 26; $i++) {
            $paint = new Painting();
            $name = $faker->words(3, true);
            $paint->setTitle($name)
                ->setDescrioption($faker->paragraphs(2, true))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setHeight($this->height[$faker->numberBetween(0, $nbHeight - 1)])
                ->setWidth($this->width[$faker->numberBetween(0, $nbWidth - 1)])
                ->setImage($i . '.jpg')
                ->setSlug($slugify->slugify($name))
                ->setStyle($style[$faker->numberBetween(0, $sty - 1)]);
            $manager->persist($paint);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [StyleFixture::class,
        ];
    }
}
