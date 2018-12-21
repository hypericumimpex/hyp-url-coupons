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
 * @package     WC-URL-Coupons/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\URL_Coupons;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 2.7.0
 *
 * @method \WC_URL_Coupons get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Upgrades the plugin to the current version.
	 *
	 * @since 2.7.0
	 *
	 * @param string $installed_version current version
	 */
	protected function upgrade( $installed_version ) {

		if ( ! empty( $installed_version ) ) {

			$update_path = array(
				'1.0.2' => 'update_to_1_0_2',
				'2.0.0' => 'update_to_2_0_0',
				'2.1.1' => 'update_to_2_1_1',
				'2.5.1' => 'update_to_2_5_1',
			);

			foreach ( $update_path as $update_to_version => $update_script ) {

				if ( version_compare( $installed_version, $update_to_version, '<' ) ) {

					// special handling for 2.5.0 => 2.5.1
					if ( '2.5.1' === $update_to_version && ! version_compare( $installed_version, '2.5.0', '=' ) ) {
						continue;
					}

					$this->$update_script();
				}
			}
		}
	}


	/**
	 * Update to v1.0.2
	 *
	 * @since 2.7.0
	 *
	 * Prior versions had a bug where any coupons trashed would not remove the associated unique URL from the active list,
	 * resulting in "coupon does not exist" errors when the unique URLs were visited.
	 * This wasn't a very visible problem with very unique URLs, but becomes a serious problem
	 * when someone uses "/checkout/" as the unique URL.
	 */
	private function update_to_1_0_2() {

		// load active coupon list
		$coupons = (array) get_option( 'wc_url_coupons_active_urls', array() );

		// iterate through post IDs
		foreach ( $coupons as $coupon_id => $coupon_data ) {

			// if coupon doesn't exist or is not published, remove from active list
			if ( 'publish' !== get_post_status( $coupon_id ) ) {
				unset( $coupons[ $coupon_id ] );
			}
		}

		// update active list
		update_option( 'wc_url_coupons_active_urls', $coupons );

		// clear transient
		delete_transient( 'wc_url_coupons_active_urls' );

		$this->get_plugin()->log( 'Updated to version 1.0.2' );
	}


	/**
	 * Update to v2.0.0
	 *
	 * Two changes to the coupon data:
	 *
	 * 1) "force apply" is now called "defer apply"
	 * 2) prior versions didn't support the redirect page type and while we can (and do) use `page` as the default,
	 *    it's nicer to have the redirect page type set properly
	 */
	private function update_to_2_0_0() {

		$coupons = (array) get_option( 'wc_url_coupons_active_urls', array() );

		foreach ( $coupons as $coupon_id => $data ) {

			// force => defer
			$coupons[ $coupon_id ]['defer'] = isset( $coupons[ $coupon_id ]['force'] ) ? $coupons[ $coupon_id ]['force'] : false;

			if ( $coupons[ $coupon_id ]['defer'] ) {

				if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

					if ( $coupon = Framework\SV_WC_Coupon_Compatibility::get_coupon( $coupon_id ) ) {

						Framework\SV_WC_Coupon_Compatibility::update_meta_data( $coupon, '_wc_url_coupons_defer_apply', 'yes' );
					}

				} else {

					update_post_meta( $coupon_id, '_wc_url_coupons_defer_apply', 'yes' );
				}
			}

			// remove force
			unset( $coupons[ $coupon_id ]['force'] );

			if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

				if ( $coupon = Framework\SV_WC_Coupon_Compatibility::get_coupon( $coupon_id ) ) {

					Framework\SV_WC_Coupon_Compatibility::delete_meta_data( $coupon, '_wc_url_coupons_force_apply' );
				}

			} else {

				delete_post_meta( $coupon_id, '_wc_url_coupons_force_apply' );
			}

			// update redirect page type
			if ( empty( $data['redirect'] ) ) {
				continue;
			}

			$post_type = get_post_type( $data['redirect'] );

			// no existing redirects should be set to these post types, but just in case
			if ( ! $post_type ) {
				$post_type = 'page';
			} elseif ( 'product_variation' === $post_type ) {
				$post_type = 'product';
			}

			$coupons[ $coupon_id ]['redirect_page_type'] = $post_type;
		}

		// update active list
		update_option( 'wc_url_coupons_active_urls', $coupons );

		// clear transient
		delete_transient( 'wc_url_coupons_active_urls' );

		$this->get_plugin()->log( 'Updated to version 2.0.0' );
	}


	/**
	 * Update to version 2.1.1
	 *
	 * @since 2.7.0
	 *
	 * Prior versions didn't update the redirect post type coupon meta.
	 */
	private function update_to_2_1_1() {

		$coupons = (array) get_option( 'wc_url_coupons_active_urls' );

		foreach ( $coupons as $coupon_id => $data ) {

			// update redirect page type
			if ( empty( $data['redirect'] ) ) {
				continue;
			}

			if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

				if ( $coupon = Framework\SV_WC_Coupon_Compatibility::get_coupon( $coupon_id ) ) {

					$redirect_page_type = Framework\SV_WC_Coupon_Compatibility::get_meta( $coupon, '_wc_url_coupons_redirect_page_type', true );

					if ( ! empty( $redirect_page_type ) ) {
						continue;
					}
				}

			} elseif ( get_post_meta( $coupon_id, '_wc_url_coupons_redirect_page_type', true ) ) {

				continue;
			}

			$post_type = get_post_type( $data['redirect'] );

			// no existing redirects should be set to these post types, but just in case
			if ( ! $post_type ) {
				$post_type = 'page';
			} elseif ( 'product_variation' === $post_type ) {
				$post_type = 'product';
			}

			if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

				if ( $coupon = Framework\SV_WC_Coupon_Compatibility::get_coupon( $coupon_id ) ) {

					Framework\SV_WC_Coupon_Compatibility::update_meta_data( $coupon, '_wc_url_coupons_redirect_page_type', $post_type );
				}

			} else {

				update_post_meta( $coupon_id, '_wc_url_coupons_redirect_page_type', $post_type );
			}
		}

		$this->get_plugin()->log( 'Updated to version 2.1.1' );
	}


	/**
	 * Update to version 2.5.1
	 *
	 * Upgrade to 2.5.1, only from 2.5.0 and if running WC 3.0+.
	 * Some data was incorrectly set for WC 3.0+ using v2.5.0 due to select2 upgrade.
	 *
	 * Note: the following update script wouldn't run anymore in newer versions of the plugin, but it's left here for reference.
	 *
	 * @since 2.7.0
	 */
	private function update_to_2_5_1() {

		$plugin = $this->get_plugin();

		if ( '2.5.1' === $plugin::VERSION && Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

			$coupons  = (array) get_option( 'wc_url_coupons_active_urls', array() );
			$new_data = array();

			foreach ( $coupons as $coupon_id => $data ) {

				$new_data[ $coupon_id ] = $data;

				// loose check: prior versions didn't properly save the redirect page ID
				if ( 0 == $data['redirect'] ) {

					// good news! coupon meta wasn't updated as as result of this error since checks failed, so we can still get it
					$coupon                             = Framework\SV_WC_Coupon_Compatibility::get_coupon( $coupon_id );
					$new_data[ $coupon_id ]['redirect'] = Framework\SV_WC_Coupon_Compatibility::get_meta( $coupon, '_wc_url_coupons_redirect_page' );
				}
			}

			update_option( 'wc_url_coupons_active_urls', $new_data );

			delete_transient( 'wc_url_coupons_active_urls' );
		}

		$plugin->log( 'Updated to version 2.5.1' );
	}


}
