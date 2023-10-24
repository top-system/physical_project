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
        $eventDb = DB::connection('mongodb')
            ->collection('events')
            ->select([
                'match_id',
                'country_id',
                'country_name',
                'league_id',
                'league_name',
                'match_date',
                'match_status',
                'match_time',
                'match_hometeam_id',
                'match_hometeam_name',
                'match_hometeam_score',
                'match_awayteam_name',
                'match_awayteam_id',
                'match_awayteam_score',
                'match_hometeam_halftime_score',
                'match_awayteam_halftime_score',
                'stage_name'
            ]);
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
    public function getEvent(Request $request, $id){
        $eventDb = DB::connection('mongodb')
            ->collection('events');
        $response = $eventDb->where('match_id', $id)->first();
        return response()->json(['code' => 0, 'data' => $response]);
    }
}
