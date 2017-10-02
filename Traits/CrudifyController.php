<?php
namespace Modules\Crud\Traits;

use Illuminate\Http\Request;
use Modules\Crud\Http\Requests\CrudRequest;
use Illuminate\Database\Eloquent\Model;

trait CrudifyController
{
    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(CrudRequest $request)
    {
        $model = $this->model;
        $rows  = $model->all();

        return $this->view('crud::index', compact('model', 'rows'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(CrudRequest $request)
    {
        $model = $this->model;

        return $this->view('crud::create',compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(CrudRequest $request)
    {
        $this->model->create(
            $request->all()
        );

        return redirect()->back()->withSuccess( $this->model->getClassName() . ' created successfully.');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show(CrudRequest $request, $id)
    {
        $model = $this->model->findOrFail($id);

        return $this->view('crud::show',compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit(CrudRequest $request, $id)
    {
        $model = $this->model->findOrFail($id);

        return $this->view('crud::edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(CrudRequest $request, $id)
    {
        $model = $this->model->findOrFail($id);

        //at moment this is cheating
        $model->update(
            $request->except('password')
        );

        //still cheating
        if( $request->get('password') ){
            $model->password = bcrypt( $request->get('password') );
            $model->save();
        }

        return redirect()->back()->withSuccess( $model->getClassName() . ' saved successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    /**
     *
     */
    public function accessDenied()
    {
        return abort(403, 'No permissions');
    }

    /**
     * @param      $name
     * @param null $variables
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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