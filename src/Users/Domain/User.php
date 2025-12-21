<?php

namespace Src\Users\Domain;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasUuids;

    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'email',
        'role',
    ];
}
