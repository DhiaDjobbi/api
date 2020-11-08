<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * encoder for passwords
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker=Factory::create("en_US");

        for ($j=0; $j < 10; $j++) { 
            $user= new User();
            $chrono=1;
            $hash=$this->encoder->encodePassword($user,"000");
            $user->setFullName($faker->firstName()." ".$faker->lastName())
                 ->setEmail($faker->email)
                 ->setPassword($hash)
                 ->setRoles(['ROLE_USER']);
            $manager->persist($user);

            for ($c=0; $c < mt_rand(5,20); $c++) { 
                $customer = new Customer();
                $customer->setFullName($faker->firstName()." ".$faker->lastName())
                ->setCompany($faker->company)
                ->setEmail($faker->email)
                ->setUser($user);
                $manager->persist($customer);

                for ($i=0; $i <  mt_rand(3,10); $i++) { 
                    $invoice=new Invoice();
                    $invoice->setAmount($faker->randomFloat(2,250,5000))
                            ->setSentAt($faker->dateTimeBetween("-3 months"))
                            ->setStatus($faker->randomElement(['SENT','PAID','CANCELLED']))
                            ->setCustomer($customer)
                            ->setChrono($chrono);
                    $manager->persist($invoice);
                    $chrono++;
                }
            }

        }


        $manager->flush();
    }
}
