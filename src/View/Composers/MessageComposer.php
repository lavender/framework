<?php
namespace Lavender\View\Composers;

class MessageComposer
{

    public function compose($view)
    {
        $messages = [];

        foreach(\Config::get('store.message_types') as $type){

            $message = \Message::get($type);

            if($message->getMessages()) $messages[$type] = $message;

        }

        $view->with('messages', $messages);
    }

}