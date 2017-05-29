=== ACF-VC Integrator ===
Contributors: dejliglama, Frederik Rosendahl-Kaa
Tags: ACF, VisualComposer, VC, AdvancedCustomFields, Page builder,
Requires at least: 3.4
Tested up to: 4.6.1
Stable tag: 1.1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The ACF-VC Plugin puts a ACF element into your Visual Composer, making it easier than ever to use your custom created fields in your own page design.

== Description ==

Advanced Custom Fields right inside your Visual Composer

The ACF-VC Plugin puts a ACF element into your Visual Composer, making it easier than ever to use your custom created fields in your own page design.

All standard Advanced Custom Fields are supported, and easy to target with your own CSS classes for ultimative design possibilities.

Signup for a download and newsletter on [ACF-VC.com](http://ACF-VC.com) to get news about future releases.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/acf-vc-integrator directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. The plugin requires no configuration on itself, but requires Advanced Custom Fields AND Visual Composer plugins to be active.


== Frequently Asked Questions ==

__Does ACF-VC Integrator work with ACF-Pro ?__
No, the fields of the ACF Pro version is not supported EXCEPT the repeater field.


== Changelog ==

= 1.1.1 =
**Bug fixes**
- Fixes has_cap warning : https://wordpress.org/support/topic/has_cap-is-deprecated-since-version-2-0-0/

= 1.1 =
**New stuff**
- Support for the repeater field. Supporting ACF-Pro and the standalone repeater plugin
- Supports multiple select
- New and improved core structure to support reuse of functions for repeaterfields.

**Bug fixes**
- Error when no taxonomy was selected
- ACF Pro check for plugin
- Error message if ACF or ACF-pro is missing
- Logo Icon was gone on the VC element 

= 1.0 =
- First version of the plugin supporting ACF version 4.4.5 and Visual Composer version 4.8.0.1
