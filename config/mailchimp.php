<?php

return [

    'api_key' => env('MAILCHIMP_APIKEY'),

    /*
     * If you want to add to your mailchimp audience when a user registers, set this to `true`
     */
    'add_new_users' => false,

    'users' => [
        /*
        * A MailChimp audience id. Check the MailChimp docs if you don't know
        * how to get this value:
        * https://mailchimp.com/help/find-audience-id/.
        */
        'audience_id' => null,

        /*
        * Mailchimp Tag to assign to the contact.
        *
        * @see https://mailchimp.com/help/getting-started-tags/
        */
        'tag' => null,

        /*
        * Use this field in your user to indicate which Mailchimp Tag to use
        *
        * @see https://mailchimp.com/help/getting-started-tags/
        */
        'tag_field' => null,

        /*
        * This is NOT recommended and means that they WILL NOT get the opt in email.
        * NOTE: This may violate privacy laws and may get your banned from Mailchimp
        */
        'disable_opt_in' => false,

        /*
        * if you need consent before you can subscribe someone, set this to `true`
        */
        'check_consent' => true,

        /*
        * if you're checking for consent, which field is it? Defaults to `'consent'`
        */
        'consent_field' => 'consent',

        /*
        * See https://mailchimp.com/help/manage-audience-signup-form-fields/ for details on
        * Mailchimp merge fields
        */
        'merge_fields' => [
            [
                /*
                * The Mailchimp merge field
                */
                'tag'=> null,

                /*
                * the blueprint field name to use for the merge field
                */
                'field_name' => null,
            ],
        ],

    ],

    /*
     * The form submissions to add to your Mailchimp Audiences
     */
    'forms' => [
        [
            /*
            * handle of the form to listen for
            */
            'form' => null,

            /*
            * Define the handle for the email field to be used. Defaults to 'email'.
            */
            'primary_email_field' => 'email',

            /*
            * if you'd like to add "interests" in a group, which field is collecting those ids? Defaults to 'interests'
            */
            'interests_field' => 'interests',

            /*
            * A MailChimp audience id. Check the MailChimp docs if you don't know
            * how to get this value: https://mailchimp.com/help/find-audience-id/.
            */
            'audience_id' => null,

            /*
            * Mailchimp Tag to assign to the contact.
            * NOTE: `tag_field` takes precendence over `tag`
            *
            * @see https://mailchimp.com/help/getting-started-tags/
            */
            'tag' => null,

            /*
            * Use this field in your user to indicate which Mailchimp Tag to use
            *
            * @see https://mailchimp.com/help/getting-started-tags/
            */
            'tag_field' => null,

            /*
            * This is NOT recommended and means that they WILL NOT get the opt in email.
            * NOTE: This may violate privacy laws and may get you banned from Mailchimp
            */
            'disable_opt_in' => false,

            /*
            * if you need consent before you can subscribe someone, set this to `true`
            */
            'check_consent' => true,

            /*
            * if you're checking for consent, which field is it? Defaults to `'consent'`
            */
            'consent_field' => 'consent',

            /*
            * To have single opt in only, which I don't recommend, set this to `true`.
            * See: https://mailchimp.com/help/single-opt-in-vs-double-opt-in/ for details
            */
            'disable_opt_in' => false,

            /*
            * See https://mailchimp.com/help/manage-audience-signup-form-fields/ for details on
            * Mailchimp merge fields
            */
            'merge_fields' => [
                [
                    /*
                    * The Mailchimp tag
                    */
                    'tag'=> null,

                    /*
                    * the blueprint field name to use for the merge field
                    */
                    'field_name' => null,
                ],
            ],
        ],
    ],
];
