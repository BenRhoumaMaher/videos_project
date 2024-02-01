<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllersCategoriesTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    public function testTextOnPage(): void
    {
        $this->client->request('GET', '/admin/categories');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Categories list');
        $this->assertSelectorTextContains('body', 'Add new category');
    }
}
