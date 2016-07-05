<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AnalyticServiceTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_should_reutrn_pageview()
    {
        // arrange
        $this->initMockClass(\App\Services\AnalyticService::class)
            ->shouldReceive('getPageviews')
            ->once()
            ->withArgs(['test'])
            ->once()
            ->andReturn(collect([
                'pageviews' => 64
            ]));

        $target = app(\App\Services\AnalyticService::class);
        $hash = 'test';
        $expected = 64;

        // act
        $actual = $target->getPageviews($hash)['pageviews'];

        // assert
        $this->assertEquals($expected, $actual);
    }
}
