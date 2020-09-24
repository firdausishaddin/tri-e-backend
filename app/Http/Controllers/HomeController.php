<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {
            foreach (array_chunk($input, 1000) as $data) {
                if (!DB::table('uniques')->insert($data)) throw new Exception("Error Processing Request", 1);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $input
            ], 200);
        } catch (\Throwable $th) {
            $thFullMessage = $th->getMessage();
            $thMessage = substr($thFullMessage, 0, strpos($thFullMessage, ' ('));

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $thMessage,
                'data' => $input
            ]);
        }
    }
}
