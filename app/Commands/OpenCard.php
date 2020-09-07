<?php

namespace App\Commands;

use Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Commands\Command;
use App\TelegramUser;
use App\Card;
use Log;

class OpenCard extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "opencard";

    /** @var string Command Argument Pattern */
    protected $pattern = '{card_id}';

    /**
     * @var string Command Description
     */
    protected $description = "Show your balance";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $text = "";
        $update = $this->getUpdate();

        if ($user = TelegramUser::find($update->getMessage()['chat']['id'])){
            if ($user->cards->count()>= 2){
                $text = 'Curently you can open only '.$user->cards->count().' cards';
            }
            else{
                $card = Card::Create(['name'=>'Card','balance'=>0,'currency'=>'USD','telegram_user_id'=>$user->id]);
                $user->save([$card]);
                $this->triggerCommand('cards');
            }
        }
        else{
            $text = "User not found";
            Log::error($text. " ". $update);
        }

        $this->replyWithMessage([
            'text'=>$text
        ]);
    }
}
