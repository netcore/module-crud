@extends('crud::layouts.master')

@section('crudName', 'Edit')

@section('crudPanelName', $model->getClassName() . ' #' . $model->id)

@section('crud')
    @include('admin::_partials._messages')

    {!! Form::model($model, ['url' => crud_route('update', $model->id), 'files' => true]) !!}
    {{ method_field('PUT') }}
    <div class="p-x-1">
        @if($model->isTranslatable())
            @include('crud::_translatable-fields', ['model' => $model, 'languages' => $languages])

            <div style="margin-top:10px"></div>
        @endif

        @include('crud::_fields', ['model' => $model])

        <button type="submit" class="btn btn-md btn-success m-t-3 pull-xs-right">
            <i class="fa fa-save"></i> Save
        </button>

        <a href="{{ crud_route('index') }}" class="btn btn-md btn-default m-t-3 m-r-1 pull-xs-right">
            <i class="fa fa-undo"></i> Back
        </a>
    </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
    <script>
        $(function () {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });

            $('textarea').summernote({
                height: 200,
                toolbar: [
                    ['parastyle', ['style']],
                    ['fontstyle', ['fontname', 'fontsize']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['picture', 'link', 'video', 'table', 'hr']],
                    ['history', ['undo', 'redo']],
                    ['misc', ['codeview', 'fullscreen']]
                ],
            });
        });
    </script>
@endsection
