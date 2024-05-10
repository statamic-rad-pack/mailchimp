# Manage Mailchimp newsletters in Statamic
[![Latest Version](https://img.shields.io/github/v/release/statamic-rad-pack/mailchimp)](https://github.com/statamic-rad-pack/mailchimp/releases)

This package provides an easy way to integrate MailChimp with Statamic forms and user registrations.

## Requirements

* PHP 8.2+
* Statamic v5

## Installation

You can install this package via composer using:

```bash
composer require statamic-rad-pack/mailchimp
```

The package will automatically register itself.

## Configuration

Set your Mailchimp API Key in your `.env` file. You can get it from: https://us10.admin.mailchimp.com/account/api-key-popup/.

```yaml
MAILCHIMP_API_KEY=your-key-here
```

Configure Mailchimp in the Control Panel
![control panel](https://raw.githubusercontent.com/statamic-rad-pack/mailchimp/main/images/config.png)
![merge fields](https://raw.githubusercontent.com/statamic-rad-pack/mailchimp/main/images/merge-fields.png)

Sample config, with comments:

```php
return [

    'api_key' => env('MAILCHIMP_API_KEY'),

    /*
     * Set to `true` to add new user registrations to a Mailchimp audience.
     */
    'add_new_users' => false,

    'users' => [
        /*
        * A Mailchimp Audience ID.
        *
        * @see https://mailchimp.com/help/find-audience-id/.
        */
        'audience_id' => null,

        /*
        * Set to `true` to require consent before subscribing someone
        * Default: `true`
        */
        'check_consent' => true,

        /*
        * Field name used to check for consent.
        * Default: 'consent'
        */
        'consent_field' => 'consent',

        /*
        * Disable Double Opt In. Not typically a best practice.
        * Default: `false`
        *
        * @see https://mailchimp.com/help/single-opt-in-vs-double-opt-in/
        */

        'disable_opt_in' => false,

        /*
        * Field name used to collect ids of group "interests".
        * Default: 'interests'
        *
        * @see https://mailchimp.com/help/how-to-use-groups-to-add-or-update-subscriber-preferences/
        */
        'interests_field' => 'interests',

        /*
        * Field name used to indicate marketing permissions.
        * Default: `gdpr`
        */
        'marketing_permissions_field' => 'gdpr',

        /*
        * Fields used to store marketing permission ids.
        * Run `php please mailchimp:permissions <form-handle>` to get the ids.
        */
        'marketing_permissions_field_ids' => [
            [
                'field_name' => '',
                'id' => '',
            ],
        ],

        /*
        * Store information about your contacts with marge fields.
        *
        * @see https://mailchimp.com/help/manage-audience-signup-form-fields/
        */
        'merge_fields' => [
            [
                /*
                * The Mailchimp tag
                */
                'tag'=> null,

                /*
                * Blueprint field name to use for the merge field
                */
                'field_name' => null,
            ],
        ],

        /*
        * Field that contains the primary email address
        * Default: 'email'
        */
        'primary_email_field' => 'email',

        /*
        * Mailchimp Tag to assign to the contact.
        * NOTE: `tag_field` takes precedence over `tag`
        *
        * @see https://mailchimp.com/help/getting-started-tags/
        */
        'tag' => null,

        /*
        * Field to indicate which Mailchimp Tag to use
        *
        * @see https://mailchimp.com/help/getting-started-tags/
        */
        'tag_field' => null,
    ],

    /*
     * The form submissions to add to your Mailchimp Audiences
     */
    'forms' => [
        [
            /*
            * Handle of the form to listen for
            */
            'form' => null,

            /*
            * MailChimp audience id to subscribe users to
            *
            * @see https://mailchimp.com/help/find-audience-id/.
            */
            'audience_id' => null,

            /*
            * Field name used to check for consent.
            * Default: 'consent'
            */
            'check_consent' => true,

            /*
            * if you're checking for consent, which field is it? Defaults to `'consent'`
            */
            'consent_field' => 'consent',

            /*
            * Disable Double Opt In. Not typically a best practice.
            * Default: `false`
            *
            * See: https://mailchimp.com/help/single-opt-in-vs-double-opt-in/ for details
            */
            'disable_opt_in' => false,

            /*
            * if you'd like to add "interests" in a group, which field is collecting those ids? Defaults to 'interests'
            */
            'interests_field' => 'interests',

            /*
            * Field name used to indicate marketing permissions.
            * Default: `gdpr`
            */
            'marketing_permissions_field' => 'gdpr',

            /*
            * Fields used to store marketing permission ids.
            * Run `php please mailchimp:permissions <form-handle>` to get the ids.
            */
            'marketing_permissions_field_ids' => [
                [
                    'field_name' => '',
                    'id' => '',
                ],
            ],

             /*
            * Store information about your contacts with marge fields.
            *
            * @see https://mailchimp.com/help/manage-audience-signup-form-fields/
            */
            'merge_fields' => [
                [
                    /*
                    * The Mailchimp tag
                    */
                    'tag' => null,

                    /*
                    * Blueprint field name to use for the merge field
                    */
                    'field_name' => null,
                ],
            ],

             /*
            * Field that contains the primary email address
            * Default: 'email'
            */
            'primary_email_field' => 'email',

            /*
            * Mailchimp Tag to assign to the contact.
            * NOTE: `tag_field` takes precedence over `tag`
            *
            * @see https://mailchimp.com/help/getting-started-tags/
            */
            'tag' => null,

            /*
            * Field to indicate which Mailchimp Tag to use
            *
            * @see https://mailchimp.com/help/getting-started-tags/
            */
            'tag_field' => null,
        ],
    ],

    /*
     * The listName to use when no listName has been specified in a method.
     */
    'defaultListName' => 'subscribers',
```

## Usage

Create your Statamic [forms](https://statamic.dev/forms#content) as usual. Don't forget to add the consent field to your blueprint.

### Interests
You will need to know the `id`s of the interests in order to add them to your form:

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

### Marketing Permissions

To work with Mailchimp's [Marketing Permissions](https://mailchimp.com/help/collect-consent-with-gdpr-forms/) you need to do a few things:

1. Get your permissions and ids by running `php artisan mailchimp:permissions {form-handle}` for each of the forms that are in Mailchimp. For example, mine look like:

```
❯ php please mailchimp:permissions contact_us
+-------------------------------+------------+
| Marketing Permission          | ID         |
+-------------------------------+------------+
| Email                         | 2d904xxxxx |
| Customized Online Advertising | 3560exxxxx |
+-------------------------------+------------+
```

2. Add those Mailchimp's config
3. Add the form field that will have those permissions

![permissions](https://raw.githubusercontent.com/silentzco/statamic-mailchimp/main/images/marketing-permissions.png).

Then in your form, have fields like this:
```
<div class="form-group">
    <label>GDPR</label>
    <label for=""email>Email</label>
    <input type="checkbox" name="gdpr[email]" value="true" class="form-control"/>
    <label for=""email>Online</label>
    <input type="checkbox" name="gdpr[customized_online_advertising]" value="true" class="form-control"/>
</div>
```

Don't forget to add the `gdpr` field to your form's blueprint.

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

Please see [SECURITY](SECURITY.md) for details.
