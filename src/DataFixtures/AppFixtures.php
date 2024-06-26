<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\Status;
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
    $this->addCampus(5, $manager);
    $this->addUsers(3, $manager);
    $this->addCities(8, $manager);
    $this->addLocations(15, $manager);
    $this->addStatuses($manager);
    $this->addOutings(50, $manager);
  }

  public function addCampus(int $number, ObjectManager $manager): void
  {
    for ($i = 0; $i < $number; $i++) {
      $campus = new Campus();
      $campus->setName($this->faker->city());
      $manager->persist($campus);
    }
    $manager->flush();
  }

  public function addUsers(int $number, ObjectManager $manager): void
  {
    $campus = $manager->getRepository(Campus::class)->findAll();
    for ($i = 0; $i < $number; $i++) {
      $user = new User();
      $user->setEmail($this->faker->email());
      $user->setPseudo($this->faker->userName());
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

  public function addCities(int $number, ObjectManager $manager)
  {
    for ($i = 0; $i < $number; $i++) {
      $city = new City();
      $city->setName($this->faker->city());
      $city->setPostCode($this->faker->postcode());
      $manager->persist($city);
    }
    $manager->flush();
  }

  public function addLocations(int $number, ObjectManager $manager)
  {
    $cities = $manager->getRepository(City::class)->findAll();
    for ($i = 0; $i < $number; $i++) {
      $location = new Location();
      $location->setName($this->faker->company());

      if (rand(0, 1) === 0) {
        $location->setStreet($this->faker->streetAddress());
        $location->setCity($cities[array_rand($cities)]);
      } else {
        $location->setLatitude($this->faker->latitude());
        $location->setLongitude($this->faker->longitude());
      }

      $manager->persist($location);
    }
    $manager->flush();
  }

  public function addStatuses(ObjectManager $manager)
  {
    $statusNames = [
        'Créée',
        'Ouverte',
        'Clôturée',
        'Activité en cours',
        'Passée',
        'Annulée'
    ];

    foreach ($statusNames as $name) {
      $status = new Status();
      $status->setName($name);
      $manager->persist($status);
    }
    $manager->flush();
  }

  public function addOutings(int $number, ObjectManager $manager): void
  {
    $campus = $manager->getRepository(Campus::class)->findAll();
    $users = $manager->getRepository(User::class)->findAll();
    $locations = $manager->getRepository(Location::class)->findAll();
    $statuses = $manager->getRepository(Status::class)->findAll();
    for ($i = 0; $i < $number; $i++) {
      $outing = new Outing();
      $outing->setTitle($this->faker->sentence(3));
      $outing->setStartAt($this->faker->dateTimeBetween('now', '+1 month'));
      $outing->setDuration($this->faker->numberBetween(30, 240));
      $outing->setEntryDeadline($this->faker->dateTimeBetween('now', '+1 month'));
      $outing->setMaxEntryCount($this->faker->numberBetween(5, 20));
      $outing->setDescription($this->faker->sentence(10));
      $outing->setLocation($locations[array_rand($locations)]);
      $outing->setStatus($statuses[array_rand($statuses)]);
      $outing->setCampus($campus[array_rand($campus)]);
      $outing->setHost($users[array_rand($users)]);

      $manager->persist($outing);
    }
    $manager->flush();
  }

}
