<?php

namespace App\Http\Controllers;

use App\Services\Football599Service;
use Illuminate\Http\Request;
use TopSystem\TopAdmin\Models\Post;

class DataStoreController extends Controller
{
    /**
     * 获取联赛数据
     * @return void
     */
    public function getRace(Request $request){
        $resp = Football599Service::getInstance()->getRace($request->get('leagueId'));
        if ($resp['code'] == 200){
            return ['code' => 0, 'data' => $resp['data']];
        }
        return ['code' => 1, 'msg' => '签名错误'];
    }

    /**
     * 获取积分数据
     * @return void
     */
    public function getScore(Request $request){
        $resp = Football599Service::getInstance()->getScore($request->get('leagueId'),$request->get('season'));
        if ($resp['code'] == 200){
            return ['code' => 0, 'data' => $resp['data']];
        }
        return ['code' => 1, 'msg' => '签名错误'];
    }

    /**
     * 获取最佳射手
     * @return void
     */
    public function getScorers(Request $request){
        $resp = Football599Service::getInstance()->getScorers($request->get('leagueId'),$request->get('season'));
        if ($resp['code'] == 200){
            return ['code' => 0, 'data' => $resp['data']];
        }
        return ['code' => 1, 'msg' => '签名错误'];
    }
}
