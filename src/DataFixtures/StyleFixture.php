<?php

namespace App\DataFixtures;

use App\Entity\Style;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



class StyleFixture extends Fixture
{
    private array $styles = ['A l\'huile', 'Aquarelle', 'Bombe', 'Acrilique', 'Pastel'];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach($this->styles as $style) {
            $sty = new Style() ;
            $sty->setName($style);
            $manager->persist($sty);
        }

        $manager->flush();
    }

}
