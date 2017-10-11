<?php

namespace Modules\Crud\Traits;

use Modules\Crud\Http\Requests\CRUDRequest;
use Illuminate\Database\Eloquent\Model;

trait CRUDController
{
    /**
     * Get model instance
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->view('crud::index', [
            'model' => $this->getModel(),
            'rows' => $this->getModel()->all()
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
            'model' => $this->getModel()
        ]);
    }

    /**
     * Store a newly created resource in storage.
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
            'model' => $this->getModel()->findOrFail($value)
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
            'model' => $this->getModel()->findOrFail($value)
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param $value
     * @param  CRUDRequest $request
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
     */
    public function destroy($value)
    {
        $model = $this->getModel()->findOrFail($value);
        $model->delete();

        return back()->withSuccess($this->getModel()->getClassName() . ' deleted successfully.');
    }

    /**
     * @param      $name
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

        return view($name,$variables);
    }
}