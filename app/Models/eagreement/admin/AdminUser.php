<?php

namespace App\Models\eagreement\admin;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $table        =   'admin_users';
    public $timestamps      =   false;
    protected $primaryKey   =   'admin_id';

    protected $fillable = [
        'admin_name', 'admin_phone'
    ];
}
