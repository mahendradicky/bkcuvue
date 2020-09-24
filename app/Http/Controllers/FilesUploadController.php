<?php

namespace App\Http\Controllers;

use App\Events\KegiatanDestroy;
use App\Imports\AnggotaCuDraftImport;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FilesUploadController extends Controller
{
    private $failures;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
            
            $random = str_random(3);
            // $file = Input::file('file');
            $file = $request->file('file');
            $temp_name = $file->getClientOriginalName();
            $fileext = $file->getClientOriginalExtension();
            $filename = pathinfo($temp_name, PATHINFO_FILENAME).'.'.$fileext;
    
            if(Storage::disk($request->disk)->put('1'.'/'.$filename,  File::get($file))) {
                $input['file_name'] = $filename;
                $input['file_type'] = $fileext;
                $input['name'] = $temp_name;
                // $input['kegiatan_id'] = $id;
                $input['file_path'] = '/files/materi/'.'1'.'/'.$filename; 
                // $file = materiUpload::create($input);
                // $id_file = $file->id;
                // $pivot = $request->pivot;
    
                // $rawQuery = 'insert into '.$pivot.' values('.$id_file .','.$id.')';
                // DB::insert($rawQuery);
                return response()->json([
                    'saved' => true,
                    // 'id' => $file->id,
                    'message'=>$temp_name.' Berhasil diUpload',
                    'file' => $file
                ], 200);
            }
            return response()->json([
                'saved' => false
            ], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function execute()
    {
        // broadcast(new KegiatanDestroy())->toOthers();
        $path = storage_path('/app/1');
        $files = File::files($path);
        foreach ($files as $file) {
            Excel::import(new AnggotaCuDraftImport, $file);
        }
        
    }
    

   
}
