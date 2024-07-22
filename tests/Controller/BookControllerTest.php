<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testListBooks()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/books');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Book List');
    }

    public function testBookDetail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/books/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Book Detail');
    }

    public function testBorrowBook()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/books/1/borrow', [
            'user_id' => 1
        ]);

        $this->assertResponseRedirects('/books/1');
    }

    public function testReturnBook()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/books/1/return');

        $this->assertResponseRedirects('/books/1');
    }
}