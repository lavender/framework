@extends('layouts.email')

@section('content')

<h1>{{ Lang::get('account.email.password_reset.subject') }}</h1>

<p>{{ Lang::get('account.email.password_reset.greetings') }},</p>

<p>{{ Lang::get('account.email.password_reset.body') }}</p>
<a href='{{ URL::to('account/reset_password/'.$token) }}'>
    {{ URL::to('account/reset_password/'.$token)  }}
</a>

<p>{{ Lang::get('account.email.password_reset.farewell') }}</p>

@stop
