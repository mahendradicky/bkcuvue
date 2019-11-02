<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use Image;
use App\AnggotaCu;
use App\JalinanKlaim;
use App\JalinanKlaimStatus;
use App\Support\Helper;
use Illuminate\Http\Request;
use Venturecraft\Revisionable\Revision;

class JalinanKlaimController extends Controller{

	protected $message = "Klaim JALINAN";

	public function index($status, $awal, $akhir)
	{
		if($awal != 'undefined' && $akhir != 'undefined'){
			$table_data = JalinanKlaim::with('anggota_cu','verifikasi_pengurus_data.aktivis.pekerjaan_aktif','verifikasi_pengawas_data.aktivis.pekerjaan_aktif','verifikasi_manajemen_data.aktivis.pekerjaan_aktif','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->whereBetween('tanggal_pencairan',[$awal, $akhir])->where('status_klaim',$status)
			->advancedFilter();
		}else if($awal != 'undefined'){
			$table_data = JalinanKlaim::with('anggota_cu','verifikasi_pengurus_data.aktivis.pekerjaan_aktif','verifikasi_pengawas_data.aktivis.pekerjaan_aktif','verifikasi_manajemen_data.aktivis.pekerjaan_aktif','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->where('tanggal_pencairan',$awal)->where('status_klaim',$status)->advancedFilter();
		}else{
			$table_data = JalinanKlaim::with('anggota_cu','verifikasi_pengurus_data.aktivis.pekerjaan_aktif','verifikasi_pengawas_data.aktivis.pekerjaan_aktif','verifikasi_manajemen_data.aktivis.pekerjaan_aktif','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->where('status_klaim',$status)->advancedFilter();
		}

		return response()
			->json([
				'model' => $table_data
			]);
	}

	public function indexCu($cu, $status, $awal, $akhir)
	{
		if($awal != 'undefined' && $akhir != 'undefined'){
			$table_data = JalinanKlaim::with('anggota_cu','verifikasi_pengurus_data.aktivis.pekerjaan_aktif','verifikasi_pengawas_data.aktivis.pekerjaan_aktif','verifikasi_manajemen_data.aktivis.pekerjaan_aktif','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->whereBetween('tanggal_pencairan',[$awal, $akhir])->where('status_klaim',$status)->whereHas('anggota_cu_cu', function($query) use ($cu){ 
				$query->where('cu_id',$cu); 
			})->advancedFilter();
		}else if($awal != 'undefined'){
			$table_data = JalinanKlaim::with('anggota_cu','verifikasi_pengurus_data.aktivis.pekerjaan_aktif','verifikasi_pengawas_data.aktivis.pekerjaan_aktif','verifikasi_manajemen_data.aktivis.pekerjaan_aktif','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->where('tanggal_pencairan',$awal)->where('status_klaim',$status)->whereHas('anggota_cu_cu', function($query) use ($cu){ 
				$query->where('cu_id',$cu); 
			})->advancedFilter();
		}else{
			$table_data = JalinanKlaim::with('anggota_cu','verifikasi_pengurus_data.aktivis.pekerjaan_aktif','verifikasi_pengawas_data.aktivis.pekerjaan_aktif','verifikasi_manajemen_data.aktivis.pekerjaan_aktif','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->where('status_klaim',$status)->whereHas('anggota_cu_cu', function($query) use ($cu){ 
				$query->where('cu_id',$cu); 
			})->advancedFilter();
		}

		return response()
			->json([
				'model' => $table_data
			]);
	}

	public function indexCair($tanggal_pencairan)
	{
		$table_data = DB::table('jalinan_klaim')
		->join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
		->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
		->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
		->select(DB::raw($this->queryCair()))
		->where('tanggal_pencairan', $tanggal_pencairan)
		->groupBy('anggota_cu_cu.cu_id')
		->get();

		return response()
			->json([
				'model' => $table_data
			]);
	}

	public function indexLaporanCair($awal, $akhir)
	{
		$table_data = DB::table('jalinan_klaim')
		->join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
		->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
		->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
		->select(DB::raw($this->queryCair()))
		->where('status_klaim','>=',5)
		->whereBetween('tanggal_pencairan',[$awal, $akhir])
		->groupBy('anggota_cu_cu.cu_id')
		->get();

		return response()
			->json([
				'model' => $table_data
			]);
	}

	public function queryCair(){
		return 'MAX(cu.no_ba) as no_ba,
		MAX(cu.name) as cu_name,
		MAX(anggota_cu_cu.cu_id) as cu_id,
		COUNT(CASE WHEN status_klaim="4" THEN 1 END) AS status_klaim_setuju, 
		COUNT(CASE WHEN status_klaim="5" THEN 1 WHEN status_klaim="6" THEN 1 END) AS status_klaim_cair, 
		COUNT(CASE WHEN anggota_cu.kelamin="LAKI-LAKI" THEN 1 END) AS lakilaki, 
		COUNT(CASE WHEN anggota_cu.kelamin="PEREMPUAN" THEN 1 END) AS perempuan, 
		COUNT(CASE WHEN tipe="MENINGGAL" THEN 1 END) AS meninggal, 
		COUNT(CASE WHEN tipe="CACAT" THEN 1 END) AS cacat, 
		SUM(tunas_disetujui) AS tunas_disetujui, 
		SUM(lintang_disetujui) AS lintang_disetujui,
		SUM(tunas_disetujui) + SUM(lintang_disetujui) as tot_disetujui';
	}

	public function indexLaporanPenyebab($awal, $akhir, $cu){
		if($cu == 'semua'){
			$table_data = JalinanKlaim::join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
			->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
			->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
			->select(DB::raw('
				COUNT(cu.no_ba) as cu,
				MAX(jalinan_klaim.created_at) as created_at,
				MAX(kategori_penyakit) as kategori,
				COUNT(case when anggota_cu.kelamin="LAKI-LAKI" then 1 end) AS lakilaki, 
				COUNT(case when anggota_cu.kelamin="PEREMPUAN" then 1 end) AS perempuan,
				COUNT(case when tipe="MENINGGAL" then 1 end) AS meninggal, 
				COUNT(case when tipe="CACAT" then 1 end) AS cacat, 
				COUNT(*) as total'))	
			->where('status_klaim','>=',5)
			->whereBetween('tanggal_pencairan',[$awal, $akhir])
			->groupBy('kategori_penyakit')
			->get();
		}else{
			$table_data = JalinanKlaim::whereHas('anggota_cu_cu', 			
			function($query) use ($cu){ 
				$query->where('anggota_cu_cu.cu_id',$cu); 
			})->join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
			->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
			->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
			->select(DB::raw('
				COUNT(cu.no_ba) as cu,
				MAX(jalinan_klaim.created_at) as created_at,
				MAX(kategori_penyakit) as kategori,
				COUNT(case when anggota_cu.kelamin="LAKI-LAKI" then 1 end) AS lakilaki, 
				COUNT(case when anggota_cu.kelamin="PEREMPUAN" then 1 end) AS perempuan,
				COUNT(case when tipe="MENINGGAL" then 1 end) AS meninggal, 
				COUNT(case when tipe="CACAT" then 1 end) AS cacat, 
				COUNT(*) as total'))	
			->where('status_klaim','>=',5)
			->whereBetween('tanggal_pencairan',[$awal, $akhir])
			->groupBy('kategori_penyakit')
			->get();
		}

		return response()
			->json([
				'model' => $table_data
			]);
	}


	public function indexLaporanPenyebabDetail($cu, $status, $awal, $akhir, $kategori)
	{
		$table_data = JalinanKlaim::with('anggota_cu','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->whereBetween('tanggal_pencairan',[$awal, $akhir])->where('status_klaim',$status)->whereHas('anggota_cu_cu', function($query) use ($cu){ 
			$query->where('cu_id',$cu); 
		})->where('kategori_penyakit',$kategori)->advancedFilter();

		return response()
			->json([
				'model' => $table_data
			]);
	}

	public function indexLaporanUsia($awal, $akhir, $cu){
		if($cu == 'semua'){
			$table_data = JalinanKlaim::join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
			->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
			->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
			->select(DB::raw('
				COUNT(cu.no_ba) as cu,
				MAX(anggota_cu.tanggal_lahir) as tanggal_lahir,
				MAX(jalinan_klaim.created_at) as created_at,
				COUNT(case when anggota_cu.kelamin="LAKI-LAKI" then 1 end) AS lakilaki, 
				COUNT(case when anggota_cu.kelamin="PEREMPUAN" then 1 end) AS perempuan,
				COUNT(case when tipe="MENINGGAL" then 1 end) AS meninggal, 
				COUNT(case when tipe="CACAT" then 1 end) AS cacat, 
				TIMESTAMPDIFF(YEAR, max(anggota_cu.tanggal_lahir), CURDATE()) AS usia
			'))->addSelect(DB::raw('
				CASE
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 1 THEN "0 s.d. 1 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 1 AND TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 10 THEN "> 1 s.d. 10 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 10 AND TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 20 THEN "> 10 s.d. 20 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 20 AND TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 30 THEN "> 20 s.d. 30 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 30 AND TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 40 THEN "30 s.d. 40 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 40 AND TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 50 THEN "40 s.d. 50 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 50 AND TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 60 THEN "50 s.d. 60 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 60 AND TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 70 THEN "60 s.d. 70 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 70 THEN "> 70 Tahun"
				END as kategori,
				COUNT(*) as total
			'))
			->where('status_klaim','>=',5)
			->whereBetween('tanggal_pencairan',[$awal, $akhir])
			->groupBy('kategori')
			->get();
		}else{
			$table_data = JalinanKlaim::whereHas('anggota_cu_cu', 			
				function($query) use ($cu){ 
					$query->where('anggota_cu_cu.cu_id',$cu); 
				})
				->join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
				->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
				->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
				->select(DB::raw('
					COUNT(cu.no_ba) as cu,
					MAX(anggota_cu.tanggal_lahir) as tanggal_lahir,
					MAX(jalinan_klaim.created_at) as created_at,
					COUNT(case when anggota_cu.kelamin="LAKI-LAKI" then 1 end) AS lakilaki, 
					COUNT(case when anggota_cu.kelamin="PEREMPUAN" then 1 end) AS perempuan,
					COUNT(case when tipe="MENINGGAL" then 1 end) AS meninggal, 
					COUNT(case when tipe="CACAT" then 1 end) AS cacat, 
					TIMESTAMPDIFF(YEAR, max(anggota_cu.tanggal_lahir), CURDATE()) AS usia
				'))->addSelect(DB::raw('
					CASE
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 1 THEN "0 s.d. 1 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 1 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 10 THEN "> 1 s.d. 10 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 10 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 20 THEN "> 10 s.d. 20 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 20 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 30 THEN "> 20 s.d. 30 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 30 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 40 THEN "30 s.d. 40 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 40 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 50 THEN "40 s.d. 50 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 50 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 60 THEN "50 s.d. 60 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 60 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 70 THEN "60 s.d. 70 Tahun"
					WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 70 THEN "> 70 Tahun"
					END as kategori,
					COUNT(*) as total
				'))
				->where('status_klaim','>=',5)
				->whereBetween('tanggal_pencairan',[$awal, $akhir])
				->groupBy('kategori')
				->get();
		}

		return response()
			->json([
				'model' => $table_data
			]);
	}

	public function indexLaporanUsiaDetail($id, $status, $awal, $akhir, $kategori, $from, $to)
	{
		$from = \Carbon\Carbon::now()->subYears($from);
		$to = \Carbon\Carbon::now()->subYears($to);

		$table_data = JalinanKlaim::with('anggota_cu','anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')
		->whereHas('anggota_cu', function($query) use ($from, $to){ 
			$query->whereBetween('tanggal_lahir',[$from, $to]);
		})->where('status_klaim',$status)
		->whereHas('anggota_cu_cu', function($query) use ($id){ 
			$query->where('cu_id',$id); 
		})->whereBetween('tanggal_lahir',[$from, $to])->advancedFilter();

		return response()
			->json([
				'model' => $table_data
			]);
	}

	public function indexLaporanLama($awal, $akhir, $cu){
		if($cu == 'semua'){
			$table_data = JalinanKlaim::join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
				->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
				->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
				->select(DB::raw('
					COUNT(cu.no_ba) as cu,
					max(anggota_cu.tanggal_lahir) as tanggal_lahir,
					Max(jalinan_klaim.created_at) as created_at,
					COUNT(case when anggota_cu.kelamin="LAKI-LAKI" then 1 end) AS lakilaki, 
					COUNT(case when anggota_cu.kelamin="PEREMPUAN" then 1 end) AS perempuan,
					COUNT(case when tipe="MENINGGAL" then 1 end) AS meninggal, 
					COUNT(case when tipe="CACAT" then 1 end) AS cacat, 
					TIMESTAMPDIFF(YEAR, max(anggota_cu.tanggal_lahir), CURDATE()) AS usia,
					TIMESTAMPDIFF(YEAR, max(anggota_cu_cu.tanggal_masuk), CURDATE()) AS lama_menjadi_anggota
				'))->addSelect(DB::raw('
					CASE
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 1 THEN "0 s.d. 1 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 1 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 3 THEN "> 1 s.d. 3 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 3 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 5 THEN "3 > s.d. 5 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 5 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 7 THEN "5 s.d. 7 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 7 THEN "> 7 Tahun"
					END as kategori,
					COUNT(*) as total
				'))
				->where('status_klaim','>=',5)
				->whereBetween('tanggal_pencairan',[$awal, $akhir])
				->groupBy('kategori')
				->get();
		}else{
			$table_data = JalinanKlaim::whereHas('anggota_cu_cu',
				function($query) use ($cu){ 
					$query->where('anggota_cu_cu.cu_id',$cu); 
				})
				->join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
				->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
				->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
				->select(DB::raw('
					COUNT(cu.no_ba) as cu,
					max(anggota_cu.tanggal_lahir) as tanggal_lahir,
					Max(jalinan_klaim.created_at) as created_at,
					COUNT(case when anggota_cu.kelamin="LAKI-LAKI" then 1 end) AS lakilaki, 
					COUNT(case when anggota_cu.kelamin="PEREMPUAN" then 1 end) AS perempuan,
					COUNT(case when tipe="MENINGGAL" then 1 end) AS meninggal, 
					COUNT(case when tipe="CACAT" then 1 end) AS cacat, 
					TIMESTAMPDIFF(YEAR, max(anggota_cu.tanggal_lahir), CURDATE()) AS usia,
					TIMESTAMPDIFF(YEAR, max(anggota_cu_cu.tanggal_masuk), CURDATE()) AS lama_menjadi_anggota
				'))->addSelect(DB::raw('
					CASE
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 1 THEN "0 s.d. 1 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 1 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 3 THEN "> 1 s.d. 3 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 3 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 5 THEN "3 > s.d. 5 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 5 and TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) < 7 THEN "5 s.d. 7 Tahun"
						WHEN TIMESTAMPDIFF(YEAR, anggota_cu.tanggal_lahir, CURDATE()) > 7 THEN "> 7 Tahun"
					END as kategori,
					COUNT(*) as total
				'))
				->where('status_klaim','>=',5)
				->whereBetween('tanggal_pencairan',[$awal, $akhir])
				->groupBy('kategori')
				->get();
		}

		
		return response()
		->json([
			'model' => $table_data
		]);
	}
	
	public function indexLaporanSimpanan($awal, $akhir, $cu){
		$table_data = DB::table('jalinan_klaim')
			->join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
			->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
			->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
			->join('anggota_produk_cu', 'anggota_produk_cu.anggota_cu_id', '=', 'jalinan_klaim.anggota_cu_id')
			->join('produk_cu', 'produk_cu.id', '=', 'anggota_produk_cu.anggota_cu_id')
			->select(DB::raw('
				MAX(anggota_cu_cu.cu_id) as cu_id,
				MAX(CASE WHEN anggota_cu.kelamin="LAKI-LAKI" THEN 1 END) AS lakilaki, 
				MAX(CASE WHEN anggota_cu.kelamin="PEREMPUAN" THEN 1 END) AS perempuan,
				SUM(CASE WHEN anggota_produk_cu.lama_pinjaman IS NULL THEN anggota_produk_cu.saldo END) AS saldo
			'))->addSelect(DB::raw('
				CASE
					WHEN max(saldo) < 1000000 THEN "< 1 Juta"
					WHEN max(saldo) > 1000000 AND max(saldo) < 10000000  THEN "> 1 s.d. 10 Juta"
					WHEN max(saldo) > 10000000 AND max(saldo) < 20000000  THEN "> 10 s.d. 20 Juta"
					WHEN max(saldo) > 20000000 AND max(saldo) < 30000000  THEN "> 20 s.d. 30 Juta"
					WHEN max(saldo) > 30000000 AND max(saldo) < 40000000  THEN "> 30 s.d. 40 Juta"
					WHEN max(saldo) > 40000000 AND max(saldo) < 50000000  THEN "> 40 s.d. 50 Juta"
				END as kategori
			'))
			->where('jalinan',1)
			->where('status_klaim','>=',5)
			->whereBetween('tanggal_pencairan',[$awal, $akhir])
			->groupBy('jalinan_klaim.anggota_cu_id')
			->get();

		$table_data2 = [
			'< 1 Juta' => [
				'kategori' => '< 1 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
			],
			'> 1 s.d. 10 Juta' => [
				'kategori' => '> 1 s.d. 10 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
			],
			'> 10 s.d. 20 Juta' => [
				'kategori' => '> 10 s.d. 20 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
			],
			'> 20 s.d. 30 Juta' => [
				'kategori' => '> 20 s.d. 30 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
			],
			'> 40 s.d. 50 Juta' => [
				'kategori' => '> 40 s.d. 50 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
			]
		];

		foreach($table_data as $t){
			foreach($table_data2 as $key => $t2){
				if($t->kategori == $key){
					if($t->lakilaki > 0){
						$table_data2[$key]['lakilaki'] =+ 1;
					}else if($t->perempuan > 0){
						$table_data2[$key]['perempuan'] =+ 1;
					}
					$table_data2[$key]['total'] =+ 1;
					$table_data2[$key]['saldo'] =+ $t->saldo;
				}
			}
		}

		return response()
		->json([
			'model' => $table_data2
		]);
	} 

	public function indexLaporanPinjaman($awal, $akhir, $cu){
		$table_data = DB::table('jalinan_klaim')
			->join('anggota_cu_cu', 'anggota_cu_cu.id', '=', 'jalinan_klaim.anggota_cu_cu_id')
			->join('anggota_cu', 'anggota_cu.id', '=', 'anggota_cu_cu.anggota_cu_id')
			->join('cu', 'cu.id', '=', 'anggota_cu_cu.cu_id')
			->join('anggota_produk_cu', 'anggota_produk_cu.anggota_cu_id', '=', 'jalinan_klaim.anggota_cu_id')
			->select(DB::raw('
				MAX(anggota_cu_cu.cu_id) as cu_id,
				MAX(CASE WHEN anggota_cu.kelamin="LAKI-LAKI" THEN 1 END) AS lakilaki, 
				MAX(CASE WHEN anggota_cu.kelamin="PEREMPUAN" THEN 1 END) AS perempuan,
				SUM(CASE WHEN anggota_produk_cu.lama_pinjaman IS NOT NULL THEN anggota_produk_cu.saldo END) AS saldo
			'))->addSelect(DB::raw('
				CASE
					WHEN max(saldo) < 1000000 THEN "< 1 Juta"
					WHEN max(saldo) > 1000000 AND max(saldo) < 10000000  THEN "> 1 s.d. 10 Juta"
					WHEN max(saldo) > 10000000 AND max(saldo) < 20000000  THEN "> 10 s.d. 20 Juta"
					WHEN max(saldo) > 20000000 AND max(saldo) < 30000000  THEN "> 20 s.d. 30 Juta"
					WHEN max(saldo) > 30000000 AND max(saldo) < 40000000  THEN "> 30 s.d. 40 Juta"
					WHEN max(saldo) > 40000000 AND max(saldo) < 50000000  THEN "> 40 s.d. 50 Juta"
				END as kategori
			'))
			->where('status_klaim','>=',5)
			->whereBetween('tanggal_pencairan',[$awal, $akhir])
			->groupBy('jalinan_klaim.anggota_cu_id')
			->get();

			$table_data2 = [
				'< 1 Juta' => [
					'kategori' => '< 1 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
				],
				'> 1 s.d. 10 Juta' => [
					'kategori' => '> 1 s.d. 10 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
				],
				'> 10 s.d. 20 Juta' => [
					'kategori' => '> 10 s.d. 20 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
				],
				'> 20 s.d. 30 Juta' => [
					'kategori' => '> 20 s.d. 30 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
				],
				'> 40 s.d. 50 Juta' => [
					'kategori' => '> 40 s.d. 50 Juta','lakilaki' => 0,'perempuan' => 0,'total' => 0,'saldo' => 0
				]
			];

			foreach($table_data as $t){
				foreach($table_data2 as $key => $t2){
					if($t->kategori == $key){
						if($t->lakilaki > 0){
							$table_data2[$key]['lakilaki'] =+ 1;
						}else if($t->perempuan > 0){
							$table_data2[$key]['perempuan'] =+ 1;
						}
						$table_data2[$key]['total'] =+ 1;
						$table_data2[$key]['saldo'] =+ $t->saldo;
					}
				}
			}

		return response()
		->json([
			'model' => $table_data2
		]);
	} 

	public function create()
	{
		return response()
			->json([
					'form' => JalinanKlaim::initialize(),
					'rules' => JalinanKlaim::$rules,
					'option' => []
			]);
	}


	public function store(Request $request)
	{
		$kelas = JalinanKlaim::create($request->all());

		$this->updateStatusAnggotaCu($request->anggota_cu_id, $request->tipe, $request->tanggal_mati);

		$this->storeStatusJalinan($kelas->id, $request->cu_id,0);
		
		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' berhasil ditambah'
			]);	
	}

	public function storeStatusJalinan($id, $cu, $status){
		JalinanKlaimStatus::create([
			'jalinan_klaim_id' => $id,
			'cu_id' => $cu,
			'status_klaim' => $status,
		]);
	}

	public function edit($nik, $cu, $tipe)
	{
		$kelas = JalinanKlaim::with('anggota_cu','anggota_cu.Villages','anggota_cu.Districts','anggota_cu.Regencies','anggota_cu.Provinces')->where('anggota_cu_cu_id', $cu)->where('tipe', $tipe)->whereNotIn('status_klaim',[2,3,4])->whereHas('anggota_cu', function($query) use ($nik){ 
			$query->where('nik',$nik); 
		})->first();

		if($kelas){
			return response()
				->json([
						'form' => $kelas,
						'option' => []
				]);
		}else{
			return $this->create();
		}
	}

	public function update(Request $request, $id)
	{
		$kelas = JalinanKlaim::findOrFail($id);

		$anggota_cu_id = $kelas->anggota_cu_id;

		$kelas->update($request->all());	

		$this->updateStatusAnggotaCu($anggota_cu_id, $request->tipe, $request->tanggal_mati, true);

		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' berhasil diubah'
			]);
	}

	public function updateStatus(Request $request, $id)
	{
		$kelas = JalinanKlaim::findOrFail($id);

		$kelas->status_klaim = $request->status;

		$anggota_cu_id = $kelas->anggota_cu_id;
		$tipe = $kelas->tipe;

		if($kelas->status_klaim == 1){
			$message = "Klaim JALINAN menunggu";
			$kelas->keterangan_klaim = NULL;
			$kelas->tunas_disetujui = NULL;
			$kelas->lintang_disetujui = NULL;
			$kelas->tanggal_pencairan = NULL;
		}else if($kelas->status_klaim == 2){
			$message = "Klaim JALINAN dokumen tidak lengkap";
			$kelas->keterangan_klaim = $request->keterangan_klaim;
			$kelas->tunas_disetujui = NULL;
			$kelas->lintang_disetujui = NULL;
			$kelas->tanggal_pencairan = NULL;
		}else if($kelas->status_klaim == 3){
			$message = "Klaim JALINAN ditolak";
			$kelas->keterangan_klaim = $request->keterangan_klaim;
			$kelas->tunas_disetujui = NULL;
			$kelas->lintang_disetujui = NULL;
			$kelas->tanggal_pencairan = NULL;
		}else if($kelas->status_klaim == 4){
			$message = "Klaim JALINAN disetujui";
			$kelas->keterangan_klaim = $request->keterangan_klaim;
			$kelas->tunas_disetujui = $request->tunas_disetujui;
			$kelas->lintang_disetujui = $request->lintang_disetujui;
			$kelas->tanggal_pencairan = $request->tanggal_pencairan;
		}
		
		$kelas->update();

		$this->updateStatusAnggotaCu($anggota_cu_id, $request->tipe, $request->tanggal_mati);
		
		$this->storeStatusJalinan($kelas->id, $request->cu_id, $request->status);
	
		return response()
			->json([
				'saved' => true,
				'message' => $message
			]);
	}

	public function updateSelesai(Request $request, $id)
	{
		$kelas = JalinanKlaim::findOrFail($id);

		if($kelas->status_klaim == '5'){
			$kelas->status_klaim = '6';
			$message = "Klaim JALINAN selesai";
		}else{
			$kelas->status_klaim = '5';
			$message = "Klaim JALINAN batal diselesaikan";
		}

		$kelas->update();
	
		return response()
			->json([
				'saved' => true,
				'message' => $message
			]);
	}

	public function updateVerifikasi(Request $request, $id)
	{
		$kelas = JalinanKlaim::findOrFail($id);

		if($request->can['verifikasi_pengurus_jalinan_klaim']){
			$kelas->verifikasi_pengurus = $request->id;
		}else if($request->can['verifikasi_pengawas_jalinan_klaim']){
			$kelas->verifikasi_pengawas = $request->id;
		}else if($request->can['verifikasi_manajemen_jalinan_klaim']){
			$kelas->verifikasi_manajemen = $request->id;
		}

		if(!empty($kelas->verifikasi_pengurus) && !empty($kelas->verifikasi_pengawas) && !empty($kelas->verifikasi_manajemen)){
			$kelas->status_klaim = '1';
		}

		$kelas->update();
	
		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' berhasil diverifikasi'
			]);
	}

	public function updateStatusAnggotaCu($id, $tipe, $tanggal, $isEdit = false){
		$kelas = AnggotaCu::findOrFail($id);
		$kelas->status_jalinan = $tipe;

		if($tipe == 'MENINGGAL'){
			$kelas->tanggal_meninggal = $tanggal;
			if($isEdit){
				if($kelas->tanggal_cacat == $tanggal){
					$kelas->tanggal_cacat = NULL;
				}
			}
		}else if($tipe == 'CACAT'){
			$kelas->tanggal_cacat = $tanggal;
			if($isEdit){
				if($kelas->tanggal_meninggal == $tanggal){
					$kelas->tanggal_meninggal = NULL;
				}
			}
		}
		
		$kelas->update();
	}

	public function UpdateCair($id, $awal, $akhir){
		if($awal != 'undefined' && $akhir != 'undefined'){
			$kelas = JalinanKlaim::with('anggota_cu_cu')->where('status_klaim',4)->whereBetween('tanggal_pencairan',[$awal, $akhir])->whereHas('anggota_cu_cu', function($query) use ($id){ 
				$query->where('cu_id',$id); 
			});	
		}else{
			$kelas = JalinanKlaim::with('anggota_cu_cu')->where('status_klaim',4)->where('tanggal_pencairan',$awal)->whereHas('anggota_cu_cu', function($query) use ($id){ 
				$query->where('cu_id',$id); 
			});	
		}

		$kelas->update(['status_klaim' => 5]);
		
		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' berhasil dicairkan'
			]);
	}

	public function UpdateCairBatal($id, $awal, $akhir){
		if($awal != 'undefined' && $akhir != 'undefined'){
			$kelas = JalinanKlaim::with('anggota_cu_cu')->where('status_klaim',5)->whereBetween('tanggal_pencairan',[$awal, $akhir])->whereHas('anggota_cu_cu', function($query) use ($id){ 
				$query->where('cu_id',$id); 
			});	
		}else{
			$kelas = JalinanKlaim::with('anggota_cu_cu')->where('status_klaim',5)->where('tanggal_pencairan',$awal)->whereHas('anggota_cu_cu', function($query) use ($id){ 
				$query->where('cu_id',$id); 
			});	
		}

		$kelas->update(['status_klaim' => 4]);
		
		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' berhasil dicairkan'
			]);
	}

	public function UpdateCairAll($awal, $akhir){
		if($awal != 'undefined' && $akhir != 'undefined'){
			$kelas = JalinanKlaim::where('status_klaim',4)->whereBetween('tanggal_pencairan',[$awal, $akhir])->update(['status_klaim' => 5]);	
		}else{
			$kelas = JalinanKlaim::where('status_klaim',4)->where('tanggal_pencairan',$awal)->update(['status_klaim' => 5]);	
		}
		
		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' berhasil dicairkan'
			]);
	}

	public function destroy($id)
	{
		$kelas = JalinanKlaim::findOrFail($id);
		$anggota_cu_id = $kelas->anggota_cu_id;

		$kelas->delete();

		$this->updateStatusAnggotaCu($anggota_cu_id, NULL, NULL);

		return response()
			->json([
				'deleted' => true,
				'message' => $this->message. ' berhasil dihapus'
			]);
	}

	public function count()
	{
			$table_data = JalinanKlaim::count();

			return response()
			->json([
					'model' => $table_data
			]);
	}

	public function history()
  {
    $time = \Carbon\Carbon::now()->subMonths(6);
		
    $table_data = Revision::with('revisionable')->where('revisionable_type','App\JalinanKlaim')->where('created_at','>=',$time)->orderBy('created_at','desc')->get();

    $history = collect();		
		foreach($table_data as $hs){
			$n = collect($hs);
			$n->put('user',$hs->userResponsible());
			$history->push($n);
		}

		return response()
			->json([
				'model' => $history
			]);
	}
	
	public function cariData($nik)
	{
		$table_data = AnggotaCu::with('anggota_cu_cu.cu','anggota_cu_cu.tp','anggota_produk_cu','Villages','Districts','Regencies','Provinces')->where('nik',$nik)->first();
		
		if($table_data){
			return response()
			->json([
				'model' => $table_data
			]);
		}else{
			return response()
			->json([
					'form' => AnggotaCu::initialize(),
					'rules' => AnggotaCu::$rules,
					'option' => []
			]);
		}
	}

	public function cekData($id){
		$kelas = JalinanKlaim::where('anggota_cu_id', $id)->first();
		
		if($kelas){
			return response()
				->json([
						'form' => $kelas,
						'option' => []
				]);
		}else{
			return $this->create();
		}
	}

	public function getPencairan()
	{
		$table_data = JalinanKlaim::where('status_klaim',4)->select('tanggal_pencairan')->distinct()->orderBy('tanggal_pencairan','DESC')->get();

		return response()
		->json([
			'model' => $table_data
		]);
	}
}