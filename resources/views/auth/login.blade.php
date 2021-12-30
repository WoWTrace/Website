@extends('auth')
@section('title',__('Sign in to your account'))

@section('content')
    <h1 class="h4 text-black mb-4">{{__('Sign in to your account')}}</h1>

    <form class="m-t-md"
          role="form"
          method="POST"
          data-controller="form"
          data-action="form#submit"
          data-form-button-animate="#button-login"
          data-form-button-text="{{ __('Loading...') }}"
          action="{{ route('web.login.submit') }}">
        @csrf

        @includeWhen($isLockUser,'auth.lockme')
        @includeWhen(!$isLockUser,'auth.signin')
    </form>
@endsection
