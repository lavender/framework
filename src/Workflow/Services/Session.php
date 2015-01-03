<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\MessageBag;
use Lavender\Workflow\Interfaces\SessionInterface;

class Session implements SessionInterface
{

    protected $session_id;

    public function getId()
    {
        return $this->session_id;
    }

    public function setId($id)
    {
        $this->session_id = $id;
    }

    public function success($message = null)
    {
        if(!$message) return \Session::pull($this->_key('_messages'));

        \Session::put($this->_key('_messages'), new MessageBag((array)$message));
    }

    public function get()
    {
        return \Session::get($this->_key());
    }

    public function put($data)
    {
        \Session::put($this->_key(), $data);
    }

    private function _key($suffix = null)
    {
        return "workflow_{$this->session_id}{$suffix}";
    }

}