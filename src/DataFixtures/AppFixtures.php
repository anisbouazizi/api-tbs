<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Product;
use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $listContacts = [];
        for ($i = 0; $i < 10; ++$i) {
            // Création de l'auteur lui-même.
            $contact = new Contact();
            $contact->setName($faker->lastName);
            $contact->setFirstName($faker->firstName);
            $manager->persist($contact);

            $listContacts[] = $contact;
        }

        $listProducts = [];
        for ($i = 0; $i < 10; ++$i) {
            // Création de l'auteur lui-même.
            $product = new Product();
            $product->setLabel($faker->company);
            $manager->persist($product);

            $listProducts[] = $product;
        }


        for ($i = 0; $i < 20; ++$i) {
            $subscription = new Subscription();
            $subscription->setContact($listContacts[array_rand($listContacts)]);
            $subscription->setProduct($listProducts[array_rand($listProducts)]);
            $subscription->setBegineDate($faker->dateTimeBetween('+0 days'));
            $subscription->setEndDate($faker->dateTimeBetween('+0 days', '+2 years'));
            $manager->persist($subscription);
        }

        $manager->flush();
    }
}
