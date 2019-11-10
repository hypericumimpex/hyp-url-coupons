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

namespace SkyVerge\WooCommerce\PluginFramework\v5_5_0;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\v5_5_0\\SV_WC_Coupon_Compatibility' ) ) :

/**
 * WooCommerce coupon compatibility class.
 *
 * This was introduced as an additional compatibility handler when support for WooCommerce 3.0 was added.
 * It is not part of the Framework, as only URL Coupon uses it for the time being.
 *
 * TODO: remove this class by version 2.12.0 {WV 2019-11-01}
 *
 * @since 5.2.0
 */
class SV_WC_Coupon_Compatibility extends SV_WC_Data_Compatibility {


	/**
	 * Gets a coupon object.
	 *
	 * @since 5.2.0
	 * @deprecated since 5.5.0
	 *
	 * @param int|\WP_Post|\WC_Coupon $coupon_id A coupon identifier or object.
	 * @return null|\WC_Coupon
	 */
	public static function get_coupon( $coupon_id ) {

		wc_deprecated_function( __METHOD__, '5.5.0', 'new WC_Coupon()' );

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
