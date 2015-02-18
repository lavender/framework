<?php
namespace Lavender\Services\Workflow;

use Lavender\Exceptions\WorkflowException;

class Validator
{

    /**
     * Validate workflow forms
     *
     * @param array $fields
     * @param array $request
     * @throws WorkflowException
     */
    public function run(array $fields, array $request)
    {
        $rules = [];

        foreach($fields as $field => $data){

            if($data['validate']) $rules[$field] = $data['validate'];

        }

        $validator = \Validator::make($request, $rules);

        if ($validator->fails()){

            throw new WorkflowException("Validator failed", $validator);
        }
    }

}