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
    private function get($type = null)
    {
        return \Session::pull($this->_key($type), new Message([]));
    }

    /**
     * Add a message to a message bag
     *
     * @param $type
     * @param $message
     */
    private function add($type, $message)
    {
        // get existing message bag or create a new one
        $messages = $this->get($type);

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
        if($type) return "lavender.messages.{$type}";

        return "lavender.messages";
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
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $params = $params ? $params[0] : null;

        @list($method, $type) = $this->uncamel($method);

        if(in_array($method, ['get', 'add'])){

            if(in_array($type, \Config::get('store.message_types'))){

                return $this->$method($type, $params);

            } elseif($method == 'get' && $params){

                return $this->get($params);

            }
        }
    }

    private function uncamel($str)
    {
        return explode(',', preg_replace_callback('/([A-Z])/', function($c){
            return ','.strtolower($c[0]);
        }, $str));
    }
}