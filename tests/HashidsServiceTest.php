<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class HashidsServiceTest extends TestCase
{
    /** @test */
    public function it_should_encode_a_string_to_hashids()
    {
        // arrange
        $target = app(\App\Services\HashidsService::class);
        $id = 1;
        $expected = 'lkPbX';

        // act
        $actual = $target->make($id);

        // assert
        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_should_decode_a_hash_into_ids()
    {
        // arrange
        $target = app(\App\Services\HashidsService::class);
        $hash = 'lkPbX';
        $expected = [1];

        // act
        $actual = $target->decode($hash);

        // assert
        $this->assertEquals($expected, $actual);
    }
}
