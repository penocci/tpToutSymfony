<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('wick@wick.us');
        $user->setNom('wick');
        $user->setPrenom('john');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'wick'));
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail('zick@wick.us');
        $user2->setNom('zick');
        $user2->setPrenom('zohn');
        $user2->setRoles(['ROLE_STUDENT']);
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, 'zick'));
        $manager->persist($user2);
        
        $manager->flush();
    }
}

