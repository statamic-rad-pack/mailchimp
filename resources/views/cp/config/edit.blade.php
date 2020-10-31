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
    <div class="bg-white border-solid rounded shadow border-grey">
        <div class="p-3">
            <h3>Tags for each of your audiences</h3>
            <p>To add one, please copy it and put it in the "Tag" field.</p>
            <ul>
                @foreach($lists as $list)
                    <li class="mt-1">
                        <h3>{{ $list['name'] }}</h3>
                        <p class="my-1">
                            @foreach($list['tags'] as $tag)
                                <span class="p-1 text-white bg-blue-500 rounded-full">{{ $tag['name'] }}</span>
                            @endforeach
                        </p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @stop
