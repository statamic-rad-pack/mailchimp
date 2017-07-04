Mailchimp
=================

A Statamic V2 add-on for Mailchimp that subscribes a user to a specific mailing list when they register.

## Installing
1. Copy the "addons" folder contents to your Statamic `site` directory;
2. Configure the addon by visiting CP > Addons > Mailchimp and add:
  * Mailchimp key - from Account - Extras - API Keys
  * If you're going to add folks when they register, turn on Add New Users
  * Not recommended, but you can disable the Double Opt In
  * Whether or not you want to check permission before add them to a list (this is the first opt in)
  * If so, which user field is the check? The value must be truthy (true, on) when selected
  * Mailchimp list id - from your list - Settings - List name and defaults
  * A row for each form submission you'd like to add to a list
	* Mailchimp list id - from your list - Settings - List name and defaults
	* Not recommended, but you can disable the Double Opt In
  	* Form - formsets to watch for if you're using a standard form instead of user registration
  	* Whether or not you want to check permission before add them to a list (this is the first opt in)
  	* If so, which form field is the check? The value must be truthy (true, on) when selected
3. Run `php please addons:refresh` to load the addon's dependencies.

## Usage

When a user is registered, they will be automatically subscribed to your mailing list.

When a form is submitted (that is in `formsets`), the `email` field will be used to subscribe to your mailing list.

## LICENSE

[MIT License](http://emd.mit-license.org)

## Support on Beerpay
Hey dude! Help me out for a couple of :beers:!

[![Beerpay](https://beerpay.io/edalzell/mailchimp/badge.svg?style=beer-square)](https://beerpay.io/edalzell/mailchimp)  [![Beerpay](https://beerpay.io/edalzell/mailchimp/make-wish.svg?style=flat-square)](https://beerpay.io/edalzell/mailchimp?focus=wish)