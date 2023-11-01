<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Football599Service{
    private static $_instance = null;
    private string $apiKey = '';

    private Client $client;

    private string $lang = 'zh';

    private int $appType = 3;

    private string $gateway = 'https://api.599.com/footballDataBase/core/';
    public function __construct(){
        $this->apiKey = env('FOOTBALL_TOKEN');
        $this->client = new Client(['base_uri' => $this->gateway]);
    }
    public static function getInstance(): ?Football599Service
    {
        if (self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getSign($url){
        $url = str_replace("?","",$url);
        $url = str_replace("&","",$url);
        $url = str_replace("=","",$url);
        $url = "/footballDataBase/core/" . $url .md5('wjj');
        return md5($url) . 99;
    }

    /**
     * 获取联赛数据
     * @throws GuzzleException
     */
    public function getRace($leagueId){
        $query = [
            'lang'      => $this->lang,
            'appType'   =>  $this->appType,
            'leagueId'  =>  $leagueId,
            'season'    =>  '',
            'st'    =>  time() * 1000,
        ];
        $uri = "h5LeagueData.findH5LeagueRace.do";
        ksort($query);
        $url = $uri . http_build_query($query);
        $sign = $this->getSign($url);
        $query['sign'] = $sign;
        $response = $this->client->get($uri,[
            'query' =>  $query
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取积分数据
     * @throws GuzzleException
     */
    public function getScore($leagueId, $season){
        $query = [
            'lang'      => $this->lang,
            'appType'   =>  $this->appType,
            'leagueId'  =>  $leagueId,
            'season'    =>  '',
            'st'    =>  time() * 1000,
        ];
        $uri = "h5LeagueData.findH5LeagueScore.do";
        ksort($query);
        $url = $uri . http_build_query($query);
        $sign = $this->getSign($url);
        $query['sign'] = $sign;
        $response = $this->client->get($uri,[
            'query' =>  $query
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 获取最佳射手
     * @throws GuzzleException
     */
    public function getScorers($leagueId, $season){
        $query = [
            'lang'      => $this->lang,
            'appType'   =>  $this->appType,
            'leagueId'  =>  $leagueId,
            'season'    =>  '',
            'st'    =>  time() * 1000,
        ];
        $uri = "ranking.shooting.do";
        ksort($query);
        $url = $uri . http_build_query($query);
        $sign = $this->getSign($url);
        $query['sign'] = $sign;
        $response = $this->client->get($uri,[
            'query' =>  $query
        ]);
        return json_decode($response->getBody()->getContents(),true);
    }
}
