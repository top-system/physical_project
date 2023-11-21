<?php

namespace App\Http\Controllers;

use App\Services\ApiFootballService;
use App\Services\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FootballController extends Controller
{
    public function getLeagues(Request $request){
        $api = ApiFootballService::getInstance();
        $response = $api->getLeagues();
        return response()->json($response);
    }

    /**
     * 获取赛事列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
                'stage_name',
                'match_live'
            ]);
        // 查询已经结束的 赛果
        if ($request->get('status') == 1){
            $eventDb = $eventDb->where('match_status','Finished');
        }
        // 查询正在进行中的
        elseif ($request->get('status') == 2){
            $eventDb = $eventDb->where('match_live',"1");
        }
        if ($request->has('date')){
            $eventDb = $eventDb->where('match_date',$request->get('date'));
        }else{
            $eventDb = $eventDb->where('match_date',date("Y-m-d"));
        }
        $response = $eventDb->get();
        return response()->json(['code' => 0, 'data' => $response]);
    }

    /**
     * 获取赛事详情
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEvent(Request $request, $id){
        $eventDb = DB::connection('mongodb')
            ->collection('events');
        $response = $eventDb->where('match_id', $id)->first();
        return response()->json(['code' => 0, 'data' => $response]);
    }

    /**
     * 获取分析（交锋）
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Google\Cloud\Core\Exception\ServiceException
     */
    public function getH2H(Request $request){
        if (!$request->has('firstTeamId')){
            return response()->json(['code' => 1, 'msg' => 'firstTeamId error']);
        }
        if (!$request->has('secondTeamId')){
            return response()->json(['code' => 1, 'msg' => 'secondTeamId error']);
        }
        $translate = Translate::getInstance();
        $api = ApiFootballService::getInstance();
        $response = $api->getH2H([
            'firstTeamId'   =>  $request->get('firstTeamId'),
            'secondTeamId'  =>  $request->get('secondTeamId')
        ]);

        foreach ($response['firstTeam_VS_secondTeam'] as &$resp){
            $resp['country_name'] = $translate->to($resp['country_name']);
            $resp['league_name'] = $translate->to($resp['league_name']);
            $resp['match_hometeam_name'] = $translate->to($resp['match_hometeam_name']);
            $resp['match_awayteam_name'] = $translate->to($resp['match_awayteam_name']);
        }
        foreach ($response['firstTeam_lastResults'] as &$resp){
            $resp['country_name'] = $translate->to($resp['country_name']);
            $resp['league_name'] = $translate->to($resp['league_name']);
            $resp['match_hometeam_name'] = $translate->to($resp['match_hometeam_name']);
            $resp['match_awayteam_name'] = $translate->to($resp['match_awayteam_name']);

        }
        foreach ($response['secondTeam_lastResults'] as &$resp){
            $resp['country_name'] = $translate->to($resp['country_name']);
            $resp['league_name'] = $translate->to($resp['league_name']);
            $resp['match_hometeam_name'] = $translate->to($resp['match_hometeam_name']);
            $resp['match_awayteam_name'] = $translate->to($resp['match_awayteam_name']);
        }
        return response()->json(['code' => 0, 'data' => $response]);
    }

    public function getLiveOddsCommnets(Request $request){
        $api = ApiFootballService::getInstance();
        $response = $api->getLiveOddsCommnets($request->get('match_id'));
        return response()->json(['code' => 0, 'data' => $response[$request->get('match_id')]]);
    }

    /**
     * 获取阵容详情
     * @param Request $request
     * @param $match_id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Google\Cloud\Core\Exception\ServiceException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLineups(Request $request, $match_id){
        $translate = Translate::getInstance();
        $api = ApiFootballService::getInstance();
        $response = $api->getLineups($match_id);
        return response()->json(['code' => 0, 'data' => $response]);
    }

    /**
     * 获取指数
     * @param Request $request
     * @param $match_id
     * @return void
     */
    public function getIndex(Request $request, $match_id){
        if(!$request->has('oddType')){
            return response()->json(['code' => 1, 'msg' => 'oddType error']);
        }

        switch ($request->get('oddType')){
            case '1':
                $pathStr = 'footballindex/handicap';
                break;
            case '2':
                $pathStr = 'footballindex/europeanindex';
                break;
            case '3':
                $pathStr = 'footballindex/totalgoals';
            default:
                $pathStr = '';
                break;
        }

        if ($pathStr == ''){
            return response()->json(['code' => 1, 'msg' => 'oddType error']);
        }

        $nodes = Storage::files($pathStr);

        $selectedNode = $this->SimpleConsistentHash($match_id, $nodes);
        $file = Storage::get($selectedNode);
        $data = json_decode($file,true);
        unset($data['result']);
        return response()->json(['code' => 0, 'data' => $data]);
    }

    public function simpleConsistentHash($key, $nodes, $replicas = 10) {
        $hashes = [];
        foreach ($nodes as $node) {
            for ($i = 0; $i < $replicas; $i++) {
                $hash = crc32($node . $i);
                $hashes[$hash] = $node;
            }
        }

        ksort($hashes);
        $keyHash = crc32($key);

        foreach ($hashes as $hash => $node) {
            if ($keyHash <= $hash) {
                return $node;
            }
        }

        // Wrap around case
        return reset($nodes);
    }
}
