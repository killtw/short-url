<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Url
 *
 * @package App
 */
class Url extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'href', 'hash', 'utm',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'href' => 'string',
        'hash' => 'string',
        'utm' => 'object',
    ];

    /**
     * @return mixed
     */
    public function getRedirectAttribute()
    {
        if ($this->attributes['utm']) {
            $utm = http_build_query($this->attributes['utm']);

            /** @var \Illuminate\Support\Collection $url */
            $url = collect(parse_url($this->attributes['href']));

            if (! $url->has('query')) {
                return http_build_url($url->put('query', $utm)->toArray());
            };

            return http_build_url($url->put('query', "{$url->get('query')}&$utm")->toArray());
        }

        return $this->attributes['href'];
    }
}
