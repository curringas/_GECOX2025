@extends('layouts.master')

@section('title')
    @lang('titulos.Rol')
@endsection

@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection

@section('content')  
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            @lang('titulos.Rol')
        @endslot
    @endcomponent

    <div class="row">
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="float-left">
                    <span class="card-title">&nbsp;</span>
                </div>      
                <div class="float-right">
                    <a class="btn btn-primary btn-sm" href="{{ route('roles.index') }}"> {{ __('botones.Volver') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">       
            <div class="card">
                <div class="card-body"> 
                    <h4 class="card-title mb-4">@lang("translation.Nombre")</h4>
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="mb-3">                                
                                {{ $role?->name }}
                            </div>    
                        </div>                
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">      
            <div class="card">
                <div class="card-body">   
                    <h4 class="card-title mb-4">@lang('titulos.Permisos') que tiene asignados</h4>
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="mb-3">
                                <ul>
                                @foreach ($permissions as $permission)
                                    @if ($permission->hasRole($role->name))                                    
                                        <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                            <li>{{ $permission->name }}</li>
                                        </div>
                                    @endif
                                @endforeach                           
                                </ul>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
