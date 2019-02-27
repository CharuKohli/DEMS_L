<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
  protected $table="tbl_ticket";
 protected $fillable = ['ticket_no','user_id','expense_name','description', 'location', 'status','date','status_updated_by'];
}
