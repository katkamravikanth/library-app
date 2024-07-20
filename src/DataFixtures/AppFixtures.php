<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user1 = new User();
        $user1->setName('Alice');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('Bob');
        $manager->persist($user2);

        $book1 = new Book();
        $book1->setTitle('1984');
        $book1->setAuthor('George Orwell');
        $book1->setStatus(1);
        $manager->persist($book1);

        $book2 = new Book();
        $book2->setTitle('Brave New World');
        $book2->setAuthor('Aldous Huxley');
        $book2->setStatus(1);
        $manager->persist($book2);

        $book3 = new Book();
        $book3->setTitle(' The Pilgrim’s Progress');
        $book3->setAuthor('John Bunyan');
        $book3->setStatus(1);
        $manager->persist($book3);

        $book4 = new Book();
        $book4->setTitle('Robinson Crusoe');
        $book4->setAuthor('Daniel Defoe');
        $book4->setStatus(1);
        $manager->persist($book4);

        $book5 = new Book();
        $book5->setTitle(' Gulliver’s Travels');
        $book5->setAuthor('Jonathan Swift');
        $book5->setStatus(1);
        $manager->persist($book5);

        $book6 = new Book();
        $book6->setTitle('Clarissa');
        $book6->setAuthor('Samuel Richardson');
        $book6->setStatus(1);
        $manager->persist($book6);

        $borrowing = new Borrowing($user1, $book1);
        $book1->setStatus(0);
        $manager->persist($borrowing);

        $manager->flush();
    }
}
