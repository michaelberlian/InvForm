<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockControllerApi extends Controller
{
    //

    public function create (Request $request){
        // return response(["data" => $request->all()]);
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        DB::table($tableName)->insert([
            'Name' => $request->name,
            'Quantity' => $request->quantity,
            'Description' => $request->description,
            'created_at' => $ldate,
            ]
        );

        return response(["message"=>'data succesfully added']);
    }

    public function view (Request $request){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        $stockList = DB::table($tableName)
            ->where('Name', 'like', '%'.$request->name.'%')
            ->where('Quantity', '>', '%'.$request->quantity.'%')
            ->where('Description', 'like', '%'.$request->description.'%')
            ->get();

        return response (['data'=>$stockList]);
    }

    public function edit (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        $selected = DB::table($tableName)
            ->where('id', $id)
            ->get();

        return response (['data'=>$selected]);
    }

    public function update (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        $selected = DB::table($tableName)
            ->where('id', $id);
        
        if (! $selected->exists()){
            return response(['message' => 'data is unavailable']);
        }
        $selected ->update([
                'Name' => $request->name,
                'Quantity' => $request->quantity,
                'Description' => $request->description,
                'updated_at' => $ldate,
            ]);
        return (['message' => 'data edited succesfully']);
    }

    public function delete (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        $selected = DB::table($tableName)
            ->where('id', $id);
        
        if (! $selected->exists()){
            return response(['message' => 'data is unavailable']);
        }
        
        $selected->delete();
        return response(['message' => 'data deleted succesfully']);
    }

}
