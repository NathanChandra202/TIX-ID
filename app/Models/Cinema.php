<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{

    //mengaktifkan soft delete
    use SoftDeletes;


    //mendaftarkan kolom selain yang bawaannya, selain id dan timestampts soft delete. agar dapat diisi datanya ke kolom tersebut
    protected $fillable = ['name', 'location'];

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }
}
