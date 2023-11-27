<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected  $guarded = [];
    protected $appends = ['status_text'];
    
    public function getStatusTextAttribute(){

        if($this->status==1) return "Pending";
        else return "Delivered";

    }
}
