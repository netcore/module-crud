@extends('crud::layouts.master')

@section('crudName', 'List all')

@section('crudPanelName', 'All results from resource')

@section('crud')
    <input id="columns" type="hidden" value="{{ $columns->toJSON() }}">

    <div class="table-primary">
        <table class="table table-bordered datatable">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $column->title }}</th>
                    @endforeach
                    <th style="min-width:200px">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        var columns = JSON.parse(jQuery('#columns').val());

        columns.push({
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false,
            className: 'text-right vertical-align-middle',
            order: [[0, 'desc']]
        });

        (function () {
            $('.datatable').dataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('crud::paginate') }}',
                responsive: true,
                columns: columns
            });

            $('.dataTables_wrapper .dataTables_filter input').attr('placeholder', 'Search...');
        })();
    </script>
@endsection
