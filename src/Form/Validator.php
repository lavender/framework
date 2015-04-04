<?php
namespace Lavender\Form;

use Lavender\Exceptions\FormException;

class Validator
{

    /**
     * Validate form
     *
     * @param array $fields
     * @param array $request
     * @throws FormException
     */
    public function run(array $fields, array $request)
    {
        $rules = [];

        foreach($fields as $field => $data){

            if($data['validate']) $rules[$field] = $data['validate'];

        }

        $validator = \Validator::make($request, $rules);

        if ($validator->fails()){

            throw new FormException("Validator failed", $validator);
        }
    }

}