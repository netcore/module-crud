@extends('crud::layouts.master')

@section('crudName', 'List all')

@section('crudPanelName', 'All results from resource')

@section('crud')
@isset($rows)
    <div class="table-primary">
        <table class="table table-bordered" id="datatables">
            <thead>
                <tr>
                    @foreach($model->hideFields(['password'])->getFields() as $field => $type )

                        @if( $type == 'textarea' )

                        @else
                            <th>{{ title_case(str_replace('_', ' ', $field))}}</th>
                        @endif
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            @foreach( $rows as $row )
                <tr>
                    @foreach($model->hideFields(['password'])->getFields() as $field => $type )
                        @if( $type == 'textarea' )

                        @else
                            <td>{{ $row->{$field} }}</td>
                        @endif
                    @endforeach

                    <td>
                        <a href="{{ crud_route('show', $row) }}" class="btn btn-xs btn-default">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ crud_route('edit', $row) }}" class="btn btn-xs btn-primary">
                            <i class="fa fa-pencil"></i>
                        </a>

                        {!! Form::open(['url' => crud_route('destroy', $row->id), 'style' => 'display: inline-block']) !!}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?');">
                                <i class="fa fa-trash"></i>
                            </button>
                        {!! Form::close() !!}
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
            $('#datatables').dataTable({
                columnDefs: [
                    {orderable: false, targets: -1}
                ]
            });
            $('#datatables_wrapper .dataTables_filter input').attr('placeholder', 'Search...');
        })();
    </script>
@endsection