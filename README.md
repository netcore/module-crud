# Netcore CMS - CRUD module.

## Pre-installation 
This package is part of Netcore CMS ecosystem and is only functional in a project that has following packages installed:

https://github.com/netcore/netcore
https://github.com/netcore/module-admin
https://github.com/nWidart/laravel-modules

## Installation

```bash
composer require netcore/module-crud
```

You need to add `CRUDModel` and `CRUDController` trait to your model and controller.

Controller:
```php 
<?php

namespace App\Http\Controllers;

use App\Article;
use Modules\Crud\Traits\CRUDController;

class ArticlesController extends Controller
{
    use CRUDController; // <- This trait is required.

    /**
     * CRUD model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * ArticlesController constructor.
     */
    public function __construct()
    {
        $this->model = app(Article::class); // <- Set model.
    }
}
```

Model:
```php 
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Modules\Crud\Traits\CRUDModel;

class Article extends Model
{
    use CRUDModel; // <- This is required
    
    /**
     * Mass assignable fields. 
     *
     * @var array
     **/
    protected $fillable = [
        'is_published',
        'views',
    ];

    .... Relations etc ...
}
```

## Datatable configuration

By default, datatable columns are equal to mass-assignable fields, but you can easily configure everything.

First of all you need to create presenter
```php 
<?php

namespace App\Presenters;

use Modules\Crud\Contracts\DatatablePresenterContract;

class ArticleModuleDatatablePresenter implements DatatablePresenterContract
{
    /**
     * Get the datatable columns config/mapping.
     *
     * @return array
     */
    public function getDatatableColumns(): array
    {
        return [
            'id'           => 'ID',
            'is_published' => 'Is published',
            'title'        => [
                'title'      => 'Article title', // column title
                'data'       => 'title', // column data field
                'name'       => 'translations.title', // SQL column name
                'searchable' => true, // Is searchable?
                'orderable'  => true, // Is orderable?
            ],
            'created_at'   => 'Added at',
        ];
    }

    /**
     * Get the list relations that should be eager loaded.
     *
     * @return array
     */
    public function eagerLoadableRelations(): array
    {
        return ['translations'];
    }

    /**
     * Get the columns that should not be escaped.
     *
     * @return array
     */
    public function getRawColumns(): array
    {
        return ['is_published'];
    }

    /** -------------------- Column modifiers -------------------- */

    /**
     * Modify is_published column.
     *
     * @param $row
     * @return string
     */
    public function isPublishedModifier($row)
    {
        $labelClass = $row->is_published ? 'success' : 'danger';
        $labelText = $row->is_published ? 'Yes' : 'No';

        return "<span class=\"label label-{$labelClass}\">{$labelText}</span>";
    }
}
```

Then you need to override/set this presenter in your CRUD model:
```php 
class Article extends Model
{
    use CRUDModel;

    ...
    
    /**
     * Get the presenter class for datatable.
     *
     * @return string
     */
    public function getDatatablePresenter(): string
    {
        return \App\Presenters\ArticleModuleDatatablePresenter::class;
    }
}
```

That's it! Datatable now will use columns/config from presenter.