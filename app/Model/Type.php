<?php

namespace Model;


use Illuminate\Database\Eloquent\Model;

class Type extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'name'
    ];
}