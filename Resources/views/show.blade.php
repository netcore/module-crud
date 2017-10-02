@extends('crud::layouts.master')

@section('crudName', 'Show')

@section('crudPanelName', $model->getClassName() . ' #' . $model->id)

@section('crud')
    {!! Form::model($model, ['url' => '']) !!}
        <div class="p-x-1">
            @include('crud::_fields', ['fields' => $model->crud['show']['fields']])

            <a href="{{crudify_route('edit', $model->id)}}" class="btn btn-lg btn-primary m-t-3 pull-xs-right">Edit {{$model->getClassName()}}</a>
            <a href="{{url()->previous()}}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">Back</a>

            {{--<a href="javascript:;" class=" text-muted p-t-4">Deactivate resource</a>--}}
        </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
    //add disabled
@endsection