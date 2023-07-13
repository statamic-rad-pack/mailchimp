# Manage Mailchimp newsletters in Statamic
[![Latest Version](https://img.shields.io/github/v/release/statamic-rad-pack/mailchimp)](https://github.com/statamic-rad-pack/mailchimp/releases)

This package provides an easy way to integrate MailChimp with Statamic forms and user registrations.

## Requirements

* PHP 8.1+
* Statamic v4

## Updating from < 2.2

The package name changed, so to update please run `composer remove edalzell/mailchimp` then follow the instructions below.

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

Publish the config file to `config/mailchimp.php` run:

```bash
php artisan vendor:publish --tag="mailchimp-config"
```

Configure Mailchimp in the Control Panel
![control panel](https://raw.githubusercontent.com/statamic-rad-pack/mailchimp/main/images/config.png)
![merge fields](https://raw.githubusercontent.com/statamic-rad-pack/mailchimp/main/images/merge-fields.png)

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
❯ plz mailchimp:permissions contact_us
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
