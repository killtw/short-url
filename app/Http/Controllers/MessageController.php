<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

/**
 * Class MessageController
 *
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function slack(Request $request)
    {
        list($href, $hash, $source, $medium, $campaign) = array_pad(explode(' ', $request->input('text')), 5, null);

        $input = collect(compact('href', 'hash'));

        if ($source) {
            $input->put('utm', compact('source', 'medium', 'campaign'));
        }

        $response = app()->prepareResponse(
            app()->handle($request->create(route('store'), 'POST', $input->toArray()))
        );
        $json = json_decode($response->getContent());

        if ($response->getStatusCode() === 422) {
            return response()->json([
                'response_type' => 'ephemeral',
                'attachments' => [
                    [
                        'color' => 'danger',
                        'title' => ':x: The hash has already been taken.',
                    ],
                ]
            ]);
        }

        return response()->json([
            'response_type' => 'ephemeral',
            'text' => $json->url,
            'attachments' => [
                ['text' => "Origin: $json->redirect",]
            ]
        ]);
    }
}
