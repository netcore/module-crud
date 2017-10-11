@extends('crud::layouts.master')

@section('crudName', 'Show')

@section('crudPanelName', $model->getClassName() . ' #' . $model->id)

@section('crud')
    <div class="p-x-1">
        {!! Form::model($model) !!}
        @include('crud::_fields', ['fields' => $model->getFields()])
            <a href="{{ crudify_route('edit', $model->id) }}" class="btn btn-lg btn-primary m-t-3 pull-xs-right">
                Edit
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">Back</a>
        {!! Form::close() !!}
    </div>
@endsection
@section('scripts')
<script>
    (function () {
        // Disable all inputs
        $('input').attr('disabled', true);

        // Replace textarea with the text and remove input
        $('textarea').each(function() {
            var text = $(this).text();
            var parent = $(this).parent();

            $(this).remove();

            parent.append('<div>' + text + '</div>');
        });
    })()
</script>
@endsection