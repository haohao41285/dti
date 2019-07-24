<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PosSubject
 */
class PosSubjectWeb extends Model
{
    protected $table = 'pos_subject';

    public $timestamps = true;

    protected $fillable = [
        'sub_id',
        'sub_place_id',
        'sub_name',
        'sub_image',
        'sub_description',
        'sub_type',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'sub_status'
    ];

    protected $guarded = [];

        
}