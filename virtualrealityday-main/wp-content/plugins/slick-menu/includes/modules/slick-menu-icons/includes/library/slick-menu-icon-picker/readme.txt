=== SM SM Icon Picker ===

Tags: icons, image, svg
Requires at least: 4.3
Tested up to: 4.5.1
Stable tag: 0.4.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Pick an icon of your choice.


== Description ==
An icon picker library plugin.

== Screenshots ==
1. Icon selector
2. Icon fields in a post meta box using [CMB](https://github.com/humanmade/Custom-Meta-Boxes/)

== Frequently Asked Questions ==
= How do I use css file from CDN? =
You can use the `slick_menu_icon_picker_icon_type_stylesheet_uri` filter, eg:
`
/**
 * Load Font Awesome's CSS from CDN
 *
 * @param  string                $stylesheet_uri Icon type's stylesheet URI.
 * @param  string                $icon_type_id   Icon type's ID.
 * @param  SM_Icon_Picker_Type_Font $icon_type      Icon type's instance.
 *
 * @return string
 */
function myprefix_font_awesome_css_from_cdn( $stylesheet_uri, $icon_type_id, $icon_type ) {
	if ( 'fa' === $icon_type_id ) {
		$stylesheet_uri = sprintf(
			'https://maxcdn.bootstrapcdn.com/font-awesome/%s/css/font-awesome.min.css',
			$icon_type->version
		);
	}

	return $stylesheet_uri;
}
add_filter( 'slick_menu_icon_picker_icon_type_stylesheet_uri', 'myprefix_font_awesome_css_from_cdn', 10, 3 );
`

== Changelog ==

= 0.1.0 =
* Initial
