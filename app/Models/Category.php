<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Src\Categories\Domain\Category as DomainCategory;

class Category extends DomainCategory
{
    use HasFactory;
}
