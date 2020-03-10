<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebServiceType extends Model
{
    protected $table = 'web_service_types';
    protected $fillable = [
        'web_service_type_id',
        'web_service_type',
        'web_service_type_name',
        'web_service_type_status'
    ];
    public $timestamps = false;
    public function services(){
        return $this->hasMany(Webservice::class,'web_service_type_id','web_service_type_id');
    }

}
