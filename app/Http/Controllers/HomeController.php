<?php

namespace App\Http\Controllers;

use App\Uniques;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => DB::table('uniques')->get(),
        ], 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        // try {
            foreach (array_chunk($input, 1000) as $data) {
                $existingDatas = DB::table('uniques')->whereIn('unique_code', $data)->get();

                if(count($existingDatas) == 0) {
                    foreach($data as $key => $value) {
                        // dd($value);
                        if (!DB::table('uniques')->insert($data)) throw new Exception("Error Processing Request", 1);
                    }
                } else {
                    $str = Str::random(6);

                    DB::table('uniques')->insert(['unique_code' => $str]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $input,
            ], 200);
        // } catch (\Exception $th) {
        //     $thFullMessage = $th->getMessage();
        //     $thMessage = substr($thFullMessage, 0, strpos($thFullMessage, ' ('));

        //     DB::rollBack();
        //     return response()->json([
        //         'success' => false,
        //         'message' => $thMessage,
        //         'data' => $input
        //     ]);
        // }
    }

    public function hasDupes($arr)
    {
        $arrCnt = $request->digit-count($arr);
        
        if($arrCnt != 0) {
            for($i = 1; $i <= $arrCnt; $i++) {
                $str = Str::random(6);

                $arr[] = ['unique_code' => $str];                
            }
        }
    }

    public function regenerate($arr)
    {
        // 
    }
}
