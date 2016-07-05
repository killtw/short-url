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
     * @return Url
     */
    public function make(Request $request) : Url
    {
        $model = $this->url->create([
            'href' => $request->input('href'),
            'hash' => ($request->has('hash')) ?
                $request->input('hash') :
                $this->service->make(($this->url->latest()->first()->id ?? 0) + 1),
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
    public function find($hash) : Url
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
     * @param $hash
     *
     * @return Collection
     */
    public function decode($hash) : Collection
    {
        $url = Cache::rememberForever($hash, function() use ($hash) {
            return $this->url->where('hash', $hash)->firstOrFail();
        });
        $response = Analytics::performQuery(Period::days(3650), 'ga:pageviews', [
            'filters' => "ga:pagePath==/{$url->hash}",
        ]);
        $ga = collect($response['rows'] ?? [])->transform(function (array $row) {
            return [
                'pageviews' => $row[0],
            ];
        })->flatten(1);

        return collect([
            'id' => $url->id,
            'hash' => $url->hash,
            'redirect' => $url->redirect,
            'ga' => [
                'pageviews' => $ga['pageviews']
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
