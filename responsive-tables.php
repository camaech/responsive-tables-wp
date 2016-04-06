<?php
/*
* Plugin Name: Responsive Tables
* Description: Responsive Tables based on Zurb's Responsive Tables
* Version: 1.0
* Author: Blue Door Consulting, LLC
* Author URI: http://www.bluedoorconsulting.com
*/

if(!defined('ABSPATH')) die();
class ResponsiveTables {
	static $add_script;

	function __construct(){
		add_shortcode( 'table', array(&$this,'responsive_tables_shortcode') );

		add_action('wp_footer', array(&$this, 'responsive_tables_print_script'));
	}

	function responsive_tables_shortcode( $atts, $content = null ) {
		self::$add_script = true;

		$first = strpos($content, '<br />');
		if($first == 0) {
			$content = preg_replace("/<br[^>]*?>/", "", $content, 1);
		}

		$content = preg_replace("/<\/p[^>]*?>/", "", $content, 1);
		$content = preg_replace("/<p[^>]*?>/", "", $content);
		$content = str_replace("</p>", "<br />", $content);

		return $this->csv_to_table($content, $atts);
	}


	function csv_to_table($content,$args){
		$row_array = $this->csv_to_array($content, $args);

		echo "<table class='responsive'>";
		foreach($row_array as &$row) {
			echo "<tr>";
			foreach($row as &$cell) {
			  echo "<td>".$cell."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}

	function csv_to_array($content,$args){
		$data = explode('<br />', $content);

		$rows = array();
		foreach ($data as &$row) {
			$row_data = explode(',', $row);
			$single = array();
			foreach($row_data as &$r) {
		  	$single[] = trim($r);
			}
			$rows[] = $single;
		}

		array_pop($rows); // remove empty last element

		return $rows;
	}

	function responsive_tables_print_script() {
		if ( ! self::$add_script )
			return;

		wp_print_scripts('responsive-tables-js');
		wp_print_styles('responsive-tables-css');
	}
} //end class

add_action('init', 'Responsive_Tables_Plugin');
function Responsive_Tables_Plugin() {
	if (class_exists('ResponsiveTables')) {
		new ResponsiveTables();
	}
}

function responsive_tables_register_script() {
	wp_register_script('responsive-tables-js', plugins_url('responsive-tables.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('init', 'responsive_tables_register_script');

function responsive_tables_register_style() {
	wp_register_style( 'responsive-tables-css', plugins_url( 'responsive-tables.css', __FILE__) );
}
add_action('init', 'responsive_tables_register_style');
?>
