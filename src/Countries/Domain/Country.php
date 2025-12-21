<?php

namespace Src\Countries\Domain;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Src\Stores\Domain\Store;

class Country extends Model
{
    use HasUuids;

    protected $table = 'countries';

    protected $fillable = [
        'name',
        'code',
        'currency',
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
