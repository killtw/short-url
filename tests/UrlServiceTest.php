<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UrlServiceTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_should_create_a_new_record()
    {
        // arrange
        $target = app(\App\Services\UrlService::class);
        $request = new \Illuminate\Http\Request([
            'href' => 'http://test.com',
        ]);

        // act
        $actual = $target->make($request);

        // assert
        $this->seeInDatabase('urls', ['href' => 'http://test.com']);
        $this->assertEquals('http://test.com', $actual->href);
    }

    /** @test */
    public function it_should_create_a_new_record_with_custom_hash()
    {
        // arrange
        $target = app(\App\Services\UrlService::class);
        $request = new \Illuminate\Http\Request([
            'href' => 'http://test.com',
            'hash' => 'test',
        ]);

        // act
        $actual = $target->make($request);

        // assert
        $this->seeInDatabase('urls', ['href' => 'http://test.com']);
        $this->assertEquals('http://test.com', $actual->href);
        $this->seeInDatabase('urls', ['hash' => 'test']);
        $this->assertEquals('test', $actual->hash);
    }

    /** @test */
    public function it_should_return_a_valid_model()
    {
        // arrange
        $target = app(\App\Services\UrlService::class);
        $target->make(new \Illuminate\Http\Request([
            'href' => 'http://test.com',
        ]));
        $target->make(new \Illuminate\Http\Request([
            'href' => 'http://test.com',
            'hash' => 'test',
        ]));
        $expected = \App\Url::where('hash', 'test')->first()->toArray();

        // act
        $actual = $target->find('test')->toArray();

        // assert
        $this->assertEquals($expected, $actual);
    }
}
