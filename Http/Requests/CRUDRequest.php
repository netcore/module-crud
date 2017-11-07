<?php

namespace Modules\Crud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Crud\Traits\CrudifyModel;

class CRUDRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var CrudifyModel $model */
        $model = $this->route()->controller->getModel();

        $userId = request()->route('user');
        if($userId) {
            $thisModel = $model->find($userId);
            $rules = $model->getValidationRules($thisModel);
        } else {
            $thisModel = null;
            $rules = $model->getValidationRules($thisModel);
        }

        $isStore = $userId ? false : true;
        if(isset($rules['password']) AND $isStore) {
            $rules['password'] .= '|required';
        }

        return $rules;
    }
}
