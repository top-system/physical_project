<?php

namespace App\Http\Controllers;
use App\Models\MessageRecord;
use App\Services\ContentService;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{
    protected $telegram;

    /**
     * Create a new controller instance.
     *
     * @param  Api  $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function body(){
        $con = new ContentService();
        var_dump($con->generate());
    }

    /**
     * Show the bot information.
     */
    public function show()
    {
//        Telegram::bot('mybot')->deleteWebhook();
        $response = Telegram::bot('mybot')->getUpdates([]);
        return $response;
    }

    public function sendMessage()
    {
        $this->telegram = Telegram::bot('mybot');
        $response = Telegram::setWebhook([
            'url' => env('TELEGRAM_WEBHOOK_URL')
        ]);
        var_dump($response);
        $replyMarkup = new \stdClass();
        $replyMarkup->inline_keyboard = [];
        $inlineKeyboard = [[
            "text" => "点击确认",
            "callback_data" => "1"
        ]];
        $replyMarkup->inline_keyboard[] = $inlineKeyboard;
        $cs = new ContentService();
        $userInfo = $cs->generate();
        $body = "<b>Name：</b>" . $userInfo['name'] . " \n<b>Sex：</b>" . $userInfo['sex'] . " \n<b>Phone：</b>" . $userInfo['mobile'] . " \n<b>Number of people：</b>1 \n<b>Mail：</b>" . $userInfo['mail'] . " \n";
        $response = $this->telegram->sendMessage([
            'chat_id' => '-4035687211',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($replyMarkup),
            'text' => $body
        ]);

        $messageId = $response->getMessageId();
        MessageRecord::create([
            'message_id'    =>  $messageId,
            'msg_body'  => $body
        ]);
    }

    public function editMessage()
    {
        $this->telegram = Telegram::bot('mybot');
        $replyMarkup = new \stdClass();
        $replyMarkup->inline_keyboard = [];
        $inlineKeyboard = [[
            "text" => "点击确认",
            "url" => "https://326b-2409-8a00-17b-5eb0-1c90-c86d-a33f-d6ab.ngrok-free.app/test",
            "callback_data" => "1"
        ]];
        $replyMarkup->inline_keyboard[] = $inlineKeyboard;
        $response = $this->telegram->sendMessage([
            'chat_id' => '-4035687211',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($replyMarkup),
            'text' => "<b>名字：</b>Theresa W Chavez \n
<b>性别：</b>female \n
<b>电话：</b>01 313-544-6952 \n
<b>人数：</b>1 \n
<b>邮箱：</b>ki5oqax0d9@payspun.com \n"
        ]);

        $messageId = $response->getMessageId();
        var_dump(json_encode($replyMarkup));
        var_dump($messageId);die;
    }

    public function setWebhook()
    {
        $this->telegram = Telegram::bot('mybot');
        $response = $this->telegram->setWebhook(['url' => "https://326b-2409-8a00-17b-5eb0-1c90-c86d-a33f-d6ab.ngrok-free.app/" . $this->telegram->getAccessToken() . '/webhook']);
        var_dump($response);die;
    }

}
