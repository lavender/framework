<?php
namespace Lavender\View\Services;

use Illuminate\Support\MessageBag as Message;

class MessageBag
{

    /**
     * Get a message bag
     *
     * @param $type
     * @return mixed
     */
    private function _get($type = null)
    {
        return \Session::pull($this->_key($type), new Message([]));
    }

    /**
     * Add a message to a message bag
     *
     * @param $type
     * @param $message
     */
    private function _add($type, $message)
    {
        // get existing message bag or create a new one
        $messages = $this->_get($type);

        // merge our new messages into the message bag
        $messages->merge((array)$message);

        // put the message bag back into the session
        \Session::put($this->_key($type), $messages);
    }

    /**
     * @param null $type
     * @return string
     */
    private function _key($type = null)
    {
        if($type) return "cms.messages.{$type}";

        return "cms.messages";
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * Dynamically retrieve messages.
     *
     * @param  string $type
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $params = $params ? $params[0] : null;

        @list($method, $type) = uncamel($method);

        if(in_array($method, ['get', 'add'])){

            if(in_array($type, \Config::get('store.message_types'))){

                return call_user_func([new static, '_' . $method], $type, $params);

            } elseif($method == 'get' && $params){

                return call_user_func([new static, '_get'], $params);

            }
        }
    }
}