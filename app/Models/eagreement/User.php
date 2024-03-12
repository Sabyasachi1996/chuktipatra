<?php

namespace App\Models\eagreement;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table        =   'users';
    public $timestamps      =   false;
    protected $primaryKey   =   'user_id';

    protected $fillable = [
        'user_phone', 'user_email', 'user_aadhaar_num', 'user_aadhaar_name', 'user_aadhaar_address', 
        'user_aadhaar_dob', 'user_aadhaar_photo', 'user_is_udin_verified', 
        'user_is_active', 'user_created_on', 'user_updated_on'
    ];

    public function agreement() {
        return $this->hasMany(Agreement::class, 'user_id', 'applicant_user_id');
    }
}
