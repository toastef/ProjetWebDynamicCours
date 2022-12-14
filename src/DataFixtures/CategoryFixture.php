<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    private array $categories = ['Pop-Art', 'Contemporain', 'Impressionnisme', 'Abstrait', 'Street-Art'];
    public function load(ObjectManager $manager): void
    {
        foreach($this->categories as $cate) {
            $cat = new Category() ;
            $cat->setName($cate);
            $manager->persist($cat);
        }

        $manager->flush();
    }

}
