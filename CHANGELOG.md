# Changelog

All notable changes to `statamic-mailchimp` will be documented in this file.

## v4.1.0 - 2023-07-19

### 🚀 New

- Update config to use tabs [@ryanmitchell](https://github.com/ryanmitchell) (#106)

### 🐛 Fixed

- Remove Spatie\Newsletter dependency and fix marketing permissions [@ryanmitchell](https://github.com/ryanmitchell) (#107)

## v4.0.1 - 2023-07-13

### 🐛 Fixed

- Fix license and versions [@edalzell](https://github.com/edalzell) (#103)

## v4.0.0 - 2023-07-12

- Moving addon to Statamic's Rad Pack [@edalzell](https://github.com/edalzell) (#102)

## v3.0.2 - 2023-06-28

### 🐛 Fixed

- Asset path [@edalzell](https://github.com/edalzell) (#99)

## v3.0.1 - 2023-06-28

### 🐛 Fixed

- Remove unused action [@edalzell](https://github.com/edalzell) (#98)
- Fix production asset build [@edalzell](https://github.com/edalzell) (#97)

## v3.0.0 - 2023-06-28

#### 🚀 New

- Support Statamic v4 [@edalzell](https://github.com/edalzell) (#95)

**Note**: The Marketing Permissions (used for GDPR) have been removed temporarily. Please stay on the previous version if need that.

### 🐛 Fixed

- Move up the consent field [@robdekort](https://github.com/robdekort) (#88)

### 🔧 Improved

- 🔄 synced file(s) with edalzell/.github [@edalzell](https://github.com/edalzell) (#96)

## 2.10 - 2023-01-27

- Support Statamic 3.4

## 2.9.1 - 2022-06-11

- Fix error when marketing permissions are null

## 2.9 - 2022-05-09

- Support Mailchimp's Marketing Permissions

## 2.8 - 2022-04-06

- Set the Mailchimp Tag from a form field
- Convert form fields to select boxes

## 2.7 - 2022-03-28

- Support Laravel 9 & Statamic 3.3

## 2.6 - 2022-03-06

- Can now add or update a subscription

## 2.5 - 2020-12-20

- Can send new user registrations to Mailchimp

## 2.4 - 2020-11-26

- Can now assign subscribers to Mailchimp Groups

## 2.3 - 2020-11-11

- Use Forma for settings

## 2.2.4 - 2020-10-31

- Re-enable config panel

## 2.2.3 - 2020-10-31

- Temporarily remove config panel
- Get tests working again

## 2.2.2 - 2020-10-31

- Handle missing config options better

## 2.2.1 - 2020-10-31

- Proper namespacing

## 2.2.0 - 2020-10-31

- Add config panel
- Add support for Mailchimp tags

## 2.1.0 - 2020-10-12

- Support for Laravel 8

## 2.0.0 - 2020-03-03

- Support for Statamic v3

## 1.4.0 - 2020-03-21

- Added: can override the email field per formset

## 1.3.8 - 2019-09-06

- Fixed: better handling of missing or malformed merge field data

## 1.3.7 - 2018-09-15

- Fixed: fixed error when visiting settings page

## 1.3.6 - 2018-08-15

- Fixed: opt in now works for user registration

## 1.3.5 - 2018-08-15

- Fixed: List is now populated when user registers, sorry about that

## 1.3.4 - 2018-07-16

- Added: Updated docs to better explain merge fields

## 1.3.3 - 2018-05-06

- Fixed: fixed JS error when toggling check permissions

## 1.3.2 - 2018-04-24

- Fixed: update member if they exist

## 1.3.1 - 2018-04-06

- Fixed: Only send merge_fields if you have some to prevent the API from b0rking

## 1.3.0 - 2018-03-19

- Fixed: Updated to match Statamic 2.8's yummy new addon goodness
- Added: User merge fields supported
