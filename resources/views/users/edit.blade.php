@extends('layouts.master')

@section('title')
    @lang('titulos.Editar_Usuario')
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
            @lang('titulos.Editar_Usuario')
        @endslot
    @endcomponent

    <form method="POST" action="{{ route('users.update', $user->id) }}"  role="form" enctype="multipart/form-data">
        {{ method_field('PATCH') }}
        @csrf

        @include('users.form')

    </form>

    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:none;" id="delete-form">
        @csrf
        @method('DELETE')        
    </form> 
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
        <!-- Idioma español del deatepicker -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.es.min.js"></script>

    <script src="{{ URL::asset('build/js/pages/users.init.js') }}"></script>

    <script>
        $(document).ready(function() {                     
            
            $('#role').on('change', function() {
                var role_id = $(this).val();
                if (role_id) {
                    $.ajax({
                        url: '/roles/get-permissions/' + role_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            $('input[name="permissions[]"]').prop('checked', false);

                            // Marcar los checkboxes correspondientes al rol seleccionado
                            response.permissionsRole.forEach(function(permission) {
                                $('input[name="permissions[]"][value="' + permission.name + '"]').prop('checked', true);
                            });
                        }
                    });
                } else {
                    $('#permissions').empty();
                }
            });

            
        });
        </script>
@endsection