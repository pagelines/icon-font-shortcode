<?php
/*
Plugin Name: Icon Font Shortcode
Plugin URI: http://pagelinestheme.com/icon-font-shortcode?ref=pluginurilink
Description: A PageLines plugin that lets you use a shortcode instead of HTML code to output an icon font, specifically for Font Awesome. Example usage: [i]icon-bolt icon-4x icon-spin icon-border pull-right[/i]. See <a href="http://fortawesome.github.com/Font-Awesome/#examples" target="_blank">Font Awesome Examples</a>. Please see the <a href="http://www.pagelinestheme.com/icon-font-shortcode?ref=plugindescriptiontext" target="_blank">plugin documentation</a>.
Version: 1.0.2013.03.04.00
Author: Clifford Paulick
Author URI: http://tourkick.com/
Pagelines: true
Tags: extension
Demo: http://pagelinestheme.com/icon-font-shortcode?ref=pl-demo-link#demo
External: http://tourkick.com/?ref=pliconfontshortcodeexternallink
*/

// Check Framework
add_action('pagelines_setup', 'ifs_init' );
function ifs_init() {
    if( !function_exists('ploption') ) {
        return;
    }
}

class PL_Icon_Font_Shortcode {

	function __construct() {
		$this->base_dir	= plugin_dir_path( __FILE__ );
		$this->base_url = plugins_url( '', __FILE__ );
		$this->icon		= plugins_url( '/icon.png', __FILE__ );
		$this->less		= $this->base_dir . '/icon-font-shortcode.less';
		add_filter( 'pagelines_lesscode', array( &$this, 'get_less' ), 10, 1 );
		add_action( 'admin_init', array( &$this, 'admin_page' ) );
		add_action( 'init', array( &$this, 'add_shortcode' ) );
	}

	function get_less( $less ){
		$less .= pl_file_get_contents( $this->less );
		return $less;
	}

	function add_shortcode() {
		add_shortcode( 'i', array( &$this, 'iconfontshortcode' ) );
	}

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
	/*
	shortcode code references:
	 http://betterwp.net/17-protect-shortcodes-from-wpautop-and-the-likes/
	 http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
	color:
	 e.g. #00ff00, white, rgba(255, 0, 0, 0.5), etc. -- https://developer.mozilla.org/en-US/docs/CSS/color -- predefined color names: https://developer.mozilla.org/en-US/docs/CSS/color_value#Color_keywords
	font-size:
	 e.g. 250%, 1.6em, 30px, small, larger, xx-large, x-small, etc. -- https://developer.mozilla.org/en-US/docs/CSS/font-size
	*/



// Get settings from PageLines Site Options
if( ploption('ifs_id') && !empty($id) ) {
	 $id = ploption('ifs_id') . ' ' . $id; // add a space
	} else {
	 $id = ploption('ifs_id');
	}
if( ploption('ifs_class') ) {
	$class = ploption('ifs_class') . ' ' . $class; // add a space
	}
$linkclass = (ploption('ifs_linkclass')) ? ploption('ifs_linkclass') : 'iconfont'; // no 'if' because if it is blank, we just set a default



	// sanitization reference: http://wp.tutsplus.com/tutorials/creative-coding/data-sanitization-and-validation-with-wordpress/
	$thecolor = esc_html( $color );
	$thefontsize = esc_html( $fontsize );
	$theid = esc_html( $id );
	  $theid = strtolower($theid); // just because (even though plugin CSS does not target any IDs
	$theclass = esc_html( $class );
	  $theclass = strtolower($theclass); // icon font and plugin selectors are lower-case
	$thelinkclass = esc_html( $linkclass );
	  $thelinkclass = strtolower($thelinkclass); // plugin selectors are in lower-case
	$thelink = esc_html( $link );
	$thelinktarget = esc_html( $target );


// Display an error message if the shortcode is used but no icon is specified (because then it just spits out an empty <i></i>, if anything at all.
// Does not protect from using the shortcode without closing the shortcode.
if ( stripos($theclass, "icon-") === false ) {
	if ( !current_user_can('edit_posts') /* && !current_user_can('edit_pages') */ ) {
		return;
	} else {
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


	// $x
	if(!empty($theid)){
		$x = 'id="' . $theid . '" class="' . $theclass . '"';
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


	// $z
	if(empty($linkcodebefore)){
		$z = '<i ' .$x . $y . ' aria-hidden="true"></i>';
	} else {
		$z = $linkcodebefore . '<i ' .$x . $y . ' aria-hidden="true"></i>' . $linkcodeafter;
	}
	// aria-hidden="true"
	/* References:
		http://css-tricks.com/examples/PseudoIconTest/
		http://www.w3.org/TR/wai-aria/states_and_properties#aria-hidden
		http://www.screenreader.net/ ( http://vimeo.com/36137650 )
		https://chrome.google.com/webstore/detail/chromevox/kgejglhpjiefppelpmljglcjbhoiplfn
	FYI: Font Awesome already ignored by screen readers (per http://phpxref.pagelines.com/nav.html?less/icons.less.source.html )
	*/

	// final result
	$debug = 0;

	if($debug == 0){
		return $z;
	} else {
		if(empty($x)) {
			return 'empty';
		} else {
			return 'not empty';
		}
	}


} // end of shortcode





	// Add PageLines settings
	function admin_page() {
		if ( ! function_exists( 'ploption' ) )
			return;
		$option_args = array(
			'name'		=> 'icon_font_shortcode', // no spaces allowed
			'title'		=> 'Icon Font Shortcode', // name of admin page title
			'array'		=> $this->options_array(),
			'icon'		=> $this->icon,
			'position'	=> 5
		);
		pl_add_options_page( $option_args );
	}

	// PageLines Site Options settings ( see /wp-content/themes/pagelines/admin/class.options.engine.php around Line 26 for all possible settings fields )
	function options_array(){
	  $options = array(
		'ifs_intro' => array(
			'docslink'	=> 'http://www.pagelinestheme.com/icon-font-shortcode?ref=plsettingspagedocslink',
			'type'		=> 'multi_option',
			'title'		=> __('Icon Font Shortcode Options', 'icon-font-shortcode'),
			'shortexp'	=> __('If desired, you can add your own IDs and classes to apply to all icon font shortcodes, to assist in global styling.<br />To add multiple, separate each with a space.<br /><span style="font-size:130%;"><strong>All fields are optional.</strong></span><br /><br />Quick Access (links open in a new window):<br /><span style="padding-left:30px;"><a href="http://fortawesome.github.com/Font-Awesome/#icons-new" target="_blank">Font Awesome icon fonts</a></span><br /><span style="padding-left:30px;"><a href="http://fortawesome.github.com/Font-Awesome/#examples" target="_blank">Font Awesome examples</a></span><br /><span style="padding-left:30px;">BONUS: LESS CSS <em>child theme</em> code to <a href="http://www.pagelinestheme.com/icon-font-shortcode/icon-font-bullets" target="_blank">choose an icon font icon as your site-wide bullets</a> &mdash; password is <em>iloveiconfonts</em></span><br /><span style="padding-left:30px;">Plugin documentation (click the "VIEW DOC" link to the right)</span>', 'icon-font-shortcode'),
			'selectvalues'	=> array(
				'ifs_id' => array(
					'type'			=> 'text',
					'inputlabel'	=> __('ID(s) &mdash; Default: N/A<br /><span style="padding-left:30px;">Adds this ID to all icon font shortcode outputs.</span>', 'icon-font-shortcode')
				),
				'ifs_class' => array(
					'type'			=> 'text',
					'inputlabel'	=> __('Class(es) &mdash; Default: N/A<br /><span style="padding-left:30px;">Adds this CLASS to all icon font shortcode outputs.</span>', 'icon-font-shortcode')
				),
				'ifs_linkclass' => array(
					'type'			=> 'text',
					'inputlabel'	=> __('Link Class(es) &mdash; Default: <em>iconfont</em><br /><span style="padding-left:30px;">Consider leaving blank and targeting the default link class with CSS/LESS. But it is here if you want to use it.</span>', 'icon-font-shortcode')
				)
			  )
		),
	  );
	return $options;
	}







} // end of plugin class

new PL_Icon_Font_Shortcode;

// End of plugin
// Do not add closing PHP tag