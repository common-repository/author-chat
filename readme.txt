=== Author Chat ===
Contributors: Piotr Pesta
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EEDF5TV3M2WVG&lc=US
Plugin Name: Author Chat
Tags: plugin, chat, author, for authors, admin, messages, internal chat, users chat, user, dashboard chat, dashboard, admin menu chat
Author: Piotr Pesta
Requires at least: 4.0
Tested up to: 5.7
Stable tag: 2.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Author Chat is an internal chat that let your authors (users with access to dashboard) to chat with each other. It is easy to use and supports private chat rooms.

== Description ==

[Author Chat](https://github.com/Pantsoffski/Author-Chat-Plugin) is an internal chat that let your authors (users with access to admin panel) to chat with each other. It is easy to use. All chat data is stored in database. You can also configure how many days chat history should be stored in database, change the interval time for the verification of new messages, show or hide our name in the messages and many more.

Author Chat now supports private chat/chat rooms.

You can also download Author Chat client for Android, and run chat on your smartphone. App is available here: [Google Play](https://play.google.com/store/apps/details?id=pl.ordin.authorchat)

If you wish to translate plugin, just add your translation file to /lang/ folder.

== Using 3rd party service ==

Please note that this plugin is relying on a 3rd party service, which is the Google Firebase Cloud Messaging service (FCM) and your data is being sent through their servers via HTTP API (https://fcm.googleapis.com/fcm/send). This is very legal to use the Google Firebase Cloud Messaging service (FCM), based on their terms and conditions https://firebase.google.com/terms/.

== Installation ==

1. Upload the zip to 'plugins' directory
2. Unzip (steps 1 and 2 can also be performed automatically)
3. Activate the plugin
4. Plugin is visible in a dashboard

Or just add .zip file as a new plugin in your Wordpress administration panel.

== Screenshots ==

1. Author Chat - stretched window on the right side.
2. Author Chat - stretched window on the right side of WYSWIG editor.
3. Author Chat - dashboard view.
4. Author Chat - options and smaller chat window.

== Changelog ==
= 2.0.3 =
* Minor bug fix.
= 2.0.2 =
* Fixed bug: "jQuery.fn.load() is deprecated"
* Wordpress 5.5 compatibility
* New translation: Dutch (thanks to Dave Krul)
= 2.0.1 =
* Fixed bug: not working add new chat room button.
* Wordpress 5.3 compatibility
= 2.0.0 =
* Added support for Android App (https://play.google.com/store/apps/details?id=pl.ordin.authorchat)
* Security fix
= 1.9.0 =
* From now chat time stamps are in user local time (not server time like before).
* New translation: persian (thanks to Javidnikoo).
* New translation: italian (thanks to Fabio).
= 1.8.2 =
* Bug fix for Wordpress mobile view (chat window is now invisible in mobile view).
* Temporary remove support for Android app.
= 1.8.1 =
* Small bug fix.
= 1.8.0 =
* Now you can create private chat rooms.
= 1.7.5 =
* Author Chat for Android - code optimization so you can send messages from smartphone
= 1.7.0 =
* Now you can download Author Chat client for Android from Google Play
* Wordpress 4.8 compatibility
= 1.6.0 =
* Bug fix
* Final 1.6.0 version
= 1.5.9 =
* Lot's of changes (!big thanks to [Pablo Custo](https://github.com/pablocusto) for his very hard work on this version!)
* Now you change the interval time for the verification of new messages from the AC Settings (1 to 10 secs).
* Incorporate the differentiation of nicknames by colors in their names as does WhatsApp.
* Possibility to show or hide our name in the messages (AC Settings).
* Big change of the chat window style.
* Code optimization.
= 1.5.1 =
* Now chat can be visible everywhere (in small draggable window), so you can chat within any page inside the admin
= 1.4.3 =
* Updated for Wordpress 4.7 (replaced deprecated get_currentuserinfo function by wp_get_current_user)
= 1.4.1 =
* Support for language files (now you can translate plugin via e.g. Poedit, just add your translation file to /lang/ folder)
* Polish translation included
= 1.4.0 =
* Simple fix - compatibility with custom user roles
* Now you can choose how to display the authors: by Login or by Name
= 1.3.0 =
* Now you can restrict access to Author Chat and exclude Editor, Author, Contributor or Subscriber
= 1.2.0 =
* In settings you can delete chat history
= 1.1.0 =
* Added information about new messages: number of messages in browser tab title and sound signal
= 1.0.0 =
* Bugfix: now plugin supports servers with <5.5 PHP version
= 0.9.9 =
* Initial Release