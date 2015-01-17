<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\Facades\Input;
use Lavender\Workflow\Exceptions\StateException;

class Validator
{

    /**
     * @param $request
     * @throws StateException
     */
    public function run($fields, $request)
    {
        // first we flash the input into session
        $this->flash($fields);

        // validate the request
        $this->validate($fields, $request);

    }
    /**
     * Flash only fields where flash = true
     *
     * @param $fields
     */
    private function flash($fields)
    {
        $flash = array_where($fields, function($key, $config){

            return $config['flash'];

        });

        Input::flashOnly(array_keys($flash));
    }

    private function validate($fields, $request)
    {
        $rules = [];

        foreach($fields as $field => $data){

            if($data['validate']) $rules[$field] = $data['validate'];

        }

        $validator = \Validator::make($request, $rules);

        if ($validator->fails()){

            throw new StateException("Validator failed", $validator);
        }
    }

}