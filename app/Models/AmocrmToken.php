<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmocrmToken extends Model
{
    use HasFactory;

    protected $table = 'amocrm_tokens';

    protected $fillable = [
        'expires_in',
        'access_token',
        'refresh_token',
    ];
}
