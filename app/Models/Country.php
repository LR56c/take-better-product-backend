<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Src\Countries\Domain\Country as DomainCountry;

class Country extends DomainCountry
{
    use HasFactory;
}
