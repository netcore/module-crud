@extends('crud::layouts.master')

@section('crudName', 'List all')

@section('crudPanelName', 'All results from resource')

@section('crud')
@isset($rows)
    <div class="table-primary">
        <table class="table table-bordered" id="datatables">
            <thead>
            <tr>
                @foreach($model->getFields() as $field => $type )

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
                    @foreach( $model->getFields() as $field => $type )
                        @if( $type == 'textarea' )

                        @else
                            <td>{{ $row->{$field} }}</td>
                        @endif
                    @endforeach

                    <td>
                        @foreach(['show','edit','destroy'] as $method)
                            <a href="{{ crudify_route($method, $row->id)}}" class="btn btn-default">{{ $method }}</a>
                        @endforeach
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
        $(function() {
            var table = $('#datatables').dataTable({
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]

            });

            $('#datatables_wrapper .table-caption').text('Some header text');
            $('#datatables_wrapper .dataTables_filter input').attr('placeholder', 'Search...');
        });
    </script>
@endsection