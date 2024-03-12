<?php

namespace App\Models\eagreement;

use Illuminate\Database\Eloquent\Model;
use DB;

class Token extends Model
{
    protected $table        =   'user_tokens';
    public $timestamps      =   false;

    protected $fillable = [
        'token', 'generated_on', 'expired_on', 'user_id'
    ];
    
}