<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">@lang('translation.User_DatosPersonales')</h4>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang('translation.Nombre')*</label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user?->name) }}" id="name" placeholder="@lang('translation.Nombre')"
                                required>
                            {!! $errors->first('name', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email*</label>
                            <input type="text" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user?->email) }}" id="email" placeholder="Email" required>
                            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                        @if (empty($user))
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">@lang('translation.Contraseña')</label>
                                        <input type="password" name="newpassword"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="newpassword" placeholder=""
                                            value="{{ old('newpassword', $user?->password) }}">
                                        {!! $errors->first('newpassword', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">@lang('translation.ConfirmarContraseña')</label>
                                        <input type="password" name="newpassword_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            id="newpassword_confirmation" placeholder=""
                                            value="{{ old('newpassword_confirmation', $user?->password) }}">
                                        {!! $errors->first(
                                            'newpassword_confirmation',
                                            '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="role" class="form-label">@lang('translation.Roles')*</label>
                            <select class="form-select" name="role" id="role" required>
                                <option value="">@lang('translation.Seleccione')</option>
                                @foreach ($roles as $role)
                                    @if ($role->name === 'Super-admin')
                                        @if (auth()->user() && auth()->user()->hasRole('Super-admin'))
                                            <option value="{{ $role->name }}"
                                                {{ !empty($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endif
                                    @else
                                        <option value="{{ $role->name }}"
                                            {{ !empty($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            {!! $errors->first('role', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
                                <input class="form-check-input" type="checkbox" id="active-switch" name="activo"
                                    value="1" {{ old('activo', $user->activo ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label ms-2 mb-0" for="active-switch">@lang('Activo')</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">


                        <div class="mb-3">
                            <label class="form-label">Avatar</label>

                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="position-absolute bottom-0 end-0">
                                        <label for="project-image-input" class="mb-0" data-bs-toggle="tooltip"
                                            data-bs-placement="right" aria-label="Select Image"
                                            data-bs-original-title="Select Image">
                                            <div class="avatar-xs">
                                                <div
                                                    class="avatar-title bg-light border rounded-circle text-muted cursor-pointer shadow font-size-16">
                                                    <i class="bx bxs-image-alt"></i>
                                                </div>
                                            </div>
                                        </label>
                                        <input name="avatar" class="form-control d-none" id="project-image-input"
                                            type="file" accept="image/png, image/gif, image/jpeg">
                                    </div>
                                    <div class="avatar-lg">
                                        <div class="avatar-title bg-light rounded-circle">
                                            @if (!empty($user) && $user->avatar)
                                                <img src="{{ URL::asset('storage/avatares/' . $user->avatar) }}"
                                                    id="projectlogo-img" class="avatar-md h-auto rounded-circle">
                                            @else
                                                <img src="{{ URL::asset('build/images/default-avatar.png') }}"
                                                    id="projectlogo-img" class="avatar-md h-auto rounded-circle">
                                            @endif
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
                            @foreach ($permissions as $permission)
                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox"
                                        id="permiso{{ $permission->id }}" name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ !empty($user) && $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="SwitchCheckSizemd">{{ $permission->name }}</label>
                                </div>
                            @endforeach
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
            <a href="{{ route('users.index') }}"class="btn btn-secondary">@lang('botones.Volver')</a>
            <button type="submit" class="btn btn-primary">@lang('botones.Guardar')</button>
        </div>
    </div>
    <div class="col-lg-6">
        @if (!empty($user->id))
            <div class="text-end mb-4">
                <button type="button" class="btn btn-danger"
                    onclick="if(confirm('{{ __('messages.estas_seguro') }}')) { document.getElementById('delete-form').submit(); };">@lang('botones.Eliminar')</button>
            </div>
        @endif
    </div>
</div>
