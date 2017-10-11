<?php

namespace Modules\Crud\Traits;

use Doctrine\DBAL\Types\StringType;
use MongoDB\BSON\Timestamp;

trait CrudifyModel {

    /**
     * Data type mapping against form generator
     *
     * @var array
     */
    protected $typeMap = [
        'string' => 'text',
        'text' => 'textarea',
        'tinyint' => 'select',
        'datetime' => 'datetime'
    ];

    /**
     * List of column names for different input field types
     *
     * @var array
     */
    protected $magicFields = [
        'email' => 'email',
        'password' => 'password'
    ];

    /**
     * List of extra validation rules for magic fields
     *
     * @var array
     */
    protected $magicFieldValidation = [
        'email' => 'email',
        'password' => 'confirmed'
    ];

    /**
     * @return string
     */
    public function getClassName()
    {
        return class_basename($this);
    }

    /**
     * Return list of all magic fields
     *
     * @return array
     */
    public function getMagicFields()
    {
        return $this->magicFields;
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
     * Build list of fields with HTML type
     *
     * @return array
     */
    public function buildFields()
    {
        $fields = [];

        foreach($this->readDatabaseSchema() as $field => $schema) {
            // Determine if field is in a magic field.
            if (in_array($field, $this->getMagicFields())) {
                $fields[$field] = $this->getMagicFields()[$field] ?? 'text';
            } else {
                $fields[$field] = $this->typeMap[$schema->getType()->getName()] ?? 'text';
            }
        }

        return $fields;
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

            // Magic field additional validation rules
            if (in_array($column, $this->getMagicFields())) {
                $rules[$column] = $this->getMagicFieldValidation($column);
            }
        }

        $rules = array_merge_recursive($rules, $this->buildUniqueRuleset());

        return $rules;
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

    /**
     * Return additional validation rules for magic field
     *
     * @param $field
     * @return array
     */
    protected function getMagicFieldValidation($field)
    {
        return $this->magicFieldValidation[$field] ?? [];
    }

}