<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserDeserialize
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher,
                                private readonly CampusRepository            $campusRepository)
    {

    }

    public function deserialize(FormInterface $form): array
    {
        $data = $form->get('importer')->getData();

        $encoders = [new CsvEncoder()];
        $normalizer = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizer, $encoders);

        $decodedData = $serializer->decode(file_get_contents($data), 'csv', [
            CsvEncoder::DELIMITER_KEY => ';'
        ]);

        $users = [];

        foreach ($decodedData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setUsername($userData['username']);
            $user->setLastName($userData['lastName']);
            $user->setFirstName($userData['firstName']);
            $user->setPhoneNumber($userData['phoneNumber']);
            $user->setRoles([$userData['roles']]);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setActive($userData['active'] == '1');
            $user->setCampus($this->campusRepository->find($userData['campus']));

            $users[] = $user;
        }
        return $users;
    }


}