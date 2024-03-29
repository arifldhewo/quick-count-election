<?php

namespace App\Http\Controllers;

use App\Models\DataLengkap;
use App\Models\CalegGroup;
use App\Models\SuaraGroup;
use App\Models\MasterKecamatan;
use App\Models\MasterKelurahan;
use App\Models\MasterPartai;
use App\Models\MasterCaleg;
use Carbon\Carbon;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataLengkapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('details.index', [

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(DataLengkap  $dataLengkap){
    
        return view('details.create', [
            'kecamatans' => MasterKecamatan::all(),
            'kelurahans' => $dataLengkap->fetchKelurahan()->toJson(),
            'calegs' => $dataLengkap->fetchCaleg()->toJson(),
            'partais' => MasterPartai::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'kecamatan_id'        => 'required|numeric',
            'kelurahan_id'        => 'required|numeric',
            'rw'                  => 'required|numeric|gte:0',
            'rt'                  => 'required|numeric|gte:0',
            'no_tps'              => 'required|numeric|gte:0',
            'total_dpt'           => 'required|numeric|gte:0',
            'total_sss'           => 'required|numeric|gte:0',
            'total_ssts'          => 'required|numeric|gte:0',
            'total_ssr'           => 'required|numeric|gte:0',
            'pemilih_hadir'       => 'required|numeric|gte:0',
            'pemilih_tidak_hadir' => 'required|numeric|gte:0',
            'partai_id'           => 'required|numeric',
            'image'               => 'required|image|max:10240'
        ]);

        $jmlCaleg = MasterCaleg::select('id')->where('partai_id', $request->partai_id)->count();;
        for($i = 1; $i <= $jmlCaleg; $i++){
            $request->validate([
                "caleg$i" => 'required|string',
                "suara$i" => 'required|numeric|gte:0'
            ]);
        }

        $validatedCaleg = $request->except(
            '_token', 'kecamatan_id', 'rw', 'rt', 'total_dpt', 'total_sss', 'total_ssts', 'total_ssr', 'pemilih_hadir', 'image', 'agree',
            'pemilih_tidak_hadir', 'suara1', 'suara2', 'suara3', 'suara3', 'suara4', 'suara5', 'suara6', 'suara7', 'suara8', 'suara9', 'suara10'  
        );

        CalegGroup::create($validatedCaleg);

        $validatedSuara = $request->except(
            '_token', 'kecamatan_id', 'rw', 'rt', 'total_dpt', 'total_sss', 'total_ssts', 'total_ssr', 'pemilih_hadir', 'image', 'agree',
            'pemilih_tidak_hadir', 'caleg1', 'caleg2', 'caleg3', 'caleg3', 'caleg4', 'caleg5', 'caleg6', 'caleg7', 'caleg8', 'caleg9', 'caleg10'  
        );

        SuaraGroup::create($validatedSuara);

        $validatedData = Validator::make($request->all(), [
            'kecamatan_id'        => 'required|numeric',
            'kelurahan_id'        => 'required|numeric',
            'rw'                  => 'required|numeric|gte:0',
            'rt'                  => 'required|numeric|gte:0',
            'no_tps'              => 'required|numeric|gte:0',
            'total_dpt'           => 'required|numeric|gte:0',
            'total_sss'           => 'required|numeric|gte:0',
            'total_ssts'          => 'required|numeric|gte:0',
            'total_ssr'           => 'required|numeric|gte:0',
            'pemilih_hadir'       => 'required|numeric|gte:0',
            'pemilih_tidak_hadir' => 'required|numeric|gte:0',
            'partai_id'           => 'required|numeric',
            'image'               => 'required|image|max:10240'
        ]);

        if($validatedData->fails()){

            CalegGroup::where('no_tps', $request->no_tps)
            ->where('kelurahan_id', $request->kelurahan_id)
            ->where('partai_id', $request->partai_id)
            ->delete();

            SuaraGroup::where('no_tps', $request->no_tps)
            ->where('kelurahan_id', $request->kelurahan_id)
            ->where('partai_id', $request->partai_id)
            ->delete();

            return redirect()->route('dataLengkapCreate')
            ->withErrors($validatedData)
            ->withInput();
        }else{
            $validatedData = $validatedData->validate();
            
            $validatedData['uuid'] = Str::uuid();
            
            $validatedData['user_id'] = auth()->user()->id;

            $validatedData['caleg_group_id'] = CalegGroup::select('id')
            ->where('no_tps', $validatedData['no_tps'])
            ->where('kelurahan_id', $validatedData['kelurahan_id'])
            ->where('partai_id', $validatedData['partai_id'])
            ->value('id');

            $validatedData['suara_group_id'] = SuaraGroup::select('id')
            ->where('no_tps', $validatedData['no_tps'])
            ->where('kelurahan_id', $validatedData['kelurahan_id'])
            ->where('partai_id', $validatedData['partai_id'])
            ->value('id');

            $kecamatan_name = MasterKecamatan::select('name')->where('id', $request->kecamatan_id)->value('name');
            $kelurahan_name = MasterKelurahan::select('name')->where('id', $request->kelurahan_id)->value('name');

            $file_ext = $request->file('image')->getClientOriginalExtension();
            $validatedData['image'] = $request->file('image')->storeAs('plano', $kecamatan_name . "_" . $kelurahan_name . "_tps_" . $request->no_tps . "_" . Str::random(9) . "." . $file_ext);

            DataLengkap::create($validatedData);

            return redirect()->route('dataLengkap')->with('success', 'Your data has been added successfully!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DataLengkap $dataLengkap, $id)
    {
        return redirect()->route('dataLengkap');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataLengkap $dataLengkap, $id)
    {
        return view('details.edit', [
            'post' => $dataLengkap->fetchDataLengkap($id),
            'kecamatans' => MasterKecamatan::all(),
            'kelurahans' => $dataLengkap->fetchKelurahan()->toJson(),
            'calegs' => $dataLengkap->fetchCaleg()->toJson(),
            'partais' => MasterPartai::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataLengkap $dataLengkap, $id)
    {

        $request->validate([
            'kecamatan_id'        => 'required|numeric',
            'kelurahan_id'        => 'required|numeric',
            'rw'                  => 'required|numeric|gte:0',
            'rt'                  => 'required|numeric|gte:0',
            'no_tps'              => 'required|numeric|gte:0',
            'total_dpt'           => 'required|numeric|gte:0',
            'total_sss'           => 'required|numeric|gte:0',
            'total_ssts'          => 'required|numeric|gte:0',
            'total_ssr'           => 'required|numeric|gte:0',
            'pemilih_hadir'       => 'required|numeric|gte:0',
            'pemilih_tidak_hadir' => 'required|numeric|gte:0',
            'partai_id'           => 'required|numeric',
        ]);

        $oldData = $dataLengkap->where('uuid', $id)->first();

        if(array_key_exists('caleg1', $request->all())){
            $jmlCaleg = MasterCaleg::select('id')->where('partai_id', $request->partai_id)->count();;
            for($i = 1; $i <= $jmlCaleg; $i++){
                $request->validate([
                    "caleg$i" => 'required|string',
                    "suara$i" => 'required|numeric|gte:0'
                ]);
            }
        }

        $validatedCaleg = $request->except(
            '_token', '_method', 'kecamatan_id', 'rw', 'rt', 'total_dpt', 'total_sss', 'total_ssts', 'total_ssr', 'pemilih_hadir', 'image', 'agree',
            'pemilih_tidak_hadir', 'suara1', 'suara2', 'suara3', 'suara3', 'suara4', 'suara5', 'suara6', 'suara7', 'suara8', 'suara9', 'suara10'  
        );

        CalegGroup::where('no_tps', $oldData->no_tps)
                ->where('kelurahan_id', $oldData->kelurahan_id)
                ->where('partai_id', $oldData->partai_id)
                ->update($validatedCaleg);

        $validatedSuara = $request->except(
            '_token', '_method', 'kecamatan_id', 'rw', 'rt', 'total_dpt', 'total_sss', 'total_ssts', 'total_ssr', 'pemilih_hadir', 'image', 'agree',
            'pemilih_tidak_hadir', 'caleg1', 'caleg2', 'caleg3', 'caleg3', 'caleg4', 'caleg5', 'caleg6', 'caleg7', 'caleg8', 'caleg9', 'caleg10'  
        );

        SuaraGroup::where('no_tps', $oldData->no_tps)
                ->where('kelurahan_id', $oldData->kelurahan_id)
                ->where('partai_id', $oldData->partai_id)
                ->update($validatedSuara);

        $validatedData = $request->validate([
            'kecamatan_id'        => 'required|numeric',
            'kelurahan_id'        => 'required|numeric',
            'partai_id'           => 'required|numeric',
            'rw'                  => 'required|numeric|gte:0',
            'rt'                  => 'required|numeric|gte:0',
            'no_tps'              => 'required|numeric|gte:0',
            'total_dpt'           => 'required|numeric|gte:0',
            'total_sss'           => 'required|numeric|gte:0',
            'total_ssts'          => 'required|numeric|gte:0',
            'total_ssr'           => 'required|numeric|gte:0',
            'pemilih_hadir'       => 'required|numeric|gte:0',
            'pemilih_tidak_hadir' => 'required|numeric|gte:0',
            'image'               => '|file|image|max:10240'
        ]);

        $validatedData['caleg_group_id'] = CalegGroup::select('id')
        ->where('no_tps', $request->no_tps)
        ->where('kelurahan_id', $request->kelurahan_id)
        ->where('partai_id', $request->partai_id)
        ->value('id');

        $validatedData['suara_group_id'] = SuaraGroup::select('id')
        ->where('no_tps', $request->no_tps)
        ->where('kelurahan_id', $request->kelurahan_id)
        ->where('partai_id', $request->partai_id)
        ->value('id');

        if (array_key_exists('image', $validatedData)) {
            $post = $dataLengkap->where('uuid', $id)->first();

            Storage::delete($post->image);

            $kecamatan_name = MasterKecamatan::select('name')->where('id', $request->kecamatan_id)->value('name');
            $kelurahan_name = MasterKelurahan::select('name')->where('id', $request->kelurahan_id)->value('name');

            $file_ext = $request->file('image')->getClientOriginalExtension();
            $validatedData['image'] = $request->file('image')->storeAs('plano', $kecamatan_name . "_" . $kelurahan_name . "_tps_" . $request->no_tps . "_" . Str::random(9) . "." . $file_ext);
        }

        $dataLengkap->where('uuid', $id)->update($validatedData);

        return redirect()->route('dataLengkap')->with('success', 'Your data has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataLengkap $dataLengkap, $id)
    {
        $post = $dataLengkap->where('id', $id)->first();

        Storage::delete($post->image);

        DataLengkap::where('id', '=', $id)->delete();

        return redirect()->route('dataLengkap')->with('success', 'Your data has been deleted successfully!');
    }

    public function spatie()
    {
        $rows = [];

        DataLengkap::query()->selectRaw('
        data_lengkaps.id,
        data_lengkaps.uuid,
        users.name AS pengirim,
        users.telp,
        master_kecamatans.name AS kecamatan,
        master_kelurahans.name AS kelurahan,
        data_lengkaps.rt,
        data_lengkaps.rw,
        data_lengkaps.no_tps,
        data_lengkaps.total_dpt,
        data_lengkaps.total_sss,
        data_lengkaps.total_ssts,
        data_lengkaps.total_ssr,
        data_lengkaps.pemilih_hadir,
        data_lengkaps.pemilih_tidak_hadir,
        master_partais.name AS partai,
        data_lengkaps.image,
        data_lengkaps.created_at,
        data_lengkaps.updated_at,
        caleg_groups.caleg1,
        suara_groups.suara1,
        caleg_groups.caleg2,
        suara_groups.suara2,
        caleg_groups.caleg3,
        suara_groups.suara3,
        caleg_groups.caleg4,
        suara_groups.suara4,
        caleg_groups.caleg5,
        suara_groups.suara5,
        caleg_groups.caleg6,
        suara_groups.suara6,
        caleg_groups.caleg7,
        suara_groups.suara7,
        caleg_groups.caleg8,
        suara_groups.suara8,
        caleg_groups.caleg9,
        suara_groups.suara9,
        caleg_groups.caleg10,
        suara_groups.suara10
        ')
        ->join('users', 'data_lengkaps.user_id', 'users.id')
        ->join('master_kecamatans', 'data_lengkaps.kecamatan_id', 'master_kecamatans.id')
        ->join('master_kelurahans', 'data_lengkaps.kelurahan_id', 'master_kelurahans.id')
        ->join('caleg_groups', 'data_lengkaps.caleg_group_id', 'caleg_groups.id')
        ->join('suara_groups', 'data_lengkaps.suara_group_id', 'suara_groups.id')
        ->join('master_partais', 'data_lengkaps.partai_id', 'master_partais.id')
        ->chunk(2000, function($dataLengkaps) use (&$rows){
            foreach ($dataLengkaps->toArray() as $dataLengkap) {
                $dataLengkap['created_at'] = Carbon::parse($dataLengkap['created_at'])->tz('Asia/Jakarta')->format("d-M-Y H:i:s");
                $dataLengkap['updated_at'] = Carbon::parse($dataLengkap['updated_at'])->tz('Asia/Jakarta')->format("d-M-Y H:i:s");
                $rows[] = $dataLengkap;
            }
        });

        SimpleExcelWriter::streamDownload('dataLengkap.csv')
            ->noHeaderRow()
            ->addRows($rows);
    }
}
