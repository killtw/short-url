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

        $response = json_decode(
            app()->prepareResponse(
                app()->handle($request->create(route('store'), 'POST', $input->toArray()))
            )->getContent()
        );

        return response()->json([
            'response_type' => 'ephemeral',
            'text' => $response->url,
            'attachments' => [
                ['text' => "Origin: $response->redirect",]
            ]
        ]);
    }
}
