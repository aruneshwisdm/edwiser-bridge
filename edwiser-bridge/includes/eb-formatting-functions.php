<?php
/**
 * Formatting FUnctions.
 *
 * @link       https://edwiser.org
 * @since      1.0.0
 *
 * @package    Edwiser Bridge
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

if ( ! function_exists( 'wp_sanitize_tooltip' ) ) {
	/**
	 * Sanitize a string destined to be a tooltip. Prevents XSS.
	 *
	 * @param string $var var.
	 * @package Edwiser bridge.
	 * @return string
	 */
	function wp_sanitize_tooltip( $var ) {
		return wp_kses(
			html_entity_decode( $var ),
			array(
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'span'   => array(),
				'ul'     => array(),
				'li'     => array(),
				'ol'     => array(),
				'p'      => array(),
			)
		);
	}
}

if ( ! function_exists( 'wpClean' ) ) {
	/**
	 * DEPRECATED FUNCTION.
	 *
	 * Clean variables.
	 *
	 * @deprecated since 2.0.1 use wp_clean() insted
	 * @param string $var var.
	 *
	 * @return string
	 */
	function wpClean( $var ) {
		return sanitize_text_field( $var );
	}
}

if ( ! function_exists( 'wp_clean' ) ) {
	/**
	 * Clean variables.
	 *
	 * @param string $var var.
	 *
	 * @return string
	 */
	function wp_clean( $var ) {
		return sanitize_text_field( $var );
	}
}
