<div class="row">
    <div class="col-md-6">       
        <div class="card">
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-8">   
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang("translation.Nombre")*</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role?->name) }}" id="name" placeholder="@lang("translation.Nombre")" required>
                            {!! $errors->first('name', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
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
                                    <input class="form-check-input" type="checkbox" id="permiso{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $role && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="SwitchCheckSizemd">{{ $permission->name }}</label>
                                </div>
                            @endforeach                           
                        </div>    
                    </div>
                
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="text-end mb-4">
            <a href="{{ route('permissions.index') }}"class="btn btn-secondary">@lang("botones.Volver")</a>
            <button type="submit" class="btn btn-primary">@lang("botones.Guardar")</button>
        </div>
    </div>
    <div class="col-lg-6">
        @if (!empty($role->id)) 
        <div class="text-end mb-4">            
            <button type="button" class="btn btn-danger" onclick="if(confirm('{{ __('messages.estas_seguro') }}')) { document.getElementById('delete-form').submit(); };">@lang('botones.Eliminar')</button>
        </div>
        @endif
    </div>
</div>