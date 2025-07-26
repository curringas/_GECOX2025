@extends('layouts.master')

@section('template_title')
    @lang('titulos.Crear_Usuario') 
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} User</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('users.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('users.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
