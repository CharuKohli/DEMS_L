<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alerts extends Model
{
  protected $table="tbl_alerts";
 protected $fillable = ['from_date','to_date','exp_name','for_whom'];
}
