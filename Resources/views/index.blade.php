@extends('crud::layouts.master')

@section('crudName', 'List all')

@section('crudPanelName', 'All results from resource')

@section('crud')
@isset($rows)
    <div class="table-primary">
        <table class="table table-bordered datatable">
            <thead>
                <tr>
                    @foreach($model->hideFields(['password'])->getFields() as $field => $type )
                        @if($type != 'textarea' )
                            <th>{{ title_case(str_replace('_', ' ', $field)) }}</th>
                        @endif
                    @endforeach
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>

            @foreach($rows as $row)
                <tr>
                    @foreach($model->hideFields(['password'])->getFields() as $field => $type)
                        @if($type != 'textarea')
                            <td>{{ $row->{$field} }}</td>
                        @endif
                    @endforeach
                    <td width="15%" class="text-center">
                        @if(isset($config['allow-view']) && $config['allow-view'] || !isset($config['allow-view']))
                        <a href="{{ crud_route('show', $row) }}" class="btn btn-xs btn-default">
                            <i class="fa fa-eye"></i> View
                        </a>
                        @endif

                        <a href="{{ crud_route('edit', $row) }}" class="btn btn-xs btn-primary">
                            <i class="fa fa-pencil"></i> Edit
                        </a>

                        @if(isset($config['allow-delete']) && $config['allow-delete'] || !isset($config['allow-delete']))
                            {!! Form::open(['url' => crud_route('destroy', $row->id), 'style' => 'display: inline-block']) !!}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?');">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            {!! Form::close() !!}
                        @endif
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
@endisset
@stop

@section('scripts')
    <script>
        (function() {
            $('.datatable').dataTable({
                columnDefs: [
                    {orderable: false, targets: -1}
                ]
            });
            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Search...');
        })();
    </script>
@endsection
