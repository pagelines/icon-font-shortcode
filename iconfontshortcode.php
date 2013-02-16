<?php
/*
Plugin Name: Icon Font Shortcode
Plugin URI: http://pagelinestheme.com/icon-font-shortcode
Description: Allows you to use a shortcode instead of HTML code to output an icon font, specifically for Font Awesome. Example usage: [i]icon-bolt icon-4x icon-spin icon-border pull-right[/i]. See <a href="http://fortawesome.github.com/Font-Awesome/#examples" target="_blank">Font Awesome Examples</a>. If you don't have Font Awesome but do have Bootstrap Icons, you can still use the shortcode but reference <a href="http://twitter.github.com/bootstrap/base-css.html#icons" target="_blank">Bootstrap Icon Glyphs</a> (scroll down to the "How to use" section) for more information.
Version: 1.0.2013.02.16.00
Author: Clifford Paulick
Author URI: http://tourkick.com/
Pagelines: true
Tags: extension
Demo: http://pagelinestheme.com/icon-font-shortcode?ref=pl-demo-link
External: http://tourkick.com/?ref=pl-icon-font-shortcode
*/

class PL_Icon_Font_Shortcode {

	function __construct() {

		$this->base_url = sprintf( '%s/%s', WP_PLUGIN_URL,  basename(dirname( __FILE__ )));

		// need to create an icon if an options page is added
		//$this->icon = $this->base_url . '/icon.png';

		$this->base_dir = sprintf( '%s/%s', WP_PLUGIN_DIR,  basename(dirname( __FILE__ )));

		$this->base_file = sprintf( '%s/%s/%s', WP_PLUGIN_DIR,  basename(dirname( __FILE__ )), basename( __FILE__ ));

		// register plugin hooks...
		$this->plugin_hooks();


		// call in the shortcode
		add_action( 'init', array( &$this, 'iconfontshortcode_init' ) );

	}

	function plugin_hooks(){
		// Always run
		add_filter( 'pagelines_lesscode', array( &$this, 'get_less' ), 10, 17 );
	}

	function get_less($less){
		$less .= pl_file_get_contents( $this->base_dir.'/iconfontshortcode.less' );
		return $less;
	}

	function iconfontshortcode_init() {
		// code to enable shortcodes in widgets (from http://hackadelic.com/the-right-way-to-shortcodize-wordpress-widgets )
		// not needed because PageLines theme already enables (see class.shortcodes.php), but in a slightly different way
		/*
		if (!is_admin()) {
			add_filter('widget_text', 'do_shortcode', 11);
		}
		*/

		// the shortcode, called in above
		add_shortcode( 'i', array( &$this, 'iconfontshortcode' ) );
	}


	/*
	shortcode code references:
	 http://betterwp.net/17-protect-shortcodes-from-wpautop-and-the-likes/
	 http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
	*/
	function iconfontshortcode($atts, $iconfontclasses = ''){ //start of shortcode
		extract(
			shortcode_atts(array(
				'color' => '', // e.g. #00ff00, white, rgba(255, 0, 0, 0.5), etc. -- https://developer.mozilla.org/en-US/docs/CSS/color -- predefined color names: https://developer.mozilla.org/en-US/docs/CSS/color_value#Color_keywords
				'fontsize' => '', // e.g. 250%, 1.6em, 30px, small, larger, xx-large, x-small, etc. -- https://developer.mozilla.org/en-US/docs/CSS/font-size
				'spanid' => '',
				'spanclass' => ''
			), $atts)
		  );

	// sanitization reference: http://wp.tutsplus.com/tutorials/creative-coding/data-sanitization-and-validation-with-wordpress/
	$thecolor = esc_html( $color );
	$thefontsize = esc_html( $fontsize );
	$theiconfontclasses = esc_html( $iconfontclasses );
	$thespanid = esc_html( $spanid );
	$thespanclass = esc_html( $spanclass );

	// create the icon HTML code
	$theiconcode = '<i class="' . $theiconfontclasses . '"></i>';

	// used empty ( http://php.net/manual/en/function.empty.php -- http://www.zachstronaut.com/posts/2009/02/09/careful-with-php-empty.html ) because zero is not a valid value

	// step 1
	if(!empty($thespanid) && !empty($thespanclass)){
		$x = '<span id="' . $thespanid . '" class="' . $thespanclass . '"';
	} elseif(!empty($thespanid) && empty($thespanclass)){
		$x = '<span id="' . $thespanid . '"';
	} elseif(empty($thespanid) && !empty($thespanclass)){
		$x = '<span class="' . $thespanclass . '"';
	} else {
		$x = '';
	}

	// step 2
	if (empty($x)){
		if(!empty($thecolor) && !empty($thefontsize)){
			$y = '<span style="color:' . $thecolor . '; font-size:' . $thefontsize . ';">';
		} elseif(!empty($thecolor) && empty($thefontsize)){
			$y = '<span style="color:' . $thecolor . ';">';
		} elseif(empty($thecolor) && !empty($thefontsize)){
			$y = '<span style="font-size:' . $thefontsize . ';">';
		} else {
			$y = '';
		}
	} else {
		if(!empty($thecolor) && !empty($thefontsize)){
			$y = ' style="color:' . $thecolor . '; font-size:' . $thefontsize . ';">';
		} elseif(!empty($thecolor) && empty($thefontsize)){
			$y = ' style="color:' . $thecolor . ';">';
		} elseif(empty($thecolor) && !empty($thefontsize)){
			$y = ' style="font-size:' . $thefontsize . ';">';
		} else {
			$y = '>';
		}
	}

	// step 3
	if(empty($x) && empty($y)){
		$z = $theiconcode;
	} else {
		$z = $x . $y . $theiconcode . '</span>';
	}

	// just do it
	return $z;


} // end of shortcode

} // end of plugin class

new PL_Icon_Font_Shortcode;


// End of plugin
// Do not add closing PHP tag