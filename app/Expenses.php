<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
  //public $timestamps = false;
  protected $table="tbl_expense";
 protected $fillable = ['user_id','fin_year','transaction_no','expense_name','paid_towards','paid_amount','payment_date','bill_path','remarks','payment_mode','bank_name','ifsc_code','acc_no','credit_no','debit_no','cheque_no','cheque_date'];

 public function vouchers(){
         return $this->hasMany(ExpVoucher::class);
     }
}
