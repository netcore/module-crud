<?php

namespace Modules\Crud\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Crud\Http\Requests\CRUDRequest;
use Illuminate\Database\Eloquent\Model;
use Netcore\Translator\Helpers\TransHelper;
use Netcore\Translator\Models\Language;

trait CRUDController
{
    /**
     * Get model instance.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get config.
     *
     * @return mixed
     */
    public function getConfig()
    {
        return $this->model->crudConfig;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        session()->put('crud-active-model', get_class($this->getModel()));
        session()->put('crud-route-name', request()->route()->getName());

        $columns = [];

        //return dd($this->getModel()->getDatatableColumns());

        foreach ($this->getModel()->getDatatableColumns() as $field => $name) {
            if ($field === 'translations') {
                foreach ($name as $key => $translatableField) {
                    $columns[] = (object)[
                        'title'      => is_array($translatableField) ? array_get($translatableField, 'title', 'Unknown column') : $translatableField,
                        'data'       => is_array($translatableField) ? array_get($translatableField, 'data', $key) : $key,
                        'name'       => is_array($translatableField) ? array_get($translatableField, 'name', $key) : $key,
                        'orderable'  => false,
                        'searchable' => false,
                    ];
                }
            } else {
                $columns[] = (object)[
                    'title'      => is_array($name) ? array_get($name, 'title', 'Unknown column') : $name,
                    'data'       => is_array($name) ? array_get($name, 'data', $field) : $field,
                    'name'       => is_array($name) ? array_get($name, 'name', $field) : $field,
                    'orderable'  => is_array($name) ? (array_get($name, 'orderable', true) ? true : false) : true,
                    'searchable' => is_array($name) ? (array_get($name, 'searchable', true) ? true : false) : true
                ];
            }
        }

        return $this->view('crud::index', [
            'model'   => $this->getModel(),
            'config'  => $this->getConfig(),
            'columns' => collect($columns),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->view('crud::create', [
            'model'     => $this->getModel(),
            'languages' => Language::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CRUDRequest $request
     *
     * @return Response
     */
    public function store(CRUDRequest $request)
    {
        $model = $this->getModel();

        $isoCodes = [];
        if (property_exists($model, 'translationModel')) {
            $isoCodes = languages()->pluck('iso_code')->toArray();
        }

        $model = $model->create(array_except($request->all(), $isoCodes));

        if (property_exists($model, 'translationModel')) {
            $model->updateTranslations(array_only($request->all(), $isoCodes));
        }

        return back()->withSuccess($model->getClassName() . ' created successfully.');
    }

    /**
     * Show the specified resource.
     *
     * @param $value
     * @return \Illuminate\View\View
     */
    public function show($value)
    {
        return $this->view('crud::show', [
            'model' => $this->getModel()->findOrFail($value)->hideFields(['password']),
            'languages' => Language::get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $value
     * @return \Illuminate\View\View
     */
    public function edit($value)
    {
        return $this->view('crud::edit', [
            'model'     => $this->getModel()->findOrFail($value),
            'languages' => Language::get(),
            'config'    => $this->getConfig(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $value
     * @param CRUDRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CRUDRequest $request, $value)
    {
        $model = $this->getModel()->findOrFail($value);
        $languages = Language::get();
        $isoCodes = [];
        if (property_exists($model, 'translationModel')) {
            $isoCodes = $languages->pluck('iso_code')->toArray();
        }

        $model->update(array_except($request->all(), $isoCodes));

        if (property_exists($model, 'translationModel')) {
            $model->updateTranslations(array_only($request->all(), $isoCodes));
        }

        return back()->withSuccess($this->getModel()->getClassName() . ' saved successfully.');
    }

    /**
     * Delete CRUD record.
     *
     * @param $value
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($value)
    {
        $model = $this->getModel()->findOrFail($value);
        $model->delete();

        return back()->withSuccess($this->getModel()->getClassName() . ' deleted successfully.');
    }

    /**
     * Get view.
     *
     * @param $name
     * @param $variables
     *
     * @return \Illuminate\View\View
     */
    public function view($name, $variables = false)
    {
        $routeName = request()->route()->getName();

        if ($routeName && view()->exists($routeName)) {
            return view($routeName, $variables);
        }

        return view($name, $variables);
    }

    /**
     * Export model as an Excel/CSV document
     *
     * @param string $type
     */
    public function export($type = 'xls')
    {
        $tableName = $this->getModel()->getTable();
        $model = $this->getModel()->get();

        Excel::create(kebab_case($tableName . '_' . time()), function ($excel) use ($model, $tableName) {
            $excel->sheet(camel_case($tableName), function ($sheet) use ($model) {
                $sheet->fromModel($model);
            });
        })->export($type);
    }

    /**
     * A simple check, used in Unit test
     *
     * @return bool
     */
    public function isCrud(){
        return true;
    }
}