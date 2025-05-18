<?php

namespace Model;


use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'number',
        'area',
        'seats',
        'floor',
        'building_id',
        'type'
    ];

}