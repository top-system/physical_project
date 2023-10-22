<?php

namespace App\Http\Controllers;

use App\Services\ApiFootballService;
use App\Services\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FootballController extends Controller
{
    public function getLeagues(Request $request){
        $api = ApiFootballService::getInstance();
        $response = $api->getLeagues();
        return response()->json($response);
    }
    public function getEvents(Request $request){
        $eventDb = DB::connection('mongodb')->collection('events');
        // 查询已经结束的 赛果
        if ($request->get('status') == 1){
            $eventDb = $eventDb->where('match_status','Finished');
        }
        // 查询正在进行中的
        elseif ($request->get('status') == 2){
            $eventDb = $eventDb->where('match_date',date("Y-m-d"))
                ->where('match_status','!=','Finished')
                ->where('match_time','>',date("i:s"));
        }
        if ($request->get('date')){
            $eventDb = $eventDb->where('match_date',$request->get('date'));
        }
        $response = $eventDb->paginate(10);
        return response()->json($response);
    }
}
