<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinYear extends Model
{
  //public $timestamps = false;
  protected $table="tbl_financialyear";
 protected $fillable = ['fin_year'];

}
