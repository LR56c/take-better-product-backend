<?php

namespace App\Models;

use Src\Brands\Domain\Brand as DomainBrand;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends DomainBrand
{
    use HasFactory;
}
