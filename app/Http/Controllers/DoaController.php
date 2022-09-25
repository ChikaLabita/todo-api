<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doa;
use Illuminate\Support\Facades\Http;

class DoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$doa = Doa::oderBy('id', 'DESC')->paginate(5);
        $response = [
            'message' => 'Data Doa',
            'data'  => $doa
        ];
        return response()->json($response, HttpFoundationResponse::HTTP_OK);
        */
        $response = Http::get('https://api.kawalcorona.com/indonesia/provinsi');
        //$response = Http::get('https://api-alquranid.herokuapp.com/surah');
        $data = $response->json();
        return view('index',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => ['required'],
            "doa" => ['required'],
            "ayat" => ['required'],
            "latin" => ['required'],
            "artinya" => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $doa = Doa::create($request->all());

            $response = [
                'message' => 'Berhasil disimpan',
                'data' => $doa,
            ];

            return response()->json($response, HttpFoundationResponse::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Gagal " . $e->errorInfo,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $doa = Doa::where('id', $id)->firstOrFail();
        if (is_null($doa)) {
            return $this->sendError('Doa tidak diemukan');
        }
        return response()->json([
            "success" => true,
            "message" => "Doa ditemukan.",
            "data" => $doa,
        ]);
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
        $doa = Doa::find($id);
        $doa->update($request->all());
        return response()->json([
            "success" => true,
            "message" => "Doa telah diubah.",
            "data" => $doa,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deletedRows = Doa::where('id', $id)->delete();
        return response()->json([
            "success" => true,
            "message" => "Data Buku berhasil dihapus.",
            "data" => $deletedRows,
        ]);
    }
}