<?php

namespace Modules\Crud\Traits;

use Doctrine\DBAL\Types\StringType;
use Illuminate\Support\Collection;

trait CRUDModel
{
    /**
     * List of fields that must be hidden.
     *
     * @var array
     */
    public $hiddenFields = [];

    /**
     * Specify whether passwords should be hashed or not
     *
     * @var bool
     */
    public $hashPasswords = true;

    /**
     * Data type mapping against form generator.
     *
     * @var array
     */
    protected $typeMap = [
        'string'   => 'text',
        'text'     => 'textarea',
        'tinyint'  => 'select',
        'boolean'  => 'select',
        'datetime' => 'datetime'
    ];

    /**
     * List of column names for different input field types.
     *
     * @var array
     */
    protected $magicFields = [
        'email'    => 'email',
        'password' => 'password'
    ];

    /**
     * List of extra validation rules for magic fields.
     *
     * @var array
     */
    protected $magicFieldValidation = [
        'email'    => 'email',
        'password' => 'confirmed'
    ];

    /**
     * CRUD config.
     *
     * @var array
     */
    public $crudConfig = [
        'allow-delete' => true,
        'allow-create' => true,
        'allow-view'   => true,
        'allow-export' => false,
    ];

    /**
     * Check if crud config allow to perform given operation.
     *
     * @param string $action
     * @param bool $default
     * @return bool
     */
    public function isAbleTo(string $action, bool $default = false): bool
    {
        if (isset($this->crudConfig['allow-' . $action])) {
            return (bool) $this->crudConfig['allow-' . $action];
        }

        return $default;
    }

    /**
     * To avoid empty password to be hashed, we hash it in model.
     *
     * @param $password
     * @return void
     */
    public function setPasswordAttribute($password): void
    {
        if (!empty($password)) {
            $this->attributes['password'] = $this->hashPasswords ? bcrypt($password) : $password;
        }
    }

    /**
     * Set fields that must be hidden from "getFields".
     *
     * @param array $fields
     * @return $this
     */
    public function hideFields($fields = [])
    {
        $this->hiddenFields = $fields;

        return $this;
    }

    /**
     * Get the model class name.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return class_basename($this);
    }

    /**
     * Return list of all magic fields.
     *
     * @return array
     */
    public function getMagicFields(): array
    {
        return $this->magicFields;
    }

    /**
     * Get array of validation rules.
     *
     * @param $model
     * @return array
     */
    public function getValidationRules($model): array
    {
        return $this->buildValidation($this->readDatabaseSchema(), $model);
    }

    /**
     * Build list of fields with HTML type.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->readDatabaseSchema() as $field => $schema) {
            // Determine if field is in a magic field.
            if (in_array($field, $this->getMagicFields())) {
                $fields[$field] = $this->getMagicFields()[$field] ?? 'text';

                // If the magic field is "password" then we append always confirmation
                if ($field == 'password') {
                    $fields['password_confirmation'] = 'password';
                }
            } else {
                $fields[$field] = $this->typeMap[$schema->getType()->getName()] ?? 'text';
            }
        }

        return $fields;
    }

    /**
     * Read the database schema and return fillable fields as Column instances.
     *
     * @return Collection
     */
    protected function readDatabaseSchema(): Collection
    {
        $schema = \DB::getDoctrineSchemaManager();

        return collect($schema->listTableColumns($this->getTable()))
            ->reject(function ($instance, $column) {
                return $this->rejectColumn($column);
            });
    }

    /**
     * Build validation rules.
     *
     * @param $columns
     * @param $model
     * @return array
     */
    protected function buildValidation($columns, $model): array
    {
        $rules = [];

        foreach ($columns as $column => $columnInstance) {
            $rules[$column] = [];

            $rules[$column][] = $columnInstance->getNotnull() ? 'required' : 'nullable';

            if ($columnInstance->getType() instanceof StringType) {
                $rules[$column][] = 'max:' . $columnInstance->getLength();
            }

            // Magic field additional validation rules
            if (in_array($column, $this->getMagicFields())) {
                $rules[$column] = $this->getMagicFieldValidation($column);
            }
        }

        $rules = array_merge_recursive($rules, $this->buildUniqueRuleset($model));

        return $rules;
    }

    /**
     * Read unique indexes from table and apply unique rule.
     *
     * @param $model
     * @return array
     */
    protected function buildUniqueRuleset($model): array
    {
        $rules = [];

        $mysqlQuery = "SHOW INDEXES FROM " . $this->getTable() . " WHERE NOT Non_unique and Key_Name <> 'PRIMARY'";

        $pgsqlQuery = "
        select
            t.relname as table_name,
            i.relname as index_name,
            ix.indisunique as uniqueness,
            ix.indisprimary as primary,
            array_to_string(array_agg(a.attname), ', ') as Column_name
        from
            pg_class t,
            pg_class i,
            pg_index ix,
            pg_attribute a
        where
            t.oid = ix.indrelid
            and i.oid = ix.indexrelid
            and ix.indisunique = true
            and ix.indisprimary = false
            and a.attrelid = t.oid
            and a.attnum = ANY(ix.indkey)
            and t.relkind = 'r'
            and t.relname like '" . $this->getTable() . "'
        group by
            t.relname,
            i.relname,
            ix.indisunique,
            ix.indisprimary
        order by
            t.relname,
            i.relname,
            ix.indisunique,
            ix.indisprimary
            ;
        ";

        $mysql = config('database.default') == 'mysql';
        $query = $mysql ? $mysqlQuery : $pgsqlQuery;

        $indexList = \DB::select(
            \DB::raw($query)
        );

        foreach ($indexList as $index) {

            $property = $mysql ? 'Column_name' : 'column_name';
            $columnName = object_get($index, $property);

            if (in_array($columnName, $this->fillable)) {
                $rule = 'unique:' . $this->getTable();

                // If the model does exist then append the route key name (usually: id)
                if ($this->exists() && object_get($model, $this->getRouteKeyName())) {
                    $rule .= ',' . $columnName;

                    if ($model) {
                        $rule .= ',' . $model->{$this->getRouteKeyName()};
                    }
                }

                $rules[$columnName][] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Return additional validation rules for magic field.
     *
     * @param $field
     * @return array
     */
    protected function getMagicFieldValidation($field)
    {
        return $this->magicFieldValidation[$field] ?? [];
    }

    /**
     * Determine if column must be rejected from output.
     *
     * @param $column
     * @return bool
     */
    protected function rejectColumn($column): bool
    {
        if (in_array($column, $this->hiddenFields)) {
            return true;
        }

        return !in_array($column, $this->fillable) ||
            (in_array($column, $this->hidden) && !in_array($column, $this->getMagicFields()));
    }

    /**
     * Get the presenter class for datatable.
     *
     * @return string
     */
    public function getDatatablePresenter(): string
    {
        $className = $this->getClassName() . 'ModuleDatatablePresenter';
        $namespace = app()->getNamespace();

        return $namespace . 'Presenters\\' . $className;
    }

    /**
     * Get datatable columns.
     *
     * @return array
     */
    final public function getDatatableColumns(): array
    {
        $columns = [];
        $presenter = $this->getDatatablePresenter();

        if (class_exists($presenter)) {
            return app($presenter)->getDatatableColumns();
        }

        // CRUD module fallback if presenter doesn't exist.
        foreach ($this->hideFields(['password'])->getFields() as $field => $type) {
            if ($type !== 'textarea') {
                $columns[$field] = title_case(str_replace('_', ' ', $field));
            }
        }

        return $columns;
    }
}