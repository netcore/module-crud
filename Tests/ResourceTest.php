<?php

namespace Modules\Crud\Tests;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResourceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCrudRoutes()
    {
        $user = app(config('netcore.module-admin.user.model'))->where('is_admin', 1)->first();

        $controllers = [];

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $action = $route->getAction();

            if (array_key_exists('controller', $action)) {
                $controller = explode('@', $action['controller'])[0];

                if (method_exists(app($controller), 'isCrud')) {
                    $name = explode('.', $route->getName());

                    if (count($name) <= 1) {
                        continue;
                    }

                    $name = $name[0];

                    if (!in_array($name, $controllers)) {
                        $controllers[] = $name;
                    }
                }
            }
        }

        foreach ($controllers as $route) {
            $response = $this->actingAs($user)->get(route($route . '.index'));

            $response->assertStatus(200);
        }
    }
}
