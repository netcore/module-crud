@extends('crud::layouts.master')

@section('crudName', 'List all')

@section('crudPanelName', 'All results from resource')

@section('crud')
    <div class="table-primary">
        <table class="table table-bordered datatable">
            <thead>
                <tr>
                    @foreach($datatable as $field => $title)
                        <th>{{ is_array($title) ? array_get($title, 'title', 'Unknown column') : $title }}</th>
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        var columns = [];

        // Build columns dynamically
        @foreach($datatable as $field => $name)
            @php
                // For some reason pagination is not working if we pass 1/0 instead of true/false..
                $isOrderable = is_array($name) ? (array_get($name, 'orderable', true) ? 'true' : 'false') : 'true';
                $isSearchable = is_array($name) ? (array_get($name, 'searchable', true) ? 'true' : 'false') : 'true';
            @endphp

            columns.push({
                data: '{{ is_array($name) ? array_get($name, 'data', $field) : $field }}',
                name: '{{ is_array($name) ? array_get($name, 'name', $field) : $field }}',
                orderable: {{ $isOrderable }},
                searchable: {{ $isSearchable }}
            });
        @endforeach

        columns.push({
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false,
            className: 'text-right vertical-align-middle'
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