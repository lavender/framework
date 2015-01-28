<?php
namespace Lavender\Workflow\Services;

use Lavender\Workflow\Exceptions\StateException;

class Validator
{

    /**
     * Validate workflow forms
     *
     * @param array $fields
     * @param array $request
     * @throws StateException
     */
    public function run(array $fields, array $request)
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