<?php
/*
Plugin Name: Icon Font Shortcode
Plugin URI: http://pagelinestheme.com/icon-font-shortcode?ref=pluginurilink
Description: A PageLines plugin that lets you use a shortcode instead of HTML code to output an icon font, specifically for Font Awesome. Example usage: [i]icon-bolt icon-4x icon-spin icon-border pull-right[/i]. See <a href="http://fortawesome.github.com/Font-Awesome/#examples" target="_blank">Font Awesome Examples</a>. Please see the <a href="http://www.pagelinestheme.com/icon-font-shortcode?ref=plugindescriptiontext" target="_blank">plugin documentation</a>.
Version: 1.0.2013.02.20.00
Author: Clifford Paulick
Author URI: http://tourkick.com/
Pagelines: true
Tags: extension
Demo: http://pagelinestheme.com/icon-font-shortcode?ref=pl-demo-link
External: http://tourkick.com/?ref=pliconfontshortcodeexternallink
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

	function plugin_hooks(){ // Always run
		add_action( 'pagelines_setup', array( &$this, 'ifs_options' )); // PageLines Site Settings Options
		add_filter( 'pagelines_lesscode', array( &$this, 'get_less' ), 10, 17 ); // Plugin's LESS code
	}

	function get_less($less){
		$less .= pl_file_get_contents( $this->base_dir.'/icon-font-shortcode.less' );
		return $less;
	}


	// Add PageLines settings
	function ifs_options(  ){
	  $iconfontshortcode_options = array(
		'salebar_setup' => array(
			'docslink'	=> 'http://www.pagelinestheme.com/icon-font-shortcode?ref=plsettingspagedocslink',
			'type'		=> 'multi_option',
			'title'		=> __('Icon Font Shortcode Options', 'iconfontshortcode'),
			'shortexp'	=> __('If desired, you can add your own IDs and classes to apply to all icon font shortcodes, to assist in global styling.<br />To add multiple, separate each with a space.<br /><span style="font-size:130%;"><strong>All fields are optional.</strong></span><br /><br />Quick Access (links open in a new window):<br /><span style="padding-left:30px;"><a href="http://fortawesome.github.com/Font-Awesome/#icons-new" target="_blank">Font Awesome icon fonts</a></span><br /><span style="padding-left:30px;"><a href="http://fortawesome.github.com/Font-Awesome/#examples" target="_blank">Font Awesome examples</a></span><br /><span style="padding-left:30px;">Plugin documentation (click the "VIEW DOC" link to the right)</span>', 'iconfontshortcode'),
			'selectvalues'	=> array(
				'ifs_spanid' => array(
					'type'			=> 'text',
					'inputlabel'	=> __('Span Class(es) &mdash; Default: N/A<br /><span style="padding-left:30px;"><em>FYI:</em> Gets added to any class you add to an individual shortcode.</span>', 'iconfontshortcode')
				),
				'ifs_spanclass' => array(
					'type'			=> 'text',
					'inputlabel'	=> __('Span Class(es) &mdash; Default: N/A<br /><em><span style="padding-left:30px;">FYI:</em> Gets added to any class you add to an individual shortcode.</span>', 'iconfontshortcode')
				),
				'ifs_linkclass' => array(
					'type'			=> 'text',
					'inputlabel'	=> __('Link Class(es) &mdash; Default: <em>iconfont</em><br /><span style="padding-left:30px;"><span style="color:#b94a48;"><em>Warning:</em></span> If you do not leave this blank, do not forget to add your own styling.</span>', 'iconfontshortcode')
				)
			  )
	 )
	);

		$iconfontshortcode_settings = array(
			'name'		=> 'Icon Font Shortcode',
			'array'		=> $iconfontshortcode_options,
			'icon' 		=> $this->base_url.'/icon.png',
			'position'	=> 9
		);

		pl_add_options_page( $iconfontshortcode_settings );
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
	color:
	 e.g. #00ff00, white, rgba(255, 0, 0, 0.5), etc. -- https://developer.mozilla.org/en-US/docs/CSS/color -- predefined color names: https://developer.mozilla.org/en-US/docs/CSS/color_value#Color_keywords
	font-size:
	 e.g. 250%, 1.6em, 30px, small, larger, xx-large, x-small, etc. -- https://developer.mozilla.org/en-US/docs/CSS/font-size
	*/
	function iconfontshortcode($atts, $class = ''){ //start of shortcode
		extract(
			shortcode_atts(array(
				'color' => '',
				'fontsize' => '',
				'id' => '',
				'link' => '',
				'target' => ''
			), $atts)
		  );


// Get settings from PageLines Site Options
if( !ploption('ifs_spanid') ) {
	$id = ploption('ifs_spanid') . ' ' . $id; // add a space
	}
if( !ploption('ifs_spanclass') ) {
	$class = ploption('ifs_spanclass') . ' ' . $class; // add a space
	}
$linkclass = (ploption('ifs_linkclass')) ? ploption('ifs_linkclass') : 'iconfont'; // no 'if' because if it is blank, we just set a default



	// sanitization reference: http://wp.tutsplus.com/tutorials/creative-coding/data-sanitization-and-validation-with-wordpress/
	$thecolor = esc_html( $color );
	$thefontsize = esc_html( $fontsize );
	$thespanid = esc_html( $id );
	  /* $thespanid = strtolower($thespanid); */ // could if we wanted to
	$theclass = esc_html( $class );
	  $theclass = strtolower($theclass); // icon font CSS is case-sensitive
	$thelinkclass = esc_html( $linkclass );
	  /* $thelinkclass = strtolower($thelinkclass); */ // could if we wanted to
	$thelink = esc_html( $link );
	$thelinktarget = esc_html( $target );


// Display an error message if the shortcode is used but no icon is specified (because then it just spits out an empty span, if anything at all.
// Does not protect from using the shortcode without closing the shortcode.
if ( stripos($theclass, "icon-") === false ) {
	if ( !current_user_can('edit_posts') /* && !current_user_can('edit_pages') */ ) {
		return;
	} else {
		/* $errormessage = '<span class="iconfontshortcodeerrormessage">Icon Font Shortcode used without specifying an icon. (message only shown to post and page Editors)</span>'; */
		$errormessage = '<span class="iconfontshortcodeerrormessage">Icon Font Shortcode used without specifying an icon. (message only shown to Contributors and higher)</span>';
		return $errormessage;
	}
}


	// used empty ( http://php.net/manual/en/function.empty.php -- http://www.zachstronaut.com/posts/2009/02/09/careful-with-php-empty.html ) because zero is not a valid value

if(empty($thelink)) {
	$linkcodebefore = '';
	$linkcodeafter = '';
} elseif(empty($thelinktarget)){
	$linkcodebefore = '<a class="' . $thelinkclass . '" href="' . $thelink . '">';
	$linkcodeafter = '</a>';
} else {
	$linkcodebefore = '<a class="' . $thelinkclass . '" href="' . $thelink . '" target="_' . $thelinktarget . '">';
}

/*
	// if I wanted to add a span id to all shortcodes
	// $x
	if(!empty($thespanid)){
		$x = 'id="iconfont ' . $thespanid . '" class="' . $theclass . '"';
	} else {
		$x = 'id="iconfont" class="' . $theclass . '"';
	}
*/

	// $x
	if(!empty($thespanid)){
		$x = 'id="' . $thespanid . '" class="' . $theclass . '"';
	} else {
		$x = 'class="' . $theclass . '"';
	}

	// $y
	if(!empty($thecolor) && !empty($thefontsize)){
		$y = ' style="color:' . $thecolor . '; font-size:' . $thefontsize . ';"';
	} elseif(!empty($thecolor) && empty($thefontsize)){
		$y = ' style="color:' . $thecolor . ';"';
	} elseif(empty($thecolor) && !empty($thefontsize)){
		$y = ' style="font-size:' . $thefontsize . ';"';
	} else {
		$y = '';
	}

	// aria-hidden="true"
	/* References:
		http://css-tricks.com/examples/PseudoIconTest/
		http://www.w3.org/TR/wai-aria/states_and_properties#aria-hidden
		http://www.screenreader.net/ ( http://vimeo.com/36137650 )
		https://chrome.google.com/webstore/detail/chromevox/kgejglhpjiefppelpmljglcjbhoiplfn
	*/
	// $z
	if(empty($linkcodebefore)){
		$z = '<span ' .$x . $y . ' aria-hidden="true"></span>';
	} else {
		$z = $linkcodebefore . '<span ' .$x . $y . ' aria-hidden="true"></span>' . $linkcodeafter;
	}

// final result
return $z;


} // end of shortcode

} // end of plugin class

new PL_Icon_Font_Shortcode;


// End of plugin
// Do not add closing PHP tag