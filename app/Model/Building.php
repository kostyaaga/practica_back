<?php

namespace Model;


use Illuminate\Database\Eloquent\Model;

class Building extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'name',
        'total_area',
        'used_area',
        'address',
        'total_floors',
        'added_by',
    ];

    public static function all($columns = ['*'])
    {
        return parent::all($columns);
    }
}