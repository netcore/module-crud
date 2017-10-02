@extends('crud::layouts.master')

@section('crudName', 'Edit')

@section('crudPanelName', $model->getClassName() . ' #' . $model->id)

@section('crud')
    @include('admin::_partials._messages')


    {!! Form::model($model, ['url' => crudify_route('update',$model->id)]) !!}

        {{ csrf_field() }}

        <input name="_method" type="hidden" value="PUT">

        <div class="p-x-1">
            @include('crud::_fields', ['fields' => $model->crud['edit']['fields']])

            <button type="submit" class="btn btn-lg btn-success m-t-3 pull-xs-right">Save {{$model->getClassName()}}</button>

            {{-- @TODO: back poga nepārāk labi strādā uz edit --}}
            <a href="{{url()->previous()}}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">Back</a>

            {{--<a href="javascript:;" class=" text-muted p-t-4">Deactivate resource</a>--}}
        </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
    <script>
        $('.datepicker').datepicker();
        $(function() {
            $('.ritch-textarea').summernote({
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