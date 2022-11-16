<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use SmartUserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: 'username', groups: ['Registration'])]
#[UniqueEntity(fields: 'email', groups: ['Registration'])]
class User extends BaseUser
{
    #[ORM\Id]
    #[ORM\Column(type : 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    #[Assert\NotBlank(message: 'smart_user.username.blank', groups: ['Registration'])]
    #[Assert\Length(
        min: 2, max: 180, minMessage: 'smart_user.username.short', maxMessage: 'smart_user.username.long',
        groups: ['Registration'])
    ]
    protected $username;

    #[Assert\NotBlank(message: 'smart_user.email.blank', groups: ['Registration'])]
    #[Assert\Length(
        min: 2, max: 180, minMessage: 'smart_user.email.short', maxMessage: 'smart_user.email.long',
        groups: ['Registration'])
    ]
    #[Assert\Email(message: 'smart_user.email.invalid', groups: ['Registration'])]
    protected $email;

    #[Assert\NotBlank(message: 'smart_user.password.blank', groups: ['Registration', 'Reseting'])]
    protected $plainPassword;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PasswordHistory::class, cascade: ['persist', 'remove'])]
    protected $passwordHistories;

    public function __construct()
    {
        parent::__construct();
    }
}
