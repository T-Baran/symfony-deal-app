<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\CommentFactory;
use App\Factory\DealFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['email'=>'superadmin@test.test','username'=>'superadmin','plainPassword'=>'testsuperadmin','roles'=>['ROLE_SUPER_ADMIN']]);
        UserFactory::createOne(['email'=>'admin@test.test','username'=>'admin','plainPassword'=>'testadmin','roles'=>['ROLE_ADMIN']]);
        UserFactory::createOne(['email'=>'mod@test.test','username'=>'mod','plainPassword'=>'testmod','roles'=>['ROLE_EDITOR']]);
        UserFactory::createOne(['email'=>'user@test.test','username'=>'user','plainPassword'=>'testuser','roles'=>['ROLE_USER']]);
        UserFactory::createMany(10);
        DealFactory::createMany(20,
            function(){
                return['user'=>UserFactory::random()];
        });
        CommentFactory::createMany(100,
            function(){
                return['user'=>UserFactory::random(),'deal'=>DealFactory::random()];
            }
        );



        $manager->flush();
    }
}
