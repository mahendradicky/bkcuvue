<?php
namespace App;

use illuminate\Database\Eloquent\Model;
use App\Support\Dataviewer;
use Spatie\Activitylog\Traits\LogsActivity;

class AsetTetapLokasi extends Model {
    
    use Dataviewer, LogsActivity;

    protected $table = 'aset_tetap_lokasi';

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    
    public static $rules = [
        'name' => 'required'
    ];
    
    protected $fillable = ['name','keterangan'];

    protected $allowedFilters = [
        'id','name','keterangan','created_at','updated_at','has_aset_tetap_count'
    ];

    protected $orderable = [
        'id','name','keterangan','created_at','updated_at','has_aset_tetap_count'
    ];

    public static function initialize(){
        return [
            'name' => '', 'keterangan' => ''
        ];
    }

    public function hasasettetap()
    {
        return $this->hasMany('App\AsetTetap','aset_tetap_lokasi_id','id');
    }
}