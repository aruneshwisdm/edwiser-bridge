<?php
/**
 * This class contains functionality to handle actions of custom buttons implemented in settings page
 *
 * @link       https://edwiser.org
 * @since      1.0.0
 *
 * @package    Edwiser Bridge
 * @subpackage Edwiser Bridge/admin
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace app\wisdmlabs\edwiserBridge;

class Eb_Settings_Ajax_Initiater {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Contsructor.
	 *
	 * @param text $plugin_name plugin_name.
	 * @param text $version version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Initiate course synchronization process.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return
	 */
	public function course_synchronization_initiater() {
		if ( ! isset( $_POST['_wpnonce_field'] ) ) {
			die( 'Busted!' );
		}

		// verifying generated nonce we created earlier.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce_field'] ) ), 'check_sync_action' ) ) {
			die( 'Busted!' );
		}

		// get sync options.
		$sync_options = json_decode( sanitize_text_field( wp_unslash( $_POST['sync_options'] ) ), true );

		// start working on request.
		$response = edwiser_bridge_instance()->course_manager()->course_synchronization_handler( $sync_options );
		echo json_encode( $response );
		die();
	}

	/**
	 * Initiate user data synchronization process.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return
	 */
	public function user_data_synchronization_initiater() {
		if ( ! isset( $_POST['_wpnonce_field'] ) ) {
			die( 'Busted!' );
		}

		// verifying generated nonce we created earlier.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce_field'] ) ), 'check_sync_action' ) ) {
			die( 'Busted!' );
		}
		// Added offset for user get limit.
		$offset = $_POST['offset'];
		// get sync options.
		$sync_options = json_decode( sanitize_text_field( wp_unslash( $_POST['sync_options'] ) ), true );

		$response = edwiser_bridge_instance()->user_manager()->user_course_synchronization_handler( $sync_options, false, $offset );

		echo json_encode( $response );
		die();
	}
	/**
	 * Initiate user link to moodle synchronization process.
	 *
	 * @since    1.4.1
	 * @access   public
	 *
	 * @return
	 */
	public function users_link_to_moodle_synchronization() {
		if ( ! isset( $_POST['_wpnonce_field'] ) ) {
			die( 'Busted!' );
		}

		// verifying generated nonce we created earlier.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce_field'] ) ), 'check_sync_action' ) ) {
			die( 'Busted!' );
		}
		// Added offset for user get limit.
		$offset = $_POST['offset'];
		// get sync options.
		$sync_options = json_decode( sanitize_text_field( wp_unslash( $_POST['sync_options'] ) ), true );

		//$response = edwiserBridgeInstance()->userManager()->user_course_synchronization_handler( $sync_user_courses );
		$response = edwiser_bridge_instance()->user_manager()->user_link_to_moodle_handler( $sync_options, $offset );

		echo json_encode( $response );
		die();
	}

	/**
	 * Test connection between wordpress and moodle.
	 *
	 * Calls connection_test_helper() from EBConnectionHelper class.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return boolean true on success else false
	 */
	public function connection_test_initiater() {
		if ( ! isset( $_POST['_wpnonce_field'] ) ) {
			die( 'Busted!' );
		}

		// verifying generated nonce we created earlier.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce_field'] ) ), 'check_sync_action' ) ) {
			die( 'Busted!' );
		}

		// start working on request.
		$url   = sanitize_text_field( wp_unslash( $_POST['url'] ) );
		$token = sanitize_text_field( wp_unslash( $_POST['token'] ) );

		$connection_helper = new EBConnectionHelper( $this->plugin_name, $this->version );
		$response          = $connection_helper->connection_test_helper( $url, $token );

		if ( $response['success'] == 0 ) {
			$response['response_message'] .= esc_html__( ' : to know more about this error', 'eb-textdomain' ) . "<a href='https://edwiser.helpscoutdocs.com/collection/85-edwiser-bridge-plugin' target='_blank'>" . esc_html__( ' click here', 'eb-textdomain' ) . '</a>';
		}

		echo json_encode( $response );
		die();
	}
}
