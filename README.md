# Netcore CMS - CRUD module.

## Pre-installation 
This package is part of Netcore CMS ecosystem and is only functional in a project that has following packages installed:

https://github.com/netcore/netcore
https://github.com/netcore/module-admin
https://github.com/nWidart/laravel-modules

## Instalation

```bash
composer require netcore/module-crud
```

You need to add `CRUDModel` and `CRUDController` trait to your model and controller.

Your controller should look like this:
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