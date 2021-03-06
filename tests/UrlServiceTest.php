<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Spatie\Analytics\Period;

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

    /** @test */
    public function it_should_create_a_new_record_with_utm_meta()
    {
        // arrange
        $target = app(\App\Services\UrlService::class);
        $request = new \Illuminate\Http\Request([
            'href' => 'http://test.com',
            'hash' => 'test',
            'utm' => [
                'source' => 'facebook',
                'medium' => 'ads',
                'campaign' => 'posts',
            ]
        ]);

        // act
        $actual = $target->make($request);

        // assert
        $this->seeInDatabase('urls', ['href' => 'http://test.com']);
        $this->assertEquals('http://test.com', $actual->href);
        $this->seeInDatabase('urls', ['hash' => 'test']);
        $this->assertEquals('test', $actual->hash);
        $this->assertEquals(json_decode('{"utm_source":"facebook","utm_medium":"ads","utm_campaign":"posts"}'), $actual->utm);
    }

    /** @test */
    public function it_should_return_a_record_with_ga_data()
    {
        // arrange
        $mock = $this->initMockClass(\App\Services\AnalyticService::class);
        $mock->shouldReceive('getPageviews')
            ->once()
            ->withArgs(['test'])
            ->once()
            ->andReturn(collect([
                'pageviews' => 64
            ]));

        $target = app(\App\Services\UrlService::class);
        $target->make(new \Illuminate\Http\Request([
            'href' => 'http://test.com',
            'hash' => 'test',
        ]));

        // act
        $actual = $target->decode('test');

        // assert
        $this->seeJsonStructure([
            'id',
            'hash',
            'redirect',
            'ga' => [
                'pageviews',
            ],
        ], $actual);
    }
}
