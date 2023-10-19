<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ContentService{
    public function getTempMail(){
        $mailDo = array('@iffymedia.com', '@linshiyouxiang.net', '@payspun.com', '@claimab.com', '@thrubay.com');
        $mailStr = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 10);
        $mailIndex = rand(0, count($mailDo) - 1);
        return $mailStr . $mailDo[$mailIndex];
    }
    public function generate(){
        $client = new Client();
        $response = $client->get("https://www.shenfendaquan.com/");
        $body = $response->getBody()->getContents();
        $crawler = new Crawler();
        $crawler->addHtmlContent($body);
        $name = $crawler->filterXPath('//input[5][@type="text"]/@value')->html();
        $sex = $crawler->filterXPath('//input[6][@type="text"]/@value')->html();
        $mobile = $crawler->filterXPath('//input[15][@type="text"]/@value')->html();
        $mail = $this->getTempMail();
        return [
            'name'  =>  $name,
            'sex'   =>  $sex,
            'mobile'=>  $mobile,
            'mail'  =>  $mail
        ];
    }
}
