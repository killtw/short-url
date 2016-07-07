<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CatchAllOptionsRequestsProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $request = app('request');

        if ($request->isMethod('OPTIONS')) {
            app()->options($request->path(), function() {
                return response('', 200);
            });
        }
    }
}
