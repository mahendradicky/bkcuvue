<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = 'main_document';
    protected $fillable = ['kegiatan_id', 'file_name', 'file_type','file_path','name'];
}
