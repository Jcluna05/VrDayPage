=== Slick Menu ===

Plugin Name: Slick Menu
Contributors: XplodedThemes
Author: XplodedThemes
Author URI: https://www.xplodedthemes.com
Tags: menu, push menu, sidebar, mega menu, menu widgets
Requires at least: 3.8
Tested up to: 5.5.0
Stable tag: trunk
Requires PHP: 5.4+
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advanced Vertical Menu with multi-level functionality that allows endless nesting of navigation elements. Automatically integrates with your existing Wordpress menus

== Description ==

Slick Menu is more than just a menu plugin. It can be used to create unlimited Multi Level Vertical Menus or Sidebars with rich content and multiple style options and animation effects.

Every menu level is customizable featuring background colors, images, videos, overlays. Insert rich content by simply adding shortcodes and widgets directly within WordPress Nav Menu editor. 

== Installation ==

Installing "Slick Menu" can be done by following these steps:

1. Download the plugin via "CodeCanyon.net" 
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

#### V.1.2.8 - 01.09.2020
- **fix**: Minor fixes

#### V.1.2.7 - 14.01.2020
- **fix**: Fixed issue with Icon Picker

#### V.1.2.6 - 17.12.2019
- **fix**: Remove / replace deprecated functions
- **Enhance**: Faster backend

#### V.1.2.5 - 22.11.2019
- **fix**: Fixed issue with license validation.

#### V.1.2.4 - 06.11.2019
- **Fix**: Minor fixes

#### V.1.2.3 - 30.10.2019
- **Enhance**: Better caching. Save all menu caches in 1 transient instead of multiple to avoid slowing down the database

#### V.1.2.2 - 29.10.2019
- **Support**: Support WordPress v5.2.4

####  V.1.2.1 - 24.10.2019
- **fix**: Fixed issue caused by v1.1.9. Overflow of the menu is not scrollable on mobile devices particularly iphone.

####  V.1.2.0 - 21.10.2019
- **support**: Better RTL support for the backend

####  V.1.1.9 - 14.10.2019
- **enhance**: Disable page content scrolling on both mobile and desktop devices once a menu is active leaving scrolling enabled only within the menu.

####  V.1.1.8 - 10.09.2019
- **fix**: Fixed issue with jQuery UI stylesheets overriding default WordPress styles in pages that are not relevant to Slick Menu.

####  V.1.1.7.1 - 23.08.2019
- **fix**: Minor backend CSS fixes

####  V.1.1.7 - 05.06.2019
- **fix**: Fixed issue with scroll to anchor feature.

####  V.1.1.6 - 29.05.2019
- **fix**: Fixed issue: "Activate Current Menu Item on Menu Open" option not working with "Always Visible" menu on Mobile Breakpoint.

####  V.1.1.5 - 12.04.2019
- **fix**: Fixed issue: "Activate Current Menu Item on Menu Open" option not working with "Always Visible" menu.

####  V.1.1.4 - 04.04.2019
- **fix**: Fixed licensing issue

####  V.1.1.3 - 01.04.2019
- **new**: Added new menu option to exclude loading a menu for one or multiple pages / posts. This also applies to "Always Visible" menus.

####  V.1.1.2 - 28.03.2019
- **fix**: Fixed licensing issue

####  V.1.1.1 - 01.02.2019
- **fix**: Fixed conflict with iThemes Security plugin

####  V.1.1.0 - 17.01.2019
- **fix**: Fix deprecated functions warning
- **fix**: Fix post updating issue after inserting a slickmenu shortcode / widget within a post / page
- **update**: Update SCSS compiler

####  V.1.0.9.8 - 22.06.2018
- **new**: Added option to set logged in user avatar as logo

####  V.1.0.9.7 - 16.06.2018
- **fix**: Added Google Font API Key setting within global settings

####  V.1.0.9.6 - 19.05.2018
- **fix**: Fix issue with scroll to section
- **fix**: Fixed issue causing php warning message: "count(): Parameter must be an array"

####  V.1.0.9.5 - 30.03.2018
- **new**: Added animation option to footer section
- **new**: New Apple Menu Demo
- **enhance**: Load modernizr using wp_enqueue_script instead of ajax
- **enhance**: Do not close menu before loading a new page when clicking a menu item
- **fix**: Fixed issue with svg icon color not being applied in some cases
- **fix**: Fix issue with site crashing when using Polylang plugin (However, slick menu still cannot be translated using Polylang. Only supports WPML for now. )

####  V.1.0.9.4 - 26.06.2017
- **fix**: Fixed issue with the "Activate Current Menu Item on Menu Open" option
- **fix**: Fixed minor CSS issues

####  V.1.0.9.3 - 21.05.2017
- **new**: Added global option to load Dynamic CSS Internally instead of using an external link.
- **new**: Google fonts are now enqueued normally instead of using css @import

####  V.1.0.9.2 - 13.05.2017
- **fix**: Fix issues with menus loaded on HTTPS websites
- **fix**: Removed menu item Label Truncate option for now, unstable.

####  V.1.0.9.1 - 09.05.2017
- **new**: Added menu item Label Visibility option (Show on hover / Hide on hover)
- **new**: Added menu item Label Truncate option (Truncate to 1 to 5 lines)
- **fix**: Fix dynamic css caching bug
- **fix**: Minor CSS Fixes

####  V.1.0.9 - 09.05.2017
- **new**: Added background pattern & overlay options to level headers and footers
- **new**: New Extension Slick Menu - Dynamic Posts
- **fix**: Fix menu caching bug when using Nav Menu Roles
- **fix**: Minor CSS Fixes
- **support**: Support PHP 5.4

####  V.1.0.8.2 - 25.04.2017
- **fix**: Fixed issue with license activation caused by minor code change in earlier version

####  V.1.0.8.1 - 24.04.2017
- **fix**: Minor customizer bug fixes.
- **fix**: Fixed mobile breakpoint for always visible menu, hide by default

####  V.1.0.8 - 22.04.2017
- **new**: Added option to select a page to be used as content for the Level Description.
- **new**: Added 3 new Menu Animations (Fade In / Scale In / Scale Out)
- **new**: Level width can now be set using %, px, vw
- **new**: Level menu item heights can now be set using %, px, vh
- **new**: Added Level Footer Minimum Height option
- **enhance**: Faster Ajax Menu Build Request
- **enhance**: Faster menu settings / customizer loading
- **enhance**: Faster dynamic css loading
- **enhance**: Restructure Extensions Code
- **update**: Update Modernizr Library
- **fix**: Fixed issue with local license being reset by it self.
- **fix**: Fixed compatibility issue with Nav Menu Roles
- **fix**: Lazyload Modernizr fixes issue with some themes
- **fix**: Minor CSS Fixes

####  V.1.0.7 - 05.03.2017
- **new**: Added more transforms options for menu items. (Initial State / Visible State / Hover State)
- **new**: Added Perspective / Perspective Origin / Transition Delay & 3D Rotate option to menu items transforms section.
- **new**: Added Box Shadow options for menu items
- **new**: Added option to show Back link icon only
- **new**: Added option to replace menu item with an image instead.
- **new**: Auto hide top sticky back link on scroll
- **new**: Enable content filters on mobile
- **new**: Added option to override item title
- **fix**: Fixed issue with menu item links opening in the same window while their target is set to open in a new window.
- **fix**: Fix issue with menu to menu triggers no being triggered properly
- **fix**: Minor CSS fixes

####  V.1.0.6 - 04.02.2017
- **new**: Added option to scroll level automatically to current level item
- **new**: Added option to show Level Scrollbars in Level General Settings
- **new**: Added option to automatically open current menu item level once menu is open
- **new**: Added Hamburger Trigger Label option. Label can be positioned above, below, before or after the hamburger trigger
- **new**: Added Hamburger Trigger Visibility option. Show on All, Mobile Only or Desktop Only
- **fix**: Fix cache issue with WPML
- **fix**: Fixed javascript bug causing a conflict with the Beaver Page Builder
- **fix**: Animate.css - Prefixed all animation classes to avoid conflicts with other plugins / themes

####  V.1.0.5.1 - 21.12.2016
- **new**: Added new JS API function SlickMenu.openSubLevel(menu_id, menu_item_id, callback) to programatically open a menu sub level. More Info
- **enhance**: License System now allows the same purchase code to be valid within a multisite setup.
  1 License: unlimited domains, subdomains, folders as long as as it is under a multisite.
- **enhance**: License System now allows you to revoke the license locally.

####  V.1.0.5 - 12.12.2016
- **new**: Added a 12 column grid system allowing you to insert menu items in multiple columns and create a grid of menu items. See new demo (Full Screen Gallery)
- **new**: Added the option to add images Above, Below or Behind a menu item. See new demo (Full Screen Gallery)
- **new**: Added the option to set filters to menu item images
- **new**: Added the option to set a different menu item height for each item
- **new**: Added the option to have a sticky / over the content header / footer
- **new**: Added global option to Fade In site content once Slick Menus are fully loaded
- **support**: Added better support for Jupiter Theme
- **support**: Added better support for BeTheme Theme
- **support**: Added better support for Berg Theme
- **fix**: Minor CSS Fixes
- **fix**: License Validation Fix
- **fix**: Fixed customizer issues on WordPress 4.7
- **fix**: Fixed menu item icons on regular menus that are not activated as Slick Menu

####  V.1.0.4 - 26.10.2016
- **new**: Level description now supports shortcodes
- **new**: Added option to set a url to the logo. Scroll to anchor also supported.
- **new**: Added a Flush Cache option. Note: Saving menu settings also flushes the cache.
- **support**: Added better support for Avada Theme
- **support**: Added better support for Kinetika Theme
- **support**: Added better support for Tower Theme
- **enhance**: Replaced Menu Level Title h2 tags with a regular div tags to avoid affecting SEO
- **enhance**: Force override existing theme trigger behaviour when adding a custom trigger selector to a Slick Menu
- **update**: Updated Modernizr Library
- **fix**: Minor CSS Fixes
- **fix**: Fix issue with full width revolution slider when a menu is active.
- **fix**: Customizer: Fixed social networks color picker not working when adding a new network

####  V.1.0.3.4 - 29.09.2016
- **new**: Updated translation files
- **new**: Added mobile fallback for the Site Wrapper Videos
- **new**: Added Content Filter option for individual menu levels
- **new**: Added Transparent and Half Transparent Content Filters
- **new**: Added Mobile Centered Level Option that forces everything to be centered on mobile breakpoint
- **new**: Added W3 Total Cache and WP Super Cache support to automatically flush the cache whenever menu settings are changed.
- **extension**: New Import / Export extension released. More Info
- **fix**: Minor CSS Fixes

####  V.1.0.3.3 - 21.09.2016
- **fix**: Fixed minor bug with the plugin updater

####  V.1.0.3.2 - 19.09.2016
- **support**: Added better support for Bridge Theme

####  V.1.0.3.1 - 17.09.2016
- **support**: Added better support for Yolo Naveda Theme

####  V.1.0.3 - 16.09.2016
- **new**: Added option to select a different menu animation on mobile (only supported ones)
- **enhance**: Added PHP 5.3 compatibility
- **enhance**: Improved mobile menus performance
- **enhance**: Merge all Google Font imports into 1 for a faster page load.
- **fix**: Fixed menu scroll on Android devices

####  V.1.0.2 - 09.09.2016
- **fix**: Fixed intermittent bug with back button on Firefox
- **support**: Added better support for The 7 Theme

####  V.1.0.1 - 07.09.2016
- **fix**: Fixed javascript event bug on Firefox

####  V.1.0.0 - 06.09.2016
- **initial**: Initial Version

