<?php
/**
 * WooCommerce Plugin Framework
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
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\PluginFramework\v5_4_1;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\v5_4_1\\SV_WC_Coupon_Compatibility' ) ) :

/**
 * WooCommerce coupon compatibility class.
 *
 * This was introduced as an additional compatibility handler when support for WooCommerce 3.0 was added.
 * It is not part of the Framework, as only URL Coupon uses it for the time being.
 *
 * TODO remove this class when WooCommerce 3.0 becomes the minimum supported version {FN 2018-0704}
 *
 * @since 5.2.0
 */
class SV_WC_Coupon_Compatibility extends SV_WC_Data_Compatibility {


	/** @var array mapped compatibility properties, as `$new_prop => $old_prop` */
	protected static $compat_props = array(
		'date_expires'       => 'expiry_date',
		'email_restrictions' => 'customer_email',
	);


	/**
	 * Gets a coupon property.
	 *
	 * @since 5.2.0
	 *
	 * @param \WC_Coupon $coupon The coupon data object.
	 * @param string $prop The property name.
	 * @param string $context If 'view' then the value will be filtered (default 'edit', returns the raw value).
	 * @param array $compat_props Compatibility properties.
	 * @return mixed
	 */
	public static function get_prop( $coupon, $prop, $context = 'edit', $compat_props = array() ) {

		return parent::get_prop( $coupon, $prop, $context, self::$compat_props );
	}


	/**
	 * Sets a coupons's properties.
	 *
	 * Note that this does not save any data to the database.
	 *
	 * @since 5.2.0
	 *
	 * @param \WC_Coupon $object The coupon object
	 * @param array $props The new properties as $key => $value.
	 * @param array $compat_props Compatibility properties.
	 * @return \WC_Data|\WC_Coupon
	 */
	public static function set_props( $object, $props, $compat_props = array() ) {

		return parent::set_props( $object, $props, self::$compat_props );
	}


	/**
	 * Gets a coupon object.
	 *
	 * @since 5.2.0
	 *
	 * @param int|\WP_Post|\WC_Coupon $coupon_id A coupon identifier or object.
	 * @return null|\WC_Coupon
	 */
	public static function get_coupon( $coupon_id ) {

		$coupon = null;

		if ( $coupon_id instanceof \WC_Coupon ) {

			$coupon = $coupon_id;

		} elseif ( $coupon_id instanceof \WP_Post ) {

			$coupon = new \WC_Coupon( $coupon_id->ID );

		} elseif ( is_numeric( $coupon_id ) ) {

			$post_title = wc_get_coupon_code_by_id( $coupon_id );
			$coupon     = new \WC_Coupon( $post_title );
		}

		return $coupon;
	}


}

endif;
