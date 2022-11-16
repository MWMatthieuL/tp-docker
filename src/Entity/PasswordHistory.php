<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use SmartUserBundle\Model\PasswordHistory as BasePasswordHistory;

#[ORM\Entity]
#[Orm\Table(name: 'users_passwords_histories')]
class PasswordHistory extends BasePasswordHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'passwordHistories')]
    protected $user;
}
