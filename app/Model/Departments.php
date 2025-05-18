<?php

namespace Model;


use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'name',
        'users_id',
        'building_id'
    ];
}