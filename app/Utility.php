<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
  //public $timestamps = false;
  protected $table="tbl_utilities";
 protected $fillable = ['date_format','set_date_format'];
}
