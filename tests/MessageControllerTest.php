<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class MessageControllerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_should_create_a_new_record_and_return_a_message()
    {
        $this->json('POST', 'slack', [
            'text' => 'http://test.com'
        ])
            ->seeJson([
                'response_type' => 'ephemeral',
                'text' => 'http://localhost/lkPbX',
                'attachments' => [
                    ['text' => 'Origin: http://test.com',]
                ]
            ]);
    }
}
