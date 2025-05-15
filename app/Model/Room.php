<?php

namespace Model;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'number',
        'area',
        'seats',
        'floor',
        'building_id',
        'added_by',
        'type'
    ];

    public static function all($columns = ['*'])
    {
        return parent::all($columns);
    }
}