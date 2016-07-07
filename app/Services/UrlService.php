<?php

namespace App\Services;

use App\Url;
use Cache;
use Illuminate\Support\Collection;
use Irazasyed\LaravelGAMP\Facades\GAMP;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Analytics\AnalyticsFacade as Analytics;
use Spatie\Analytics\Period;

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
     * @var AnalyticService
     */
    private $analytics;

    /**
     * UrlService constructor.
     *
     * @param Url $url
     * @param HashidsService $service
     * @param AnalyticService $analytics
     */
    public function __construct(Url $url, HashidsService $service, AnalyticService $analytics)
    {
        $this->url = $url;
        $this->service = $service;
        $this->analytics = $analytics;
    }

    /**
     * @param Request $request
     *
     * @return Url
     */
    public function make(Request $request) : Url
    {
        $model = $this->url->create([
            'href' => $request->input('href'),
            'hash' => ($request->has('hash')) ?
                $request->input('hash') :
                $this->service->make(($this->url->latest()->first()->id ?? 0) + 1),
            'utm' => ($request->has('utm.*.source') and $request->input('utm.source') != null) ? [
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
        GAMP::setClientId($this->getClientId())
            ->setDocumentPath($hash)
            ->setUserAgentOverride($_SERVER['HTTP_USER_AGENT'] ?? '')
            ->setDocumentReferrer($_SERVER['HTTP_REFERER'] ?? '')
            ->sendPageview();

        return Cache::rememberForever($hash, function() use ($hash) {
            return $this->url->where('hash', $hash)->firstOrFail();
        });
    }

    /**
     * @param string $hash
     *
     * @return Collection
     */
    public function decode(string $hash) : Collection
    {
        $url = Cache::rememberForever($hash, function() use ($hash) {
            return $this->url->where('hash', $hash)->firstOrFail();
        });

        return collect([
            'id' => $url->id,
            'hash' => $url->hash,
            'redirect' => $url->redirect,
            'ga' => [
                'pageviews' => $this->analytics->getPageviews($hash)['pageviews']
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function getClientId() : string
    {
        if (isset($_COOKIE['_ga'])) {
            return substr($_COOKIE['_ga'], 6);
        }

        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
