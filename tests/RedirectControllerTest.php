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

        $this->json('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
        ])
            ->seeStatusCode(422);
    }

    /** @test */
    public function it_should_create_a_record_with_utm_meta()
    {
        $this->json('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
            'utm' => [
                'source' => 'facebook',
                'medium' => 'ads',
                'campaign' => 'posts',
            ],
        ])
            ->seeJson([
                'href' => 'http://test.com',
                'hash' => 'test',
                'utm' => [
                    'utm_source' => 'facebook',
                    'utm_medium' => 'ads',
                    'utm_campaign' => 'posts',
                ]
            ]);
    }

    /** @test */
    public function it_should_redirect_to_correct_url()
    {
        $this->call('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
        ]);

        $this->get('test')
            ->seeStatusCode(302)
            ->assertEquals('http://test.com', $this->response->getTargetUrl());
    }

    /** @test */
    public function it_shoould_return_a_json_with_pageview()
    {
        $this->call('POST', 'store', [
            'href' => 'http://test.com',
            'hash' => 'test',
        ]);

        $this->initMockClass(\App\Services\AnalyticService::class)
            ->shouldReceive('getPageviews')
            ->once()
            ->withArgs(['test'])
            ->once()
            ->andReturn(collect([
                'pageviews' => 64
            ]));

        $this->get('test+')
            ->seeJsonStructure([
                'id',
                'hash',
                'redirect',
                'ga' => [
                    'pageviews',
                ],
            ])
            ->seeJson([
                'id' => 1,
                'hash' => 'test',
                'redirect' => 'http://test.com',
                'ga' => [
                    'pageviews' => 64
                ],
            ]);
    }
}
