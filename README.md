Mailchimp
=================

A Statamic V2 add-on for Mailchimp that subscribes a user to a specific mailing list when they register.

## Installing
1. Copy the "addons" folder contents to your Statamic `site` directory;
2. Do the same to the files inside the `settings` directory;
3. Configure the "mailchimp.yaml" file with your custom values:
  * mailchimp_key - from your list - Settings - List name and defaults
4. Run `php please addons:refresh` to load the addon's dependencies.

## Usage

When a user is registered, they will be automatically subscribed to your mailing list.

## LICENSE

[MIT License](http://emd.mit-license.org)
