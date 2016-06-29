<?php

namespace App\Services;

use App\Url;
use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class UrlService
 *
 * @package App\Services
 */
class UrlService
{
    /**
     * @var Url
     */
    private $url;
    /**
     * @var HashidsService
     */
    private $service;

    /**
     * UrlService constructor.
     *
     * @param Url $url
     * @param HashidsService $service
     */
    public function __construct(Url $url, HashidsService $service)
    {
        $this->url = $url;
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @return Model
     */
    public function make(Request $request) : Model
    {
        $model = $this->url->create([
            'href' => $request->input('href'),
            'hash' => ($request->has('hash')) ?
                $request->input('hash') :
                $this->service->make((($this->url->latest()->id ?? 0) + 1)),
            'utm' => ($request->has('utm.*.source')) ? [
                'utm_source' => $request->input('utm.source', 'facebook'),
                'utm_medium' => $request->input('utm.medium'),
                'utm_campaign' => $request->input('utm.campaign'),
            ] : null
        ]);

        return Cache::rememberForever($model->hash, function() use ($model) {
            return $model;
        });
    }

    /**
     * @param string $hash
     *
     * @return Url
     */
    public function find(string $hash) : Url
    {
        return Cache::rememberForever($hash, function() use ($hash) {
            return $this->url->where('hash', $hash)->firstOrFail();
        });
    }
}
