<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    protected $fillable = ['order_id', 'name', 'code', 'price', 'type', 'refund', 'user_id'];
}
