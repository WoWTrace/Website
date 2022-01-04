<div class="row align-items-center p-3">
    <div class="col-md-6 col-xs-12">
        <a class="btn btn-outline-secondary btn-block" href="{{ route('web.login.index') }}">
            <x-orchid-icon path="login" class="small me-2"/>
            {{__('Login')}}
        </a>
    </div>
    <div class="col-md-6 col-xs-12">
        @if(config('auth.registration'))
        <a class="btn btn-outline-primary btn-block" href="{{ route('web.register.index') }}">
            <x-orchid-icon path="key" class="small me-2"/>
            {{__('Register')}}
        </a>
        @endif
    </div>
</div>