<?php

namespace Modules\Crud\Traits;

use Doctrine\DBAL\Types\StringType;

trait CrudifyModel {

    protected $typeMap = [
        'string' => 'text',
        'text' => 'textarea',
        'tinyint' => 'select'
    ];

    // TODO: Implement email, password as magic methods

    /**
     * @return string
     */
    public function getClassName()
    {
        return class_basename($this);
    }

    /**
     * @return array
     */
    public function buildFields()
    {
        $fields = [];

        foreach($this->readDatabaseSchema() as $field => $schema) {
            $fields[$field] = $this->typeMap[$schema->getType()->getName()] ?? 'text';
        }

        return $fields;
    }

    /**
     * Get array of validation rules
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->buildValidation($this->readDatabaseSchema());
    }

    /**
     * Read the database schema and return fillable fields as Column instances
     *
     * @return Collection
     */
    protected function readDatabaseSchema()
    {
        $schema = \DB::getDoctrineSchemaManager();

        return collect($schema->listTableColumns($this->getTable()))
            ->reject(function ($instance, $column) {
               return ! in_array($column, $this->fillable) || in_array($column, $this->hidden);
            });
    }

    /**
     * Build validation rules
     *
     * @param $columns
     * @return array
     */
    protected function buildValidation($columns)
    {
        $rules = [];

        foreach($columns as $column => $columnInstance) {
            $rules[$column] = [];

            $rules[$column][] = $columnInstance->getNotnull() ? 'required' : 'nullable';

            if ($columnInstance->getType() instanceof StringType) {
                $rules[$column][] = 'max:' . $columnInstance->getLength();
            }
        }

        return array_merge_recursive($rules, $this->buildUniqueRuleset());
    }

    /**
     * Read unique indexes from table and apply unique rule
     *
     * @return array
     */
    protected function buildUniqueRuleset()
    {
        $rules = [];
        $indexList = \DB::select(
            \DB::raw("SHOW INDEXES FROM " . $this->getTable() . " WHERE NOT Non_unique and Key_Name <> 'PRIMARY'")
        );

        foreach ($indexList as $index) {
            if(in_array($index->Column_name, $this->fillable)) {
                $rule = 'unique:' . $this->getTable();

                // If the model does exist then append the route key name
                if ($this->exists()) {
                    $rule .= ',' . $index->Column_name . ',' . $this->{$this->getRouteKeyName()};
                }

                $rules[$index->Column_name][] = $rule;
            }
        }

        return $rules;
    }

}