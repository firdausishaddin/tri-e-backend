<?php

namespace App\Http\Controllers;

use App\Uniques;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Arr;
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

        foreach (array_chunk($input, 50000) as $data) {

            // $existingDatas = DB::table('uniques')->whereIn('unique_code', $data)->get();

            // if(count($existingDatas) != 0) {
            //     $filteredArrays = Arr::get($data, 'unique_code', $existingDatas);

            //     if(!$filteredArrays) {
            //         if (!DB::table('uniques')->insert($data)) throw new Exception("Error Processing Request", 1);
            //     } else {
            //         foreach($filteredArrays as $filteredArray) {
            //             $str = Str::random(6);

            //             $test = array_replace([$filteredArray], ['unique_code' => $str]);

            //             DB::table('uniques')->insert(['unique_code' => $test['unique_code']]);
            //         }
            //     }
            //     DB::table('uniques')->insert($data);
            // }

            $this->hasDupes($data);
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

    public function hasDupes($data)
    {
        $existingDatas = DB::table('uniques')->whereIn('unique_code', $data)->get();
        if (count($existingDatas->toArray()) > 0) {
            $filteredArrays = Arr::get($data, 'unique_code', $existingDatas);

            foreach ($filteredArrays as $key => $filteredArray) {
                $str = Str::random(6);
                $test = array_replace([$filteredArrays[$key]], ['unique_code' => $str]);
                unset($data[$key]);
                DB::table('uniques')->insert(['unique_code' => $test['unique_code']]);
            }

            foreach ($data as $val => $c) {
                if ($c > 1) {
                    $str = Str::random(6);
                    unset($c);
                    DB::table('uniques')->insertOrIgnore(['unique_code' => $str]);
                }
            }
        } else {
            foreach ($data as $val => $c) {
                if ($c > 1) {
                    $str = Str::random(6);
                    unset($c);
                    DB::table('uniques')->insertOrIgnore(['unique_code' => $str]);
                }
            }
        }
    }
}
