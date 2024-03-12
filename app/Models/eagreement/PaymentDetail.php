<?php

namespace App\Models\eagreement;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class PaymentDetail extends Model
{
    protected $table        =   'payment_details';
    public $timestamps      =   false;
    protected $primaryKey   =   'id';

    protected $fillable = [
        'pmt_id', 'pmtCategory', 'deptCode', 'svcCode', 'drn', 'identificationNo', 'depName',
        'depAddress', 'depEmail', 'onBehalfOf', 'depType', 'periodFrom', 'periodTo',
        'remarks', 'totalAmt', 'paramDtls', 'grn', 'grnTime', 'grnStatus', 'grnStatusDesc', 'bank', 'paymentMode', 'brn', 'brnTime', 'gatewayRefId'
    ];


    public function payment() {
        return $this->belongsTo(Payment::class, 'pmt_id', 'id');
    }
}
