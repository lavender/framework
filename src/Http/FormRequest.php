<?php
namespace Lavender\Http;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidatesWhenResolvedTrait;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;

class FormRequest extends Request implements ValidatesWhenResolved
{
    use ValidatesWhenResolvedTrait;


    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'default';

    /**
     * The input keys that should not be flashed on redirect.
     *
     * @var array
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {

    }




}