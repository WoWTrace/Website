<div class="mb-3 d-flex align-items-center">
    <span style="width:125px;" class="small">
        <span class="text-ellipsis">{{ $lockUser->presenter()->title() }}</span>
        <span class="text-muted d-block text-ellipsis">{{ $lockUser->presenter()->subTitle() }}</span>
    </span>
    <input type="hidden" name="email" required value="{{ $lockUser->email }}">
</div>

@error('email')
<span class="d-block invalid-feedback text-danger">
            {{ $errors->first('email') }}
    </span>
@enderror

<div class="mb-3">
    <input type="hidden" name="remember" value="true">

    {!!  \Orchid\Screen\Fields\Password::make('password')
            ->required()
            ->tabindex(1)
            ->autofocus()
            ->placeholder(__('Enter your password'))
    !!}
</div>

<div class="row align-items-center">
    <div class="col-md-6 col-xs-12">
        <a href="{{ route('web.login.lock') }}" class="small">
            {{__('Sign in with another user.')}}
        </a>
    </div>
    <div class="col-md-6 col-xs-12">
        <button id="button-login" type="submit" class="btn btn-primary btn-block" tabindex="2">
            <x-orchid-icon path="login" class="small me-2"/> {{__('Login')}}
        </button>
    </div>
    <div class="p-3">
        <a class="btn btn-outline-secondary btn-block" href="{{ route('web.register.index') }}">
            <x-orchid-icon path="key" class="small me-2"/>
            {{__('Register')}}
        </a>
    </div>
</div>
