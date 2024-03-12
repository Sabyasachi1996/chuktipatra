<?php

namespace App\Models\eagreement;

use Illuminate\Database\Eloquent\Model;

class CoSignRequest extends Model
{
    protected $table        =   'cosign_requests';
    public $timestamps      =   false;
    protected $primaryKey   =   'cr_id';

    protected $fillable = [
        'cr_agreement_id', 'cr_cosigner_type', 'cr_phone', 'cr_code', 'has_accepted', 'acceptance_date', 'created_on'
    ];

    
}
