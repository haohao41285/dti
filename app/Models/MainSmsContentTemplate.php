<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainSmsContentTemplate extends Model
{
    protected $table = "main_sms_content_template";
    protected $fillable = ['template_title','sms_content_template','created_by','updated_by'];
}
