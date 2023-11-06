<?php

namespace App\Services;

use App\Models\Language;
use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class Translate
{
    private static $_instance = null;
    private string $apiKey;
    private string $cacheKey = "_languages";
    private TranslateClient $translateClient;
    private $redis;
    public function __construct(){
        $this->apiKey = env('TRANSLATE_TOKEN');
        $this->translateClient = new TranslateClient([
            'key' => $this->apiKey
        ]);
        $this->redis = Redis::connection("default")->client();
    }
    public static function getInstance(): ?Translate
    {
        if (self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @throws ServiceException
     */
    public function to($text,$target='zh'){
        if ($text == ''){
            return $text;
        }
        $ref = $this->getLang($text);
        if ($ref){
            return $ref;
        }
        $result = $this->translateClient->translate($text,[
            'target' => $target
        ]);
        if (isset($result['text'])){
            try {
                Language::create([
                    'code'  =>  $target,
                    'key_name'  =>  $text,
                    'value' =>  $result['text']
                ]);
                $this->redis->hSet($this->cacheKey, $text, $result['text']);
            }catch (\Exception $exception){

            }
        }
        return $result['text'];
    }

    public function initCache(): void
    {
        $sum = 0;
        while (($rows = Language::where("id",">",$sum)->limit(1000)->get())){
            foreach ($rows as $lang){
                $this->redis->hSet($this->cacheKey, $lang['key_name'], $lang['value']);
            }
            $sum+=1000;
        }
    }

    private function getLang($key_name){
        // 查询缓存
        $ret = $this->redis->hGet($this->cacheKey, $key_name);
        if ($ret){
            return $ret;
        }
        // 查询数据库
        $ret = Language::pluck("value","key_name")
            ->where('code','zh')
            ->where('key_name',$key_name)
            ->first();
        if ($ret){
            $this->redis->hSet($this->cacheKey, $key_name, $ret['value']);
            return $ret['value'];
        }
        return false;

    }
}
