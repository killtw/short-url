<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

/**
 * Class AnalyticService
 *
 * @package App\Services
 */
class AnalyticService
{
    /**
     * @var Analytics
     */
    private $analytics;

    /**
     * AnalyticService constructor.
     *
     * @param Analytics $analytics
     */
    public function __construct(Analytics $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * @param string $hash
     *
     * @return Collection
     */
    public function getPageviews(string $hash) : Collection
    {
        $response = $this->analytics->performQuery(Period::days(3650), 'ga:pageviews', [
            'filters' => "ga:pagePath==/{$hash}",
        ]);

        return collect($response['rows'] ?? [])->transform(function (array $row) {
            return [
                'pageviews' => $row[0],
            ];
        })->flatten(1);
    }
}
