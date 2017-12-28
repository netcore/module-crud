<?php

namespace Modules\Crud\Http\Controllers;

use DataTables;
use Illuminate\Routing\Controller;
use Modules\Crud\Contracts\DatatablePresenterContract;

class DatatableController extends Controller
{
    /**
     * Model config.
     *
     * @var array
     */
    protected $config;

    /**
     * Prepare data for datatable.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paginate()
    {
        $model = session('crud-active-model');

        if (!$model || !class_exists($model)) {
            abort(404);
        }

        $model = app($model);
        $columns = $model->getDatatableColumns();
        $presenter = $model->getDatatablePresenter();

        $this->config = $model->crudConfig;

        if (class_exists($presenter) && app($presenter) instanceof DatatablePresenterContract) {
            $presenter = app($presenter);
        } else {
            $presenter = null;
        }

        $rawColumns = [];

        // Eager-load
        if ($presenter) {
            $query = $model->with($presenter->eagerLoadableRelations());
        } else {
            $query = $model->query();
        }

        $datatable = DataTables::of($query);

        if ($presenter) {
            $this->modifyDatatableColumns($datatable, $columns, $presenter);
            $rawColumns = $presenter->getRawColumns();
        }

        // Add action column
        $datatable->addColumn('actions', function ($row) {
            return view('crud::datatable._actions', [
                'row' => $row,
            ]);
        });

        $datatable->rawColumns(array_merge([
            'actions',
        ], $rawColumns));

        return $datatable->make(true);
    }

    /**
     * Modify datatable columns.
     *
     * @param $datatable
     * @param array $columns
     * @param $presenter
     * @return void
     */
    private function modifyDatatableColumns(&$datatable, array $columns, $presenter): void
    {
        foreach ($columns as $name => $title) {
            $method = camel_case($name) . 'Modifier';

            if (!method_exists($presenter, $method)) {
                continue;
            }

            $datatable->editColumn($name, [$presenter, $method]);
        }
    }
}