<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
            'href' => 'required|active_url',
            'hash' => 'unique:urls|string',
            'utm' => 'array',
            'utm.source' => 'string',
            'utm.medium' => 'required_with:utm.source|string',
            'utm.campaign' => 'required_with:utm.source|string',
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
        return redirect($service->find($hash)->redirect);
    }

    /**
     * @param UrlService $service
     * @param $hash
     *
     * @return Collection
     */
    public function decode(UrlService $service, $hash)
    {
        return $service->decode($hash);
    }
}
