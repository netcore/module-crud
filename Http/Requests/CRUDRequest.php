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
        $thisModel = $model->whereEmail($this->email)->first();

        return $model->getValidationRules($thisModel);
    }
}
