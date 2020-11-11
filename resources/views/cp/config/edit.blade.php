@extends('statamic::layout')

@section('title', __('Config'))

@section('content')
    <publish-form
        title="Configuration"
        action="{{ cp_route('mailchimp.config.update') }}"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :values='@json($values)'
    ></publish-form>
@stop
