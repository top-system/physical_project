<?php

namespace App\Services;

use App\Models\Language;
use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\Translate\V2\TranslateClient;

class Translate
{
    private static $_instance = null;
    private string $apiKey;
    private $langMap;
    private TranslateClient $translateClient;
    public function __construct(){
        $this->apiKey = env('TRANSLATE_TOKEN');
        $this->translateClient = new TranslateClient([
            'key' => $this->apiKey
        ]);
        $this->langMap = Language::where('code','zh')->pluck("value","key_name");
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
        if (isset($this->langMap[$text])){
            return $this->langMap[$text];
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
            }catch (\Exception $exception){

            }
            $this->langMap = Language::where('code','zh')->pluck("value","key_name");
            if (!isset($this->langMap[$text])){
                $this->langMap[$text] = $result['text'];
            }
        }
        return $result['text'];
    }
}
