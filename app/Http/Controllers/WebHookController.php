<?php

namespace App\Http\Controllers;

use App\Models\MessageRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebHookController extends Controller
{
    public function index(Request $request,$token){
        // 回调点击
        if ($request->get('callback_query')){
            $callback_query = $request->get('callback_query');
            $message_id = $callback_query['message']['message_id'];
            $record = MessageRecord::where('message_id', $message_id)->first();
            if ($record->user_id > 0){
//                return false;
            }
            $from = $callback_query['from'];
            $record->user_id = $from['id'];
            $record->username = $from['username'];
            $record->save();
            Log::info($request->all());

            $replyMarkup = new \stdClass();
            $replyMarkup->inline_keyboard = [];
            $inlineKeyboard = [[
                "text" => "已被认领",
                "callback_data" => "1"
            ]];
            $replyMarkup->inline_keyboard[] = $inlineKeyboard;
            $res = Telegram::bot('mybot')->editMessageReplyMarkup([
                'chat_id' => '-4035687211',
                'message_id' => $message_id,
                'reply_markup' => json_encode($replyMarkup),
            ]);
            Log::info($res);
        }
    }
}
