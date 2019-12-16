<?php

namespace App\Imports;

use Auth;
use App\Cu;
use App\Tp;
use App\System;
use App\AnggotaCu;
use App\AnggotaCuCu;
use App\AnggotaCuCuDraft;
use App\AnggotaCuDraft;
use App\Region\Villages;
use App\Region\Districts;
use App\Region\Provinces;
use App\Region\Regencies;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AnggotaCuDraftImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{

    public function model(array $row)
    {
        $gender = array_key_exists('gender', $row) ? strtoupper($row['gender']) : '';
        $status_pernikahan = array_key_exists('status_pernikahan', $row) ? strtoupper($row['status_pernikahan']) : '';

        if(array_key_exists('ktp', $row) && $row['ktp']){
            $ktp = preg_replace('/\s+/', ' ',$row['ktp']);

            $anggotaCu = AnggotaCu::where('nik',$ktp)->select('id','nik')->first();
            $anggotaCuDraft = AnggotaCuDraft::where('nik',$ktp)->select('id','nik')->first();
        }else{
            $kelas_ktp = System::findOrFail(1);
            $ktp = $kelas_ktp->nik;
            $val = $ktp + 1;
            $kelas_ktp->nik = str_pad($val,16,"0",STR_PAD_LEFT);
            $kelas_ktp->update();

            $anggotaCu = null;
            $anggotaCuDraft = null;
        }

        if(array_key_exists('provinsi', $row) && $row['provinsi']){
            $provinces = Provinces::where('name','like', '%' .strtoupper($row['provinsi']). '%')->first();
            $provinces = $provinces ? $provinces->id : '';
        }else{
            $provinces = '';
        }
        if(array_key_exists('kabupaten', $row) && $row['kabupaten']){
            $regencies = Regencies::where('name','like', '%' .strtoupper($row['kabupaten']). '%')->first();
            $regencies = $regencies ? $regencies->id : '';
        }else{
            $regencies = '';
        }
        if(array_key_exists('kecamatan', $row) && $row['kecamatan']){
            $districts = Districts::where('name','like', '%' .strtoupper($row['kecamatan']). '%')->first();
            $districts = $districts ? $districts->id : '';
        }else{
            $districts = '';
        }
        if(array_key_exists('kelurahan', $row) && $row['kelurahan']){
            $villages = Villages::where('name','like', '%' .strtoupper($row['kelurahan']). '%')->first();
            $villages = $villages ? $villages->id : '';
        }else{
            $villages = '';
        }

        if($gender == 'L'){
            $gender = 'LAKI-LAKI';
        }else if($gender == 'P'){
            $gender = 'PEREMPUAN';
        }

        if($status_pernikahan == 'KW'){
            $status_pernikahan = 'MENIKAH';
        }else if($status_pernikahan == 'TK'){
            $status_pernikahan = 'BELUM MENIKAH';
        }

        if(!$anggotaCu && !$anggotaCuDraft){
            $anggotaCu = AnggotaCuDraft::create([
                'name' => array_key_exists('nama', $row) ? $row['nama'] : '',
                'id_provinces' => $provinces,
                'id_regencies' => $regencies,
                'id_districts' => $districts,
                'id_villages' => $villages,
                'nik' => $ktp,
                'npwp' => array_key_exists('npwp', $row) ? $row['npwp'] : '',
                'tempat_lahir' => array_key_exists('tempat_lahir', $row)? $row['tempat_lahir'] : '',
                'tanggal_lahir' => array_key_exists('tanggal_lahir', $row) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir']) : '',
                'kelamin' => $gender,
                'agama' => array_key_exists('agama', $row) ? strtoupper($row['agama']) : '',
                'status' => $status_pernikahan,
                'alamat' => array_key_exists('alamat', $row)? $row['alamat'] : '',
                'rt' => array_key_exists('rt', $row) ? preg_replace('/[^A-Za-z0-9]/', '',$row['rt']) : '',
                'rw' => array_key_exists('rw', $row) ? preg_replace('/[^A-Za-z0-9]/', '',$row['rw']) : '',
                'kontak' => array_key_exists('kontak_lain', $row) ? $row['kontak_lain'] : '' ,
                'darah' => array_key_exists('golongan_darah', $row) ? strtoupper($row['golongan_darah']) : '',
                'tinggi' => array_key_exists('tinggi', $row) ? $row['tinggi'] : '',
                'email' => array_key_exists('email', $row) ? $row['email'] : '',
                'hp' => array_key_exists('hp', $row) ? preg_replace('/\s+/', '',$row['hp']) : '',
                'pendidikan' => array_key_exists('pendidikan', $row) ? strtoupper($row['pendidikan']) : '',
                'lembaga' => array_key_exists('tempat_kerja', $row) ? $row['tempat_kerja'] : '',
                'jabatan' => array_key_exists('jabatan', $row) ? strtoupper($row['jabatan']) : '',
                'organisasi' => array_key_exists('organisasi', $row) ? $row['organisasi'] : '',
                'ahli_waris' => array_key_exists('ahli_waris', $row) ? $row['organisasi'] : '',
                'pekerjaan' => array_key_exists('pekerjaan', $row) ? strtoupper($row['pekerjaan']) : '',
                'penghasilan' => array_key_exists('rata_rata_penghasilan_perbulan', $row) ? $row['rata_rata_penghasilan_perbulan'] : 0,
                'pengeluaran' => array_key_exists('rata_rata_pengeluaran_perbulan', $row) ? $row['rata_rata_pengeluaran_perbulan'] : 0,
                'suku' => array_key_exists('suku', $row) ? strtoupper($row['suku']) : '',
                'nama_ibu' => array_key_exists('nama_ibu', $row) ? $row['nama_ibu'] : '',
                'kk' => array_key_exists('kk', $row) ? $row['kk'] : ''
            ]);
        }

        if($anggotaCu){
            $cu = Cu::where('no_ba', $row['no_ba_cu'])->select('id','no_ba')->first();
            $tp = Tp::where('id_cu', $cu->id)->where('no_tp', $row['kode_tp'])->select('id','id_cu','no_tp')->first();

            AnggotaCuCuDraft::create([
                'anggota_cu_draft_id' => $anggotaCu->id,
                'cu_id' => $cu->id,
                'tp_id' => $tp->id,
                'no_ba' => array_key_exists('no_ba', $row) ? $row['no_ba'] : '',
                'tanggal_masuk' => array_key_exists('tanggal_jadi_anggota', $row) ?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_jadi_anggota']) : '',
                'keterangan_masuk' => array_key_exists('keterangan_jadi_anggota', $row) ? $row['keterangan_jadi_anggota'] : '',
            ]);
        }
    }
    
    public function chunkSize(): int
    {
        return 1000;
    }
}
