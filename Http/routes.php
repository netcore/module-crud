<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'namespace'  => 'Modules\Crud\Http\Controllers',
    'prefix'     => 'admin/crud',
    'as'         => 'crud::',
], function () {

    /**
     * Pagination for datatable.
     */
    Route::get('paginate', [
        'uses' => 'DatatableController@paginate',
        'as'   => 'paginate',
    ]);

});