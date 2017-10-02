<?php

namespace Modules\Crud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrudRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $model = $this->route()->controller->getModel();


        //@TODO: šo iespējams noderīgi būtu iestrādāt kontrolierī vai kā helper
        $segments = explode('.',$this->route()->getName());

        $method = end($segments);

        $fields = [];

        if( in_array($method, ['store','update'] ) ){
            $fields = $model->getValidationFields($method);
        }

        return $fields;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //@TODO: jāpievieno permission modulis
        return auth()->user()->isAdmin();
    }
}
