<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseName extends Model
{
  //public $timestamps = false;
  protected $table="tbl_expensename";
 protected $fillable = ['exp_name','due_date','for_whom'];
}
