<?php

namespace Src\Users\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
