<?php

namespace App\Models;

use Src\Products\Domain\Product as DomainProduct;

class Product extends DomainProduct
{
    // This class now extends the domain model to maintain compatibility with Laravel's default structure
    // while allowing the core logic to reside in the src/ directory.
}
