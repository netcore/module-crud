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
            $rawColumns = $presenter->getRawColumns();

            $rawColumns = array_merge($this->modifyDatatableColumns($datatable, $columns, $presenter), $rawColumns);
        } else {
            if ($model->isTranslatable()) {
                foreach ($model->translatedAttributes as $field) {
                    $datatable->editColumn($field, function ($row) use ($field) {
                        return view('crud::datatable._translatable-field', [
                            'row'   => $row,
                            'field' => $field,
                        ]);
                    });

                    $rawColumns[] = $field;
                }
            }

            if ($model->hasAttachments()) {
                foreach ($model->getAttachedFiles() as $name => $file) {
                    $datatable->editColumn($name . '_file_name', function ($row) use ($name) {
                        return view('crud::datatable._file-field', [
                            'row'   => $row,
                            'field' => $name,
                        ]);
                    });

                    $rawColumns[] = $name . '_file_name';
                }
            }
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
     * @return array
     */
    private function modifyDatatableColumns(&$datatable, array $columns, $presenter)
    {
        $rawColumns = [];

        foreach ($columns as $name => $title) {
            $method = camel_case($name) . 'Modifier';

            if ($name === 'translations') {
                foreach ($title as $field => $subName) {
                    $method = camel_case('translation_' . $field) . 'Modifier';

                    if (!method_exists($presenter, $method)) {
                        $datatable->editColumn($field, function ($row) use ($field) {
                            return view('crud::datatable._translatable-field', [
                                'row'   => $row,
                                'field' => $field,
                            ]);
                        });

                        $rawColumns[] = $field;

                        continue;
                    }

                    $datatable->editColumn($field, [$presenter, $method]);
                }
            } else {
                if (!method_exists($presenter, $method)) {
                    continue;
                }

                $datatable->editColumn($name, [$presenter, $method]);
            }
        }

        return $rawColumns;
    }
}