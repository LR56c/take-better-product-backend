<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Src\Brands\Domain\Brand as DomainBrand;

class Brand extends DomainBrand
{
    use HasFactory;
}
