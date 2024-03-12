<?php

namespace App\Models\eagreement;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class Payment extends Model
{
    protected $table        =   'payment_master';
    public $timestamps      =   false;
    protected $primaryKey   =   'id';

    protected $fillable = [
        'clientRefNum', 'clientReqId', 'clientReturnUrl', 'dpr', 'src', 'respUrl', 'action', 'paymentAmt', 'depMob', 'srcId',
        'userId', 'ip', 'mac', 'sysTimeStamp', 'pmtRequest','encDataRequest',
        'csRequest', 'gpr', 'paymentStatus', 'paymentStatusDesc', 
        'pmtResponse', 'encDataResponse' ,'csResponse', 'created_by', 'created_on', 'response_on'
    ];


    public function paymentDetail() {
        return $this->hasMany(PaymentDetail::class, 'pmt_id', 'id');
    }

}
