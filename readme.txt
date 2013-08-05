=== Email ===
Contributors: developdaly
Tags: email,  e-mail, wp-email, mail, wp_mail, send, email log
Requires at least: 3.5.2
Tested up to: 3.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Email users with custom templates when certain actions happen, such as new posts or updated custom post types and keep a log of sent emails.

== Description ==

[**Contribute on Github**](https://github.com/developdaly/Email) | [**Report Bugs**](https://github.com/developdaly/Email/issues?labels=bug&milestone=&page=1&state=open)

This plugin allows you configure and reconfigure emails that are sent to specified users when certain things happen.

Some examples (applies to custom post types as well):

* new post
* updated post
* deleted post
* (more coming soon)

Additionally, you control the email template users receive. Boilterplate templates are included to get you started.

== Installation ==

See [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Screenshots ==

1. Add new email
2. Email configuration with email template
3. Email log

== Changelog ==

= 1.1 =
* Fixes issue where emails were not sending for the "new" action
* Fixes issue where emails were attempting to send (but failing in most cases) for all posts types, even if not specified by the rule
* Adds logging of failed emails

= 1.0.2 =
* Improved Readme

= 1.0.1 =
* Improved Readme

= 1.0 =
* Initial release
