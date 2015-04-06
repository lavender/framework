<?php
namespace Lavender\Http;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Lavender\Exceptions\FormException;

class FormRequest extends Request
{

    /**
     * Validate the class instance.
     *
     * @param array $fields
     * @throws FormException
     */
    public function validate(array $fields)
    {
        $this->flashInput($fields);

        $rules = $this->getValidationRules($fields);

        $validator = $this->getValidatorInstance($rules);

        if($validator->fails()){

            $this->failedValidation($validator);
        }
    }

    /**
     * Initialize the form request with data from the given request.
     *
     * @param  Request  $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $files = $request->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $this->initialize(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $files, $request->server->all(), $request->getContent()
        );

        if ($session = $request->getSession()){

            $this->setSession($session);

        }

        $this->setUserResolver($request->getUserResolver());

        $this->setRouteResolver($request->getRouteResolver());
    }

    /**
     * Flash only fields where flash = true
     *
     * @param array $fields
     */
    protected function flashInput(array $fields)
    {
        $flash = array_where($fields, function($key, $config){

            return $config['flash'];

        });

        Input::flashOnly(array_keys($flash));
    }

    /**
     * Get the validation rules for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidationRules(array $fields)
    {
        $rules = [];

        foreach($fields as $field => $data){

            if($data['validate']) $rules[$field] = $data['validate'];

        }

        return $rules;
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance($rules)
    {
        $factory = app('Illuminate\Validation\Factory');

        return $factory->make($this->all(), $rules);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator $validator
     * @return void
     * @throws FormException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new FormException("Validator failed", $validator);
    }


}