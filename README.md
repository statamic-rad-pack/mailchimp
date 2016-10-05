Mailchimp
=================

A Statamic V2 add-on for Mailchimp that subscribes a user to a specific mailing list when they register.

## Installing
1. Copy the "addons" folder contents to your Statamic `site` directory;
2. Configure the addon by visiting CP > Addons > Mailchimp and add:
  * Mailchimp list id - from your list - Settings - List name and defaults
  * Mailchimp key - from Account - Extras - API Keys
  * Formsets - formsets to watch for if you're using a standard form instead of user registration
3. Run `php please addons:refresh` to load the addon's dependencies.

## Usage

When a user is registered, they will be automatically subscribed to your mailing list.

When a form is submitted (that is in `formsets`), the `email` field will be used to subscribe to your mailing list.

## LICENSE

[MIT License](http://emd.mit-license.org)
