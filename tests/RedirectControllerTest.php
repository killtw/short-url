<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RedirectControllerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_should_create_a_new_record()
    {
        $this->json('POST', 'store', ['href' => 'http://test.com'])
            ->seeJson([
                'href' => 'http://test.com',
            ]);
    }

    /** @test */
    public function it_should_create_a_new_record_with_custom_hash()
    {
        $this->json('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
        ])
            ->seeJson([
                'href' => 'http://test.com',
                'hash' => 'test',
            ]);
    }

    /** @test */
    public function it_should_fail_when_hash_exists()
    {
        $this->call('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
        ]);

        $response = $this->json('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
        ])
            ->seeStatusCode(422);
    }

    /** @test */
    public function it_should_a_valid_json()
    {
        $this->call('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
        ]);

        $this->get('test+')
            ->seeJson([
                'href' => 'http://test.com',
                'hash' => 'test',
            ]);
    }
}
