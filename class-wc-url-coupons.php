<?php
/**
 * WooCommerce URL Coupons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce URL Coupons to newer
 * versions in the future. If you wish to customize WooCommerce URL Coupons for your
 * needs please refer to http://docs.woocommerce.com/document/url-coupons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;

/**
 * URL Coupons main plugin class.
 *
 * @since 1.0
 */
class WC_URL_Coupons extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '2.7.5';

	/** @var WC_URL_Coupons single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'url_coupons';

	/** @var \WC_URL_Coupons_Frontend instance */
	protected $frontend;

	/** @var \WC_URL_Coupons_Admin instance */
	protected $admin;

	/** @var \WC_URL_Coupons_Ajax instance */
	protected $ajax;

	/** @var \WC_URL_Coupons_Import_Export_Handler instance */
	protected $import_export_handler;


	/**
	 * Bootstrap plugin
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-url-coupons',
			)
		);

		$this->includes();
	}


	/**
	 * Loads and initializes the plugin lifecycle handler.
	 *
	 * @since 2.7.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-url-coupons-lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\URL_Coupons\Lifecycle( $this );
	}


	/**
	 * Builds the REST API handler instance.
	 *
	 * @since 2.7.3
	 */
	protected function init_rest_api_handler() {

		require_once( $this->get_plugin_path() . '/includes/api/class-wc-url-coupons-rest-api.php' );

		$this->rest_api_handler = new \SkyVerge\WooCommerce\URL_Coupons\REST_API( $this );
	}


	/**
	 * Includes required files.
	 *
	 * @since 2.0.0
	 */
	public function includes() {

		if ( is_admin() ) {

			// admin
			$this->admin = $this->get_admin_instance();

			if ( is_ajax() ) {
				$this->ajax = $this->load_class( '/includes/class-wc-url-coupons-ajax.php', 'WC_URL_Coupons_AJAX' );
			}
		}

		// if performing AJAX or not the admin at all
		if ( is_ajax() || ! is_admin() ) {

			// frontend
			$this->frontend = $this->load_class( '/includes/frontend/class-wc-url-coupons-frontend.php', 'WC_URL_Coupons_Frontend' );
		}

		// import/export handler
		$this->import_export_handler = $this->load_class( '/includes/class-wc-url-coupons-import-export-handler.php', 'WC_URL_Coupons_Import_Export_Handler' );
	}


	/**
	 * Returns the Admin instance.
	 *
	 * May be loaded in API context:
	 * @see \SkyVerge\WooCommerce\URL_Coupons\REST_API::handle_insert_shop_coupon_data()
	 *
	 * @since 2.3.0
	 *
	 * @return \WC_URL_Coupons_Admin
	 */
	public function get_admin_instance() {

		if ( null === $this->admin ) {
			$this->admin = $this->load_class( '/includes/admin/class-wc-url-coupons-admin.php', 'WC_URL_Coupons_Admin' );
		}

		return $this->admin;
	}


	/**
	 * Returns the Front End instance.
	 *
	 * @since 2.3.0
	 *
	 * @return \WC_URL_Coupons_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Returns the AJAX instance.
	 *
	 * @since 2.3.0
	 *
	 * @return \WC_URL_Coupons_Ajax
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the import/export handler instance.
	 *
	 * @since 2.4.0
	 *
	 * @return \WC_URL_Coupons_Import_Export_Handler
	 */
	public function get_import_export_handler_instance() {

		return $this->import_export_handler;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.2
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce URL Coupons', 'woocommerce-url-coupons' );
	}


	/**
	 * Returns the plugin main class file.
	 *
	 * @since 1.2
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/url-coupons/';
	}


	/**
	 * Returns the plugin documentation URL.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/url-coupons/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns the plugin configuration URL.
	 *
	 * @since 2.3.1
	 *
	 * @param string $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = null ) {

		return Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.4.0' ) ? admin_url( 'admin.php?page=wc-settings&tab=general' ) : admin_url( 'admin.php?page=wc-settings&tab=checkout' );
	}


	/**
	 * Returns the main plugin class instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @see wc_url_coupons()
	 *
	 * @since 1.3.0
	 *
	 * @return \WC_URL_Coupons
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}


/**
 * Returns the One True Instance of URL Coupons.
 *
 * @since 1.3.0
 *
 * @return \WC_URL_Coupons
 */
function wc_url_coupons() {

	return \WC_URL_Coupons::instance();
}
