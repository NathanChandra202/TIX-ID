<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cinema_id',
        'movie_id',
        'hours',
        'price',
         ];

    protected function casts(): array
    {
        return [
            //agar aformat data yg disimpan array bukan json
            'hours' => 'array'
         ];
    }


    public function cinema()
    {
        //karena schedule ada FK cinema_id definisikan dengan: belongsTo
        return $this->belongsTo(Cinema::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
