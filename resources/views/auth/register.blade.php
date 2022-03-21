@extends('auth')

@section('content')
    <h1 class="h4 text-black mb-4">{{__('Create a WoWTrace account')}}</h1>

    @empty(!$errors->count())
        <div class="alert alert-danger rounded shadow-sm mb-3 p-3" role="alert">
            <strong>{{  __('Oh snap!') }}</strong>
            {{ __('Errors occurred during registration!') }}
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="m-t-md"
          role="form"
          method="POST"
          data-controller="form"
          data-action="form#submit"
          data-form-button-animate="#button-login"
          data-form-button-text="{{ __('Loading...') }}"
          action="{{ route('web.register.submit') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">
                {{__('Username')}}
            </label>
            {!!  \Orchid\Screen\Fields\Input::make('name')
                ->required()
                ->tabindex(1)
                ->autofocus()
                ->placeholder(__('Enter your username'))
            !!}
        </div>

        <div class="mb-3">
            <label class="form-label">
                {{__('Email address')}}
            </label>
            {!!  \Orchid\Screen\Fields\Input::make('email')
                ->type('email')
                ->required()
                ->tabindex(2)
                ->autofocus()
                ->placeholder(__('Enter your email'))
            !!}
        </div>

        <div class="mb-3">
            <label class="form-label w-100">
                {{__('Password')}}
            </label>

            {!!  \Orchid\Screen\Fields\Password::make('password')
                ->required()
                ->tabindex(2)
                ->placeholder(__('Enter your password'))
            !!}
        </div>

        <div class="row align-items-center">
            <div class="col-md-6 col-xs-12">

            </div>
            <div class="col-md-6 col-xs-12">
                <button id="button-login" type="submit" class="btn btn-primary btn-block" tabindex="3">
                    <x-orchid-icon path="key" class="small me-2"/>
                    {{__('Register')}}
                </button>
            </div>
        </div>

    </form>
@endsection
