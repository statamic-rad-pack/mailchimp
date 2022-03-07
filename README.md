# Manage Mailchimp newsletters in Statamic
[![Latest Version](https://img.shields.io/github/release/silentzco/statamic-mailchimp.svg?style=flat-square)](https://github.com/silentzco/statamic-mailchimp/releases)

This package provides an easy way to integrate MailChimp with Statamic forms and user registrations.

## Requirements

* PHP 7.4+
* Statamic v3.2.32

## Updating from < 2.2

The package name changed, so to update please run `composer remove edalzell/mailchimp` then follow the instructions below.

## Installation

You can install this package via composer using:

```bash
composer require silentz/mailchimp
```

The package will automatically register itself.

## Configuration

Set your Mailchimp API Key in your `.env` file. You can get it from: https://us10.admin.mailchimp.com/account/api-key-popup/.

```yaml
MAILCHIMP_APIKEY=your-key-here
```

To publish the config file to `config/mailchimp.php` run:

```bash
php artisan vendor:publish --tag="mailchimp-config"
```

This will publish a file with the following contents:
```php
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
                * The Mailchimp tag
                */
                'tag'=> null,

                /*
                * the blueprint field name to use for the merge field
                */
                'field_name' => null,
            ],
        ],

        /*
        * To have single opt in only, which I don't recommend, set this to `true`.
        * See: https://mailchimp.com/help/single-opt-in-vs-double-opt-in/ for details
        */
        'disable_opt_in' => false,
    ],

    /*
     * The form submissions to add to your Mailchimp Audiences
     */
    'forms' => [
        [
            /*
            * A MailChimp audience id. Check the MailChimp docs if you don't know
            * how to get this value: https://mailchimp.com/help/find-audience-id/.
            */
            'audience_id' => null,

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
            * handle of the form to listen for
            */
            'form' => null,

            /*
            * if you'd like to add "interests" in a group, which field is collecting those ids? Defaults to 'interests'
            */
            'interests_field' => 'interests',

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

            /*
            * Define the handle for the email field to be used. Defaults to 'email'.
            */
            'primary_email_field' => 'email',

            /*
            * if you'd like to apply a tag to each member when they submit a particular form
            */
            'tag' => 'Tag One',
        ],
    ],
];


```

You can also configure Mailchimp in the Control Panel
![control panel](https://raw.githubusercontent.com/silentzco/statamic-mailchimp/main/images/config.png)
![merge fields](https://raw.githubusercontent.com/silentzco/statamic-mailchimp/main/images/merge-fields.png)

## Usage

Create your Statamic [forms](https://statamic.dev/forms#content) as usual. Don't forget to add the consent field to your blueprint.

**NOTE**: If you are using groups you will need to know the `id`s of the interests in order to add them to your form:

``` html
<div class="form-group">
    <label>Interests</label>
    <input type="checkbox" name="interests[]" value="4e4b2bc6ae" class="form-control"/>
    <input type="checkbox" name="interests[]" value="3e1e51dbae" class="form-control"/>
    <input type="checkbox" name="interests[]" value="f79652f791" class="form-control"/>
</div>
```

To get those IDs, first run `php artisan mailchimp:groups your_form_handle` to get the group ids. Then run `php artisan mailchimp:interests your_form_handle the_group_id` to get the list of interests and their ID. Use those ids in your template (example above).

The interests field in your form blueprint should end up looking something like this (assuming you use the default `interests` as your fields' handle:

``` yaml
-
  handle: interests
  field:
    options:
      e25a8f41d6: 'Interest group 1'
      cd1g2413a2: 'Interest group 2'
      1b1a842842: 'Interest group 3'
    type: checkboxes
```

## Testing

Run the tests with:
```bash
vendor/bin/phpunit
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [addon-security@silentz.co](mailto:addon-security@silentz.co) instead of using the issue tracker.

## License

This is commercial software. You may use the package for your sites. Each site requires it's own license.
