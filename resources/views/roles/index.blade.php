@extends('layouts.master')

@section('title')
    @lang('translation.Users')
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
            @lang('titulos.Roles')
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
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="bx bx-smile font-size-16 align-middle me-2"></i>
                    @lang("titulos.Crear_Rol")</a>
            </div>
        </div>
    </div>
    <table class="table table-bordered yajra-datatable">
        <thead class="thead">
            <tr>
                <th>No</th>                                        
                <th>@lang("translation.Nombre");</th>
                <th >Permisos</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ ++$i }}</td>
                    
                    <td >{{ $role->name }}</td>
                    <td >
                        @foreach ($role->permissions as $permission)
                            {{ $permission->name }},
                        @endforeach
                    </td>

                    <td>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                            <a class="btn btn-sm btn-primary " href="{{ route('roles.show', $role->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}</a>
                            <a class="btn btn-sm btn-success" href="{{ route('roles.edit', $role->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('{{ __('messages.estas_seguro') }}') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('translation.Eliminar') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {!! $roles->withQueryString()->links() !!}
@endsection
