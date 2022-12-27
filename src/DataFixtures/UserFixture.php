<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private object $hasher;
    private array $genders = ['male', 'female'];
    private array $role = [['ROLE_USER'], ['ROLE_SELLER']];

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $slug = new Slugify(); // uniquement pour nettoyer le prefixe du mail
        $countroles = count($this->role);
        for ($i = 1; $i <= 15; $i++) {
            $user = new User();
            $gender = $faker->randomElement($this->genders);
            $user   ->setFirstName($faker->firstName($gender))
                    ->setLastName($faker->lastName)
                    ->setEmail($slug->slugify($user->getFirstName()) . '.' . $slug->slugify($user->getLastName()) . '@' . $faker->freeEmailDomain());
            $gender = $gender == 'male' ? 'm' : 'f';
            $user   ->setImageName('0' . ($i + 9) . $gender . '.jpg')
                    ->setPassword($this->hasher->hashPassword($user, 'password'))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setUpdatedAt(new \DateTimeImmutable())
                    ->setRoles($this->role[$faker->numberBetween(0, $countroles - 1)]);
            $manager->persist($user);
        }


        /**
         *  super admin Pat Mar
         */
        $user = new User();
        $user->setFirstName('Pat')
            ->setLastName('Mar')
            ->setEmail('patmar@gmail.com')
            ->setImageName('024m.jpg')
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($user);
        $manager->flush();



        /**
         *  super admin Stef Toad
         */
        $user = new User();
        $user->setFirstName('Stef')
            ->setLastName('Toad')
            ->setEmail('stef.toad@gmail.com')
            ->setImageName('067m.jpg')
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($user);
        $manager->flush();
    }
}