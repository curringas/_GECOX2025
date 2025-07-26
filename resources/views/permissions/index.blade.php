@extends('layouts.master')

@section('title')
    @lang('titulos.Permisos')
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
            @lang('titulos.Permisos')
        @endslot
    @endcomponent
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="text-end mb-4">
                <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                    <i class="bx bx-smile font-size-16 align-middle me-2"></i>
                    @lang("titulos.Crear_Permiso")</a>
            </div>
        </div>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead class="thead">
            <tr>
                <th>No</th>                                        
                <th>@lang("translation.Nombre")</th>
                <th>Roles</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ ++$i }}</td>
                    
                    <td >{{ $permission->name }}</td>
                    <td >
                        @foreach ($permission->roles as $role)
                            {{ $role->name }},
                        @endforeach
                    </td>

                    <td>
                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST">
                            <a class="btn btn-sm btn-primary " href="{{ route('permissions.show', $permission->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('botones.Ver') }}</a>
                            <a class="btn btn-sm btn-success" href="{{ route('permissions.edit', $permission->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('botones.Editar') }}</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('{{ __('messages.estas_seguro') }}') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('botones.Eliminar') }}</button>
                        </form>
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
    {!! $permissions->withQueryString()->links() !!}
@endsection
