<?php

class AnalyticServiceTest extends TestCase
{
    /** @test */
    public function it_should_return_a_ga_data()
    {
        // arrange
        $mock = Mockery::mock(\App\Services\AnalyticService::class);
        $this->app->instance(\App\Services\AnalyticService::class, $mock);
        $mock->shouldReceive('getPageviews')
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
        $actual = $target->getPageViews($hash)['pageviews'];

        // assert
        $this->assertEquals($expected, $actual);
    }
}
