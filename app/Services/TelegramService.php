<?php

namespace App\Services;

use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    private static $_instance;

    public static function getInstance(){
        if (!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function sendMessage(){
        $this->telegram = Telegram::bot('mybot');
        $replyMarkup = new \stdClass();
        $replyMarkup->inline_keyboard = [];
        $inlineKeyboard = [[
            "text" => "点击确认",
//            "url" => "tg://inline?data=111",
            "callback_data" => "1"
        ]];
        $replyMarkup->inline_keyboard[] = $inlineKeyboard;
        $text = "<b>名字：</b>Theresa W Chavez \n<b>性别：</b>female \n<b>电话：</b>01 313-544-6952 \n<b>人数：</b>1 \n<b>邮箱：</b>ki5oqax0d9@payspun.com \n";
        $response = $this->telegram->sendMessage([
            'chat_id' => '-4035687211',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($replyMarkup),
            'text' => $text
        ]);

        $messageId = $response->getMessageId();
    }

    public function editMessage(){
        $this->telegram = Telegram::bot('mybot');
    }

}
