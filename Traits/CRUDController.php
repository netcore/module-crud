<?php

namespace Modules\Crud\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Crud\Http\Requests\CRUDRequest;
use Illuminate\Database\Eloquent\Model;

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

        return $this->view('crud::index', [
            'model'     => $this->getModel(),
            'config'    => $this->getConfig(),
            'datatable' => $this->getModel()->getDatatableColumns(),
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
            'model' => $this->getModel(),
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
        $this->getModel()->create($request->all());

        return back()->withSuccess($this->getModel()->getClassName() . ' created successfully.');
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
            'model' => $this->getModel()->findOrFail($value),
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
            'model'  => $this->getModel()->findOrFail($value),
            'config' => $this->getConfig(),
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
        $model->update($request->all());

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
}