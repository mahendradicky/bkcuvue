<?php
namespace App\Http\Controllers;

use DB;
use File;
use Image;
use App\AssesmentAccess;
use App\AssesmentAccessP1;
use App\AssesmentAccessP2;
use App\AssesmentAccessP3;
use App\AssesmentAccessP4;
use Illuminate\Http\Request;
use Venturecraft\Revisionable\Revision;

class AssesmentAccessController extends Controller{

	protected $message = "Self Assesment ACCESS Branding";

	public function index()
	{
		$table_data = AssesmentAccess::with('cu','p1','p2','p3','p4')->advancedFilter();

		return response()
		->json([
			'model' => $table_data
		]);
	}

	public function create()
	{
		$form = AssesmentAccess::initialize();
		$p1 = AssesmentAccessP1::initialize();
		$p2 = AssesmentAccessP2::initialize();
		$p3 = AssesmentAccessP3::initialize();
		$p4 = AssesmentAccessP4::initialize();

		$form['p1'] = (object)$p1;
		$form['p2'] = (object)$p2;
		$form['p3'] = (object)$p3;
		$form['p4'] = (object)$p4;

		return response()
			->json([
					'form' => $form,
					'rules' => AssesmentAccess::$rules,
					'option' => []
			]);
	}

	public function store(Request $request)
	{
		$this->validate($request,AssesmentAccess::$rules);

		$periode = $request->periode;

		$kelasP1 = AssesmentAccessP1::create($request->p1);
		$kelasP2 = AssesmentAccessP2::create($request->p2);
		$kelasP3 = AssesmentAccessP3::create($request->p3);
		$kelasP4 = AssesmentAccessP4::create($request->p4);

		$kelas = AssesmentAccess::create($request->all() + [
			'id_p1' => $kelasP1->id,
			'id_p2' => $kelasP2->id,
			'id_p3' => $kelasP3->id,
			'id_p4' => $kelasP4->id,
		]);
		
		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' ' .$periode. ' berhasil ditambah',
				'id' => $kelas->id
			]);	
	}

	public function edit($id)
	{
		$kelas = AssesmentAccess::with('cu','p1','p2','p3','p4')->findOrFail($id);

		return response()
				->json([
						'form' => $kelas,
						'option' => []
				]);
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, AssesmentAccess::$rules);

		$periode = $request->periode;

		$kelas = AssesmentAccess::findOrFail($id);
		$kelasP1 = AssesmentAccessP1::findOrFail($kelas->id_p1);
		$kelasP2 = AssesmentAccessP2::findOrFail($kelas->id_p2);
		$kelasP3 = AssesmentAccessP3::findOrFail($kelas->id_p3);
		$kelasP4 = AssesmentAccessP4::findOrFail($kelas->id_p4);

		$kelas->update($request->all());	
		$kelasP1->update($request->p1());	
		$kelasP2->update($request->p2());	
		$kelasP3->update($request->p3());	
		$kelasP4->update($request->p4());	

		return response()
			->json([
				'saved' => true,
				'message' => $this->message. ' ' .$periode. ' berhasil diubah'
			]);
	}

	public function destroy($id)
	{
		$kelas = AssesmentAccess::findOrFail($id);
		$kelasP1 = AssesmentAccessP1::findOrFail($kelas->id_p1);
		$kelasP2 = AssesmentAccessP2::findOrFail($kelas->id_p2);
		$kelasP3 = AssesmentAccessP3::findOrFail($kelas->id_p3);
		$kelasP4 = AssesmentAccessP4::findOrFail($kelas->id_p4);

		$periode = $kelas->periode;

		$kelas->delete();
		$kelasP1->delete();
		$kelasP2->delete();
		$kelasP3->delete();
		$kelasP4->delete();

		return response()
			->json([
				'deleted' => true,
				'message' =>  $this->message. ' ' .$name. 'berhasil dihapus'
			]);
	}

	public function count()
	{
			$table_data = AssesmentAccess::count();

			return response()
			->json([
					'model' => $table_data
			]);
	}

	public function history()
  {
    $time = \Carbon\Carbon::now()->subMonths(6);
		
    $table_data = Revision::with('revisionable')->where('revisionable_type','App\AssesmentAccess')->where('created_at','>=',$time)->orderBy('created_at','desc')->take(5);

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
}