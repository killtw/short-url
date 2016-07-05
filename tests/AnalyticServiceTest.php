<?php

class AnalyticServiceTest extends TestCase
{
    /** @test */
    public function it_should_return_pageview()
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

        $target = app(\App\Services\AnalyticService::class);
        $hash = 'test';
        $expected = 64;

        // act
        $actual = $target->getPageViews($hash)['pageviews'];

        // assert
        $this->assertEquals($expected, $actual);
    }
}
