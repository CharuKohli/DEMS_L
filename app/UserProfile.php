<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
  //public $timestamps = false;
  protected $table="tbl_userprofile";
 protected $fillable = ['name','designation','location','mobile', 'email', 'password'];
}
