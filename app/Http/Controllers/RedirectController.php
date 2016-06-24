<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;

/**
 * Class RedirectController
 *
 * @package App\Http\Controllers
 */
class RedirectController extends Controller
{
    /**
     * @param Request $request
     * @param UrlService $service
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Request $request, UrlService $service)
    {
        $this->validate($request, [
            'href' => 'required',
            'hash' => 'unique:urls',
        ]);

        return $service->make($request);
    }

    /**
     * @param UrlService $service
     * @param $hash
     *
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     */
    public function redirect(UrlService $service, $hash)
    {
        return redirect($service->find($hash)->href);
    }

    /**
     * @param UrlService $service
     * @param $hash
     *
     * @return \App\Url
     */
    public function decode(UrlService $service, $hash)
    {
        return $service->find($hash);
    }
}
