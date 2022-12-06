<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['email'=>'admin@test.test','username'=>'admin','password'=>'testtest','roles'=>['ROLE_ADMIN']]);
        UserFactory::createOne(['email'=>'mod@test.test','username'=>'mod','password'=>'testtest','roles'=>['ROLE_EDITOR']]);
        UserFactory::createOne(['email'=>'user@test.test','username'=>'user','password'=>'testtest','roles'=>['ROLE_USER']]);
        UserFactory::createMany(10);


        $manager->flush();
    }
}
