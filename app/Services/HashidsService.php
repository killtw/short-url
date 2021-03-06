<?php

namespace App\Services;

use Hashids\Hashids;

/**
 * Class HashidsServices
 *
 * @package App\Services
 */
class HashidsService
{
    /**
     * @var Hashids
     */
    protected $hashids;

    /**
     * HashidsServices constructor.
     */
    public function __construct()
    {
        $this->hashids = new Hashids(env('HASHIDS_SALT'), env('HASHIDS_LENGTH'), env('HASHIDS_ALPHABET'));
    }

    /**
     * @param int $ids
     *
     * @return string
     */
    public function make(int $ids) : string
    {
        return $this->hashids->encode($ids);
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    public function decode(string $hash) : array
    {
        return $this->hashids->decode($hash);
    }
}
