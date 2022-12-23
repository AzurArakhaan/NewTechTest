<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    const ENTITY_TYPE_USER = 'user';
    const ENTITY_TYPE_COMPANY = 'company';
    const ENTITY_TYPE_SITE = 'site';
}
