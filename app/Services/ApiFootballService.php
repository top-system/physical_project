<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiFootballService{
    private static $_instance = null;
    private string $apiKey = '';

    private Client $client;

    private string $timezone = 'Asia/Shanghai';
    private string $gateway = 'https://apiv3.apifootball.com/';
    public function __construct(){
        $this->apiKey = env('FOOTBALL_TOKEN');
        $this->client = new Client(['base_uri' => $this->gateway]);
    }
    public static function getInstance(): ?ApiFootballService
    {
        if (self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 获取国家列表
     * @throws GuzzleException
     */
    public function getCountries(){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_countries',
                'APIkey'    =>  $this->apiKey,
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取比赛列表
     * @param $country_id 国家id
     * @return void
     * @throws GuzzleException
     */
    public function getLeagues($country_id=0){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_leagues',
                'APIkey'    =>  $this->apiKey,
                'country_id'=>  $country_id
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取团队列表
     * @param $league_id 联赛 ID - 如果未设置球队 ID，则必须提供联赛 ID
     * @param $team_id 球队 ID - 如果未设置联盟 ID，则必须输入球队 ID
     * @return void
     * @throws GuzzleException
     */
    public function getTeams($team_id = 0,$league_id = 0){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_teams',
                'APIkey'    =>  $this->apiKey,
                'team_id'   =>  $team_id,
                'league_id' =>  $league_id
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取玩家列表
     * @param $player_id    玩家 ID - 如果未设置玩家名称，则为必填项
     * @param $player_name  玩家姓名 - 如果未设置玩家 ID，则必须填写
     * @return void
     * @throws GuzzleException
     */
    public function getPlayers($player_id = 0, $player_name = 0){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_players',
                'APIkey'    =>  $this->apiKey,
                'player_id'   =>  $player_id,
                'player_name' =>  $player_name
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取积分榜
     * @param $league_id 联赛内部代码
     * @return void
     * @throws GuzzleException
     */
    public function getStands($league_id = 0){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_standings',
                'APIkey'    =>  $this->apiKey,
                'league_id'   =>  $league_id
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     *
     * @param $parameter
     * timezone 默认时区：Europe/Berlin。使用此过滤器，您可以设置要接收数据的时区。时区采用 TZ 格式（例如：America/New_York）。（选修的）
     * from 开始日期（年-月-日）
     * to   停止日期（年-月-日）
     * country_id   国家/地区 ID - 如果设置，则仅返回来自特定国家/地区的联赛（可选）
     * league_id    联盟 ID - 如果设置了来自特定联盟的事件，将返回（可选）
     * match_id     比赛 ID - 如果设置，则仅返回特定比赛的详细信息（可选）
     * team_id      团队 ID - 如果设置，则仅返回特定团队的详细信息（可选）
     * match_live   比分 - 如果 match_live=1 则仅返回现场比赛（可选）
     * withPlayerStats  withPlayerStats - 如果您想接收该场比赛的球员统计数据，您必须将此参数设置为任何值（例如您可以发送值“1”）（可选）
     * @return void
     * @throws GuzzleException
     */
    public function getEvents($parameter=[]){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_events',
                'APIkey'    =>  $this->apiKey,
                'timezone'  =>  $parameter['timezone'] ?? $this->timezone,
                'from'      =>  $parameter['from'] ?? date('Y-m-d'),
                'to'        =>  $parameter['to'] ?? date('Y-m-d'),
                'country_id'=>  $parameter['country_id']??0,
                'league_id' =>  $parameter['league_id']??0,
                'match_id'  =>  $parameter['match_id']??0,
                'team_id'   =>  $parameter['team_id']??0,
                'match_live'=>  $parameter['match_live']??0,
                'withPlayerStats'=>$parameter['withPlayerStats']??0,
            ]
        ]);

        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取阵容
     * @param $match_id 比赛编号
     * @return void
     * @throws GuzzleException
     */
    public function getLineups($match_id){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_lineups',
                'APIkey'    =>  $this->apiKey,
                'match_id'   =>  $match_id
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取阵容
     * @param $match_id 比赛编号
     * @return void
     * @throws GuzzleException
     */
    public function getStatistics($match_id){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_statistics',
                'APIkey'    =>  $this->apiKey,
                'match_id'   =>  $match_id
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取赔率
     * @param $from
     * @param $to
     * @param $match_id
     * @return void
     * @throws GuzzleException
     */
    public function getOdds($from, $to, $match_id){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_statistics',
                'APIkey'    =>  $this->apiKey,
                'match_id'   =>  $match_id,
                'from'      =>  $from,
                'to'        =>  $to
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取实时赔率和评论
     * @param $country_id 国家/地区 ID - 如果设置，则仅返回来自特定国家/地区的联赛（可选）
     * @param $league_id  联盟 ID - 如果设置了来自特定联盟的事件，将返回（可选）
     * @param $match_id   比赛 ID - 如果设置，则仅返回特定赛事的赔率（可选）
     * @return void
     * @throws GuzzleException
     */
    public function getLiveOddsCommnets($match_id='',$country_id='', $league_id=''){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_live_odds_commnets',
                'APIkey'    =>  $this->apiKey,
                'match_id'   =>  $match_id,
                'country_id' =>  $country_id,
                'league_id'  =>  $league_id
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);

    }

    public function getH2H($parameter=[]){
        $response = $this->client->get('',[
            'query' =>  [
                'action'    =>  'get_H2H',
                'APIkey'    =>  $this->apiKey,
                'timezone'  =>  $parameter['timezone'] ?? $this->timezone,
                'firstTeam' =>  $parameter['firstTeam'] ?? '',
                'secondTeam'=>  $parameter['secondTeam'] ?? '',
                'firstTeamId'=>  $parameter['firstTeamId'] ?? '',
                'secondTeamId'=>  $parameter['secondTeamId'] ?? '',
            ]
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }
}
