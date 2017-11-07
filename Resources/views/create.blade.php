@extends('crud::layouts.master')

@section('crudName', 'Create')

@section('crudPanelName',  'Create new ' . $model->getClassName())

@section('crud')
    @include('admin::_partials._messages')

    {!! Form::open(['url' => crud_route('store')]) !!}
        <div class="p-x-1">
            @include('crud::_fields', ['fields' => $model->getFields()])

            <button type="submit" class="btn btn-lg btn-success m-t-3 pull-xs-right">
                Create new {{ $model->getClassName() }}
            </button>
            <a href="{{ crud_route('index') }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">Back</a>
        </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
    <script>
        $(function() {
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