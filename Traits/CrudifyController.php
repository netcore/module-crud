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
     * Find single row
     *
     * @param $value
     * @return mixed
     */
    public function findRow($value)
    {
        return $this->getModel()->findOrFail($value);
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
            'model' => $this->findRow($value)
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
            'model' => $this->findRow($value)
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param $value
     * @param  CrudRequest $request
     */
    public function update(CrudRequest $request, $value)
    {
        //at moment this is cheating
        $model = $this->findRow($value);
        $model->update(
            $request->except('password')
        );

        //still cheating
        if( $request->get('password') ){
            $this->getModel()->password = bcrypt( $request->get('password') );
            $this->getModel()->save();
        }

        return redirect()->back()->withSuccess($this->getModel()->getClassName() . ' saved successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy()
    {

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

        if ( $routeName AND view()->exists($routeName) ) {
            return view($routeName, $variables);
        }

        return view($name,$variables);
    }
}