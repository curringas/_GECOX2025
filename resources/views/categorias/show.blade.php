@extends('layouts.master')

@section('title')
    @lang('titulos.Usuario')
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
            @lang('titulos.Usuario')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-md-6">       
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.User_DatosPersonales')</h4>     
                    <div class="row">
                    <div class="col-md-8">   
                        <div class="mb-3">
                            <b>Nombre:</b> {{ $user?->name }}                            
                        </div>
                        <div class="mb-3">
                            <b>Email:</b>  {{ $user?->email }}                            
                        </div>
                        
                        <div class="mb-3">
                            <b>Role:</b> {{ $user->roles[0]->name }}
                        </div>  
                        <div class="mb-3">                            
                            <b>Fecha Nacimiento:</b> {{ \Carbon\Carbon::parse($user->dob)->format("d/m/Y") }}
                        </div>      
                    </div>
                    <div class="col-md-4">
                    
                    
                        <div class="mb-3">
                            <label class="form-label">Avatar</label>
                            
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="position-absolute bottom-0 end-0">
                                        <label for="project-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image" data-bs-original-title="Select Image">
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-light border rounded-circle text-muted cursor-pointer shadow font-size-16">
                                                    <i class="bx bxs-image-alt"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="avatar-lg">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <img src="{{ URL::asset('storage/avatares/'.$user->avatar) }}" id="projectlogo-img" class="avatar-md h-auto rounded-circle">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">      
            <div class="card">
                <div class="card-body">   
                    <h4 class="card-title mb-4">@lang('translation.User_Permisos')</h4>
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="mb-3">
                                <ul>
                                @foreach ($user->permissions as $permission)
                                    <li>{{ $permission->name }}</li>
                                @endforeach                           
                                </ul>
                            </div>    
                        </div>                
                    </div>    
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="text-end mb-4">
                <a href="{{ route('users.index') }}"class="btn btn-secondary">@lang("botones.Volver")</a>
                <a href="{{ route('users.edit',$user) }}"class="btn btn-primary">@lang("botones.Editar")</a>
            </div>
        </div>
        <div class="col-lg-6">
            @if (!empty($user->id)) 
                <div class="text-end mb-4">        
                    

                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:none;" id="delete-form">
                        @csrf
                        @method('DELETE')        
                    </form>     
                    <button type="button" class="btn btn-danger" onclick="if(confirm('{{ __('messages.estas_seguro') }}')) { document.getElementById('delete-form').submit(); };">@lang('botones.Eliminar')</button>
                </div>
            @endif
        </div>
    </div>
@endsection
