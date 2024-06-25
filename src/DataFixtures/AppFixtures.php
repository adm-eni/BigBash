<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private readonly Generator $faker;

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->addCampus(1, $manager);
        $this->addUsers(1, $manager);

    }

    public function addCampus(int $number, ObjectManager $manager): void {
        $campus = new Campus();
        $campus->setName("Rennes");
        $manager->persist($campus);
        $manager->flush();
    }
    public function addUsers(int $number, ObjectManager $manager): void {
        $campus = $manager->getRepository(Campus::class)->findAll();
        for ($i = 0; $i < $number; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
            $user->setLastName($this->faker->lastName());
            $user->setFirstName($this->faker->firstName());
            $user->setPhoneNumber($this->faker->phoneNumber());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $user->setActive(true);
            $user->setCampus($campus[array_rand($campus)]);

            $manager->persist($user);
        }

        $manager->flush();
    }


}
