<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Utility\Time\TimeConverter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AppUserFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasherInterface)
    {
        $this->passwordHasher = $passwordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@yflix.com');

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'user');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);

        $managerUser = new User();
        $managerUser->setEmail('manager@yflix.com');
        $hashedPassword = $this->passwordHasher->hashPassword($managerUser, 'manager');
        $managerUser->setPassword($hashedPassword);
        $managerUser->setRoles(['ROLE_MANAGER']);

        $manager->persist($managerUser);

        $adminUser = new User();
        $adminUser->setEmail('admin@yflix.com');
        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, 'admin');
        $adminUser->setPassword($hashedPassword);
        $adminUser->setRoles(['ROLE_ADMIN']);

        $manager->persist($adminUser);

        $manager->flush();
    }
}
