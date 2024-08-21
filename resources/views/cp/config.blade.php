@extends('statamic::layout')
@section('title', __('Manage Mailchimp Settings'))

@section('content')
    <publish-form
            title="{{ __('Manage Mailchimp Settings') }}"
            action="{{ cp_route('mailchimp.config.update') }}"
            :blueprint='@json($blueprint)'
            :meta='@json($meta)'
            :values='@json($values)'
    ></publish-form>
@stop