@extends('admin::layouts.master')

@section('content')
    <ol class="breadcrumb page-breadcrumb">
        <li><a href="{{url('/admin')}}">Admin</a></li>
        <li><a href="{{crudify_route('index')}}">{{str_plural($model->getClassName())}}</a></li>
        <li class="active">@yield('crudName')</li>
    </ol>

    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon ion-ios-keypad"></i>{{str_plural($model->getClassName())}} / </span>@yield('crudName')</h1>

        <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
            <a href="{{crudify_route('create')}}" class="btn btn-primary btn-block"><span class="btn-label-icon left ion-plus-round"></span>Create new {{$model->getClassName()}}</a>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <div class="panel-title">@yield('crudPanelName')</div>
        </div>
        <div class="panel-body">
            @yield('crud')
        </div>
    </div>

@endsection
