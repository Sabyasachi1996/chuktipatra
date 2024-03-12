<?php

namespace App\Models\eagreement;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $table        =   'agreement_master';
    public $timestamps      =   false;
    protected $primaryKey   =   'id';

    protected $fillable = [
        'ref_num', 'applicant_type', 'applicant_user_id', 'co_applicant_type', 'co_applicant_user_id', 
        'witness_1_user_id', 'witness_2_user_id',  
        'property_type', 'property_detail', 
        'property_address', 'property_city', 'property_state', 'property_pin', 
        'cotract_detail', 'pg_grn', 
        'file_path', 'file_size', 'udin_num', 'udin_num_final', 'status', 'created_at'
    ];

    public function coApplicant() {
        return $this->hasOne(User::class, 'user_id', 'co_applicant_user_id');
    }

    public function witnessOne() {
        return $this->hasOne(User::class, 'user_id', 'witness_1_user_id');
    }

    public function witnessTwo() {
        return $this->hasOne(User::class, 'user_id', 'witness_2_user_id');
    }


}
