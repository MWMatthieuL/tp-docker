<?php

namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use SmartUserBundle\Manager\UserManager;

final class UserFixtures extends Fixture
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function load(ObjectManager $manager): void
    {
        // Super Admin
        $this->create('superadmin@matop.fr', 'superadmin', ['ROLE_SUPER_ADMIN', 'ROLE_ALLOWED_TO_SWITCH']);

        // Admin
        $this->create('admin@matop.fr', 'admin', ['ROLE_SUPER_ADMIN', 'ROLE_ALLOWED_TO_SWITCH']);

        // Users
        for ($i = 1; $i < 10; ++$i) {
            $this->create('user'.$i.'@matop.fr', $i);
        }
    }

    private function create(string $email, int|string $key, array $roles = []): void
    {
        $user = new User();
        $user
            ->setUsername($email)
            ->setEmail($email)
            ->setPlainPassword('xxx')
            ->setRoles($roles)
            ->setEnabled(true);

        $this->userManager->updateUser($user);
        $this->addReference('user-'.$key, $user);
    }
}
