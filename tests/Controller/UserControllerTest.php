<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testListUsers()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'User List');
    }

    public function testUserDetail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'User Detail');
    }

    public function testBorrowBook()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/users/1/borrow', [
            'book_id' => 1
        ]);

        $this->assertResponseRedirects('/users/1');
    }

    public function testReturnBook()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/user/1/return', [
            'book_id' => 1
        ]);

        $this->assertResponseRedirects('/users/1');
    }
}