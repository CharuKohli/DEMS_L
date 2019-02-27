<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpVoucher extends Model
{

  protected $table="tbl_exp_vouchers";
 protected $fillable = ['expenses_id','voucher_path'];

 public function expense()
    {
        return $this->belongsTo(Expenses::class);
    }
}
