@extends('layouts.single')

@section('head.title')
Goodbye!
@stop

@section('head.meta')
@parent
<meta http-equiv="refresh" content="5; url=/" />
@stop

@section('content')
<h2>Log out success!</h2>
<p>You will be redirected to the homepage in 5 seconds.</p>

@stop