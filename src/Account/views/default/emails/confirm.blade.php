@extends('layouts.email')

@section('content')

<h1>{{ Lang::get('account.email.confirmation.subject') }}</h1>

<p>{{ Lang::get('account.email.confirmation.greetings') }},</p>

<p>{{ Lang::get('account.email.confirmation.body') }}</p>

<a href='{{{ URL::to("account/confirm/{$user['confirmation_code']}") }}}'>
{{{ URL::to("account/confirm/{$user['confirmation_code']}") }}}
</a>

<p>{{ Lang::get('account.email.confirmation.farewell') }}</p>

@stop


