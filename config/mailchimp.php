<?php

return [

    'api_key' => env('MAILCHIMP_API_KEY'),

    /*
     * The form submissions to add to your Mailchimp Audiences
     */
    'forms' => [],

    /*
     * The listName to use when no listName has been specified in a method.
     */
    'defaultListName' => 'subscribers',
];
