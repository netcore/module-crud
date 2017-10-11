<?php

namespace Modules\Crud\Traits;

use Modules\Crud\Http\Requests\CrudRequest;
use Illuminate\Database\Eloquent\Model;

trait CrudifyController
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
     * @param  CrudRequest $request
     *
     * @return Response
     */
    public function store(CrudRequest $request)
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
     * @param  CrudRequest $request
     */
    public function update(CrudRequest $request, $value)
    {
        $model = $this->getModel()->findOrFail($value);
        $model->update($request->all());

        return back()->withSuccess($this->getModel()->getClassName() . ' saved successfully.');
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