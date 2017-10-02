<?php
namespace Modules\Crud\Traits;

trait CrudifyModel{

    /*
     * @TODO: 1. no show skata, tabulas, varētu automātiski izvākt textarea laukus, jeb arī tiem automātiski piemērot strlimit
     * */

    private static $crudDefault = [
        'destroy' => true,
        'index'   => 'fillable',
        'show'    => 'fillable',
        'create'  => 'fillable',
        'edit'    => 'create',
    ];

    //@TODO: 2. magic fields varētu pabeigt
    private static $magicFields = [
        'password' => [
            'fields' => [
                'password'              => 'password',
                'password_confirmation' => 'password'
            ],
            'validation' => [
                'password' => 'confirmed'
            ]
        ],
        'email' => [
            'fields' => [
                'email' => 'email'
            ],
            'validation' => [
                'email' => 'email'
            ]
        ],
    ];

    private static $morphCrudMethods = [
        'store'  => 'create',
        'update' => 'edit'
    ];

    public static $columnsWithType;

    /**
     * @return string
     */
    public function getClassName()
    {
        return class_basename($this);
    }

    /**
     * @return mixed
     */
    public function getTableColumns()
    {
        if( self::$columnsWithType ){
            return self::$columnsWithType;
        }
        $builder = $this->getConnection()->getSchemaBuilder();
        $columns = $builder->getColumnListing($this->getTable());

        $columnsWithType = collect($columns)->mapWithKeys(function ($item, $key) use ($builder) {
            $key = $builder->getColumnType($this->getTable(), $item);
            return [$item => $key];
        });

        self::$columnsWithType = $columnsWithType->toArray();

        return self::$columnsWithType;
    }

    /**
     * @param $method
     * @return array
     */
    public function getValidationFields($method)
    {
        if( in_array( $method, array_keys( self::$morphCrudMethods ) ) ){
            $method = self::$morphCrudMethods[$method];
        }

        $fields = $this->crud[$method];

        return isset( $fields['validation'] ) ? $fields['validation'] : [];
    }

    /**
     * @param $method
     * @return array
     */
    public function getCrudFields($method)
    {
        if( in_array( $method, array_keys( self::$morphCrudMethods ) ) ){
            $method = self::$morphCrudMethods[$method];
        }

        $fields = $this->crud[$method];

        return $fields['fields'];
    }

    /**
     * @return array
     */
    public function getCrudAttribute()
    {
        $views = self::$crudDefault;

        if( isset( self::$crud ) ){
            $views = array_merge(
                $views, self::$crud
            );
        }

        $map = $this->buildCrudMap(
            $views
        );

        return $map;
    }

    /**
     * @param $views
     * @return array
     */
    public function buildCrudMap($methods)
    {
        $map = [
            'destroy' => $methods['destroy']
        ];

        unset($methods['destroy']);

        foreach( $methods as $view => $fieldContainer ){
            $map[$view] = $this->resolveFieldContainer($methods, $view, $fieldContainer);
        }

        return $map;
    }

    /**
     * @param $views
     * @param $view
     * @param $fieldContainer
     * @return array
     */
    public function resolveFieldContainer( $methods, $view, $fieldContainer )
    {
        //field is presented as array, we can assume desired configuration is passed
        if( is_array( $fieldContainer ) ){
            return $this->buildFields( $fieldContainer );
        }

        //field is a string, so we check against other fields and resolve it from them
        if( $aliasView = $fieldContainer AND in_array($aliasView, array_keys( self::$crudDefault ) ) ){
            return $this->resolveFieldContainer($methods, $aliasView, $methods[$aliasView]); //this is stupid. for now will do.
        }

        //as we cant resolve view, it will be populated from
        return $this->buildFields(
            $this->fieldsFromFillable(
                $removeHidden = (in_array($view,['create','edit']) ? false : true)
            )
        );
    }

    /**
     * @param $fields
     * @return array
     */
    public function buildFields($fields)
    {
        if( !is_array($fields) ){
            return $fields;
        }

        $parsed = [];

        foreach( $fields as $field => $fieldData ){
            if( is_numeric( $field ) ){
                $field = $fieldData;
            }

            preg_match_all('/(.*)\[(.*)\]/', $fieldData, $matches, PREG_SET_ORDER, 0);

            if( $matches ){
                $parsed['fields'][$field]     = ($matches[0][1] ? $matches[0][1] : 'text');
                $parsed['validation'][$field] = $matches[0][2];
            } else {

                //@TODO: refactor this
                $tableColumns = $this->getTableColumns();

                $parsed['fields'][$field] = 'text';

                if( strpos($field, 'is_') !== false ){
                    $parsed['fields'][$field] = 'boolean';
                }

                if( isset( $tableColumns[$field] ) AND $dbFieldType = $tableColumns[$field] ){

                    $dbToHtml = [
                        'string'   => 'text',
                        'text'     => 'textarea',
                        'datetime' => 'datetime'
                    ];

                    if( isset( $dbToHtml[ $tableColumns[$field] ] ) ){
                        $parsed['fields'][$field] = $dbToHtml[ $tableColumns[$field] ];
                    }
                }

                $parsed['validation'][$field] = 'required';

                if( isset( self::$magicFields[$field] ) ){
                    //autoapply from magic fields

                    if( isset( self::$magicFields[$field]['fields'] ) ){
                        foreach( self::$magicFields[$field]['fields'] as $magicField => $magicFieldData ){
                            $parsed['fields'][$magicField] = $magicFieldData;
                        }
                    }

                    if( isset( self::$magicFields[$field]['validation'] ) ){
                        foreach( self::$magicFields[$field]['validation'] as $magicField => $magicFieldData ){
                            $parsed['validation'][$magicField] = $magicFieldData;
                        }
                    }
                }
            }
        }

        return $parsed;
    }

    /**
     * @return array
     */
    public function fieldsFromFillable($removeHidden = true)
    {
        $fillable = $this->fillable;

        if( $removeHidden AND isset( $this->hidden ) ){
            $fillable = array_diff($this->fillable, $this->hidden);
        }

        return $fillable;
    }
}