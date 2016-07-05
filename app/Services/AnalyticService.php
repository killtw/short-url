<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelAnalytics\LaravelAnalytics;

class AnalyticService
{
    /**
     * @var LaravelAnalytics
     */
    private $analytics;

    /**
     * AnalyticService constructor.
     *
     * @param LaravelAnalytics $analytics
     */
    public function __construct(LaravelAnalytics $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * @param $hash
     *
     * @return Collection
     */
    public function getPageviews($hash)
    {
        $response = $this->analytics->performQuery(Carbon::now()->subDays(3650), Carbon::now(), 'ga:pageviews', [
            'filters' => "ga:pagePath==/{$hash}",
        ]);

        return collect(isset($response['rows']) ? $response['rows'] : [])->transform(function ($row) {
            return [
                'pageviews' => $row[0],
            ];
        })->flatten(1);
    }
}
