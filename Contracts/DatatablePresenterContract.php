<?php

namespace Modules\Crud\Contracts;

interface DatatablePresenterContract
{
    /**
     * Get the datatable columns config/mapping.
     *
     * @return array
     */
    public function getDatatableColumns(): array;

    /**
     * Get the list relations that should be eager loaded.
     *
     * @return array
     */
    public function eagerLoadableRelations(): array;

    /**
     * Get the columns that should not be escaped.
     *
     * @return array
     */
    public function getRawColumns(): array;
}