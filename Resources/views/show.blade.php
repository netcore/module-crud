@extends('crud::layouts.master')

@section('crudName', 'Show')

@section('crudPanelName', $model->getClassName() . ' #' . $model->id)

@section('crud')
    <div class="p-x-1">
        @include('crud::_texts', ['fields' => $model->buildFields()])

        <a href="{{ crudify_route('edit', $model->id) }}" class="btn btn-lg btn-primary m-t-3 pull-xs-right">
            Edit
        </a>
        <a href="{{ url()->previous() }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">Back</a>
    </div>
@endsection

@section('scripts')
    //add disabled
@endsection