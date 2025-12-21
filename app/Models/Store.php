<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Src\Stores\Domain\Store as DomainStore;

class Store extends DomainStore
{
    use HasFactory;
}
