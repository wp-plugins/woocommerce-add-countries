<?php
/*
Plugin Name: Woocommerce Add Countries
Plugin URI: http://www.danieledesantis.net
Description: Woocommerce Add Countries is a lightweight plugin which allows you to add new countries to the Woocommerce countries list. Requires Woocommerce.
Version: 1.0
Author: Daniele De Santis
Author URI: http://www.danieledesantis.net
Text Domain: woocommerce-add-countries
Domain Path: /languages/
License: GPL2
*/

/*
Copyright 2014  Daniele De Santis  (email : info@danieledesantis.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH')) die ('No direct access allowed');

if(!class_exists('Wac'))
{
    class Wac
    {
        public function __construct() {
			add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'add_menu'));
			add_action('init', array($this, 'init'));
			add_action( 'plugins_loaded', array($this, 'apply_wac_filter') );				
        }
		
		public static function uninstall()
        {
			delete_option('wac_countries_list');	
        }
		
		public function admin_init()
		{
    		$this->init_settings();
		}
		     
		public function init_settings()
		{
			register_setting('wac-group', 'wac_countries_list');
			
		}
				
		public function woocommerce_add_countries( $country ) {
			$countries_list = get_option('wac_countries_list');
			$countries_list = explode("\r\n", $countries_list);
			foreach($countries_list as $single_country) {
				$single_country = explode(",", $single_country);
				$iso_code = trim($single_country[0]);
				$country_name = trim($single_country[1]);  				
				$country[$iso_code] = $country_name;
			}
			return $country; 
		}		
		
		public function init() {
			load_plugin_textdomain( 'woocommerce-add-countries', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
		
		public function apply_wac_filter() {
			add_filter( 'woocommerce_countries', array($this, 'woocommerce_add_countries'), 10, 1 );
		}
		
		public function add_menu() {
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    			global $wac_settings_page;
				$wac_settings_page = add_submenu_page('woocommerce', 'Woocommerce Add Countries', 'Add countries', 'manage_options', 'woocommerce-add-countries', array($this, 'wac_settings_page'));
			}
		}
		
		public function wac_settings_page() {
			echo '<div class="wrap">';
			echo '<h2>' . __('Woocommerce Add Countries', 'woocommerce-add-countries') . '</h2>
					<p>' . __('Add new countries to Woocommerce countries list by adding them to the text area.', 'woocommerce-add-countries') . '<br>'
					. __('Enter the desired ISO code and country name separated by a comma.', 'woocommerce-add-countries') . '<br>' 
					. __('Enter each country on a new line.', 'woocommerce-add-countries') . '</p>
					<p>' . __('Example:','woocommerce-add-countries') . '</p>
					<p>' . __('UK, United Kingdom','woocommerce-add-countries') . '<br>'
					. __('FR, France','woocommerce-add-countries') . '<br>'
					. __('DE, Germany','woocommerce-add-countries') . '</p>';				
			echo '<form id="wac_form" method="post" action="options.php">';
			settings_fields('wac-group');
			echo '<table class="form-table">  
					<tbody>
						<tr valign="top">
							<th scope="row">' . __('Countries:', 'woocommerce-add-countries') . '</th>
							<td><textarea id="wac_countries_list" name="wac_countries_list" style="min-height:200px;">' . get_option('wac_countries_list') . '</textarea>
							</td>
						</tr>
					</tbody>
				</table>';				
			echo submit_button( __('Save list', 'woocommerce-add-countries') );
			echo '</form>
				<h3>' . __('Credits', 'woocommerce-add-countries') . '</h3>
				<ul>
					<li>' . __('"Woocommerce Add Countries" is a plugin by <a href="http://www.danieledesantis.net/" target="_blank" title="Daniele De Santis">Daniele De Santis</a>.', 'woocommerce-add-countries') . '</li>
				</ul>
				</div>';
		}				
    }
}


if(class_exists('Wac')) {
	register_uninstall_hook(__FILE__, array('Wac', 'uninstall'));
	$wac = new Wac();
}

if(isset($wac)) {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		function wac_settings_link($links) {
			$settings_link = '<a href="admin.php?page=woocommerce-add-countries">' . __('Settings', 'woocommerce-add-countries') . '</a>';
			array_unshift($links, $settings_link);
			return $links; 
		}
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wac_settings_link');
	}
}
?>