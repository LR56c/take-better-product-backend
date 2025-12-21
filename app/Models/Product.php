<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Src\Products\Domain\Product as DomainProduct;

class Product extends DomainProduct
{
    use HasFactory;
}
