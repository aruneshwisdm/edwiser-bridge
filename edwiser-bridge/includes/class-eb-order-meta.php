<?php
/**
 * This class defines all code necessary to manage user's course orders meta'.
 *
 * @link       https://edwiser.org
 * @since      1.0.0
 * @package    Edwiser Bridge.
 * @author     WisdmLabs <support@wisdmlabs.com>
 */

namespace app\wisdmlabs\edwiserBridge;

/**
 * Order Meta.
 */
class Eb_Order_Meta {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The current version of this plugin.
	 */
	private $version;

	/**
	 * Contsructor.
	 *
	 * @param text $plugin_name plugin name.
	 * @param text $version plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}
	/**
	 * Meta boxes.
	 */
	public function add_eb_order_meta_boxes() {
		$status_hit = new Eb_Order_History_Meta( $this->plugin_name, $this->version );
		add_meta_box( 'eb_order_status_update_history_meta', __( 'Order status history', 'eb-textdomain' ), array( $status_hit, 'add_order_status_history_meta' ), 'eb_order', 'side', 'default' );
		add_meta_box( 'eb_order_refund_meta', __( 'Refund order', 'eb-textdomain' ), array( $this, 'add_order_refund_meta' ), 'eb_order', 'advanced', 'default' );
	}

	/**
	 * Refund meta
	 */
	public function add_order_refund_meta() {
		global $post;
		$refundable = get_post_meta( $post->ID, 'eb_transaction_id', true );
		if ( ! $refundable || empty( $refundable ) ) {
			esc_html_e( 'Refund not available for this order', 'eb-textdomain' );
			return;
		}
		$currency       = eb_get_current_paypal_currency_symb();
		$price          = $this->get_course_price( $post->ID );
		$refunds        = $this->get_orders_all_refund( $post->ID );
		$refunded_amt   = get_total_refund_amt( $refunds );
		$avl_refund_amt = $price - $refunded_amt;
		?>
		<div class="eb-order-refund-data">
			<?php $this->disp_refunds( $refunds ); ?>
			<table class="eb-order-refund-unenroll">
				<tbody>
					<?php do_action( 'eb_before_order_refund_meta' ); ?>
					<tr>
						<td>
							<?php esc_html_e( 'Suspend course enrollment?: ', 'eb-textdomain' ); ?>
						</td>
						<td>
							<input type="checkbox" name="eb_order_meta_unenroll_user" id="eb_order_meta_unenroll_user" value="ON" />
						</td>
					</tr>
					<tr>
						<td>
							<?php esc_html_e( 'Purchase cost: ', 'eb-textdomain' ); ?>
						</td>
						<td>
							<label class="eb-ord-cost"><?php echo esc_html( $currency . $price ); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<?php esc_html_e( 'Amount already refunded: ', 'eb-textdomain' ); ?>
						</td>
						<td>
							<label class="eb-ord-refunded-amt">- <?php echo esc_html( $currency . $refunded_amt ); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<?php esc_html_e( 'Total available to refund: ', 'eb-textdomain' ); ?>
						</td>
						<td>
							<label class="eb-ord-avlb-refund-amt"><?php echo esc_html( $currency . $avl_refund_amt ); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<?php esc_html_e( 'Refund amount: ', 'eb-textdomain' ); ?>
						</td>
						<td>
							<input type="text" id="eb_ord_refund_amt" min="0" max="<?php echo esc_html( $avl_refund_amt ); ?>" name="eb_ord_refund_amt" placeholder="0.00"/>
						</td>
					</tr>
					<tr>
						<td>
							<?php esc_html_e( 'Reason for refund (optional): ', 'eb-textdomain' ); ?>
						</td>
						<td>
							<input type="text" id="eb_order_refund_note" name="eb_order_refund_note" />
						</td>
					</tr>
					<?php do_action( 'eb_after_order_refund_meta' ); ?>
				</tbody>
			</table>
			<div class="eb-ord-refund-btn-cont">
				<?php do_action( 'eb_before_order_refund_meta_button' ); ?>
				<button type="button" class="button-primary" id="eb_order_refund_btn" name="eb_order_refund_btn" >
					<?php echo esc_html__( 'Refund', 'eb-textdomain' ) . esc_html( ' ' . $currency . ' ' ); ?>
					<span id="eb-ord-refund-amt-btn-txt">0.00</span>
				</button>
				<?php
				do_action( 'eb_after_order_refund_meta_button' );
				wp_nonce_field( 'eb_order_refund_nons_field', 'eb_order_refund_nons' );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Current price.
	 *
	 * @param text $order_id order_id.
	 */
	private function get_course_price( $order_id ) {
		$order_data = get_post_meta( $order_id, 'eb_order_options', true );
		$price      = get_arr_value( $order_data, 'price', '0.00' );
		return (float) $price;
	}

	/**
	 * Current price.
	 *
	 * @param text $order_id order_id.
	 */
	public function get_orders_all_refund( $order_id ) {
		$refunds = get_post_meta( $order_id, 'eb_order_refund_hist', true );
		if ( ! is_array( $refunds ) ) {
			$refunds = array();
		}
		return $refunds;
	}

	/**
	 * Current price.
	 *
	 * @param text $refunds refunds.
	 */
	private function disp_refunds( $refunds ) {
		?>
		<ul class="eb-order-refund-hist-cont">
			<?php
			foreach ( $refunds as $refund ) {
				$refund_by = get_arr_value( $refund, 'by' );
				$time      = get_arr_value( $refund, 'time' );
				$amt       = get_arr_value( $refund, 'amt' );
				$currency  = get_arr_value( $refund, 'currency' );
				?>
				<li>
					<div class="eb-order-refund-hist-stmt"><?php esc_html__( 'Refunded by', 'eb-textdomain' ) . printf( '%1$s on %2$s', esc_html( $refund_by ), esc_html( gmdate( 'F j, Y, g:i a', $time ) ) ); ?></div>
					<div class="eb-order-refund-hist-amt"><?php echo esc_html( "$currency$amt" ); ?></div>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}

	/**
	 * Get details of an order by order id.
	 *
	 * @since  1.0.0
	 *
	 * @param int $order_id id of an order.
	 */
	public function get_order_details( $order_id ) {
		// get order billing id & email.
		$order_data = get_post_meta( $order_id, 'eb_order_options', true );

		if ( ! is_array( $order_data ) ) {
			$order_data = array();
		}

		if ( isset( $order_data['buyer_id'] ) && ! empty( $order_data['buyer_id'] ) ) {
			$buyer_id_js_on_decoded = json_decode( $order_data['buyer_id'] );

			$buyer_id = $order_data['buyer_id'];
			if ( isset( $buyer_id_js_on_decoded->buyer_id ) && ! empty( $buyer_id_js_on_decoded->buyer_id ) ) {
				$buyer_id = $buyer_id_js_on_decoded->buyer_id;
			}
			$buyer_details = get_userdata( $buyer_id );
			$this->print_buyer_details( $buyer_details->data );
		} else {
			$this->print_buyer_details();
		}

		$this->print_product_details( $order_id, $order_data );
	}

	/**
	 * Get details of an buyer.
	 *
	 * @since  1.0.0
	 *
	 * @param int $buyer_details buyer_details.
	 */
	public function print_buyer_details( $buyer_details = '' ) {

		$user_id = 0;
		if ( isset( $buyer_details->ID ) && ! empty( $buyer_details->ID ) ) {
			$user_id = $buyer_details->ID;
		}

		?>
		<div class='eb-order-meta-byer-details'>
			<p>
				<strong><?php esc_html_e( 'Buyer Details: ', 'eb-textdomain' ); ?></strong>
			</p>
			<?php
			if ( isset( $buyer_details->user_email ) && ! empty( $buyer_details->user_email ) ) {
				?>
				<p>
					<label><?php esc_html_e( 'Name: ', 'eb-textdomain' ); ?></label>
					<?php echo esc_html( $buyer_details->user_login ); ?>
				</p>

				<p>

					<label><?php esc_html_e( 'Email: ', 'eb-textdomain' ); ?></label>
					<?php echo esc_html( $buyer_details->user_email ); ?>
				</p>
				<?php
			} else {
				?>
				<p>
					<label><?php esc_html_e( 'Name: ', 'eb-textdomain' ); ?></label>
					<!-- <input type="select" name="eb_order_options[eb_order_username]"> -->
					<div>
						<select id="eb_order_username" name="eb_order_options[eb_order_username]" required>
						<?php
						echo esc_html( $this->get_all_users( $user_id ) );
						?>
						</select>
					</div>
				</p>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Get details of an prodct.
	 *
	 * @since  1.0.0
	 *
	 * @param int $order_id order_id.
	 * @param int $order_data order_data.
	 */
	private function print_product_details( $order_id, $order_data ) {
		$course_id = 0;
		if ( isset( $order_data['course_id'] ) && ! empty( $order_data['course_id'] ) ) {
			$course_id = $order_data['course_id'];
		}

		?>
		<div class='eb-order-meta-details'>
			<p>
				<strong><?php esc_html_e( 'Order Details: ', 'eb-textdomain' ); ?></strong>
			</p>
			<p>
				<label><?php esc_html_e( 'Id: ', 'eb-textdomain' ); ?></label>
				<?php echo esc_html( $order_id ); ?>
			</p>

			<?php
			if ( $course_id ) {
				?>

				<p>
					<label><?php esc_html_e( 'Course Name: ', 'eb-textdomain' ); ?></label>
					<a href='<?php echo esc_html( get_permalink( $order_data['course_id'] ) ); ?>'>
						<?php echo esc_html( get_the_title( $course_id ) ); ?>
					</a>
				</p>

				<?php
			} else {
				?>
				<p>
					<label><?php esc_html_e( 'Course Name: ', 'eb-textdomain' ); ?></label>
					<!-- <input type="text" name="eb_order_options[eb_order_course]"> -->
					<div>
						<select id="eb_order_course" name="eb_order_options[eb_order_course]" required>
						<?php
						echo esc_html( $this->get_all_courses( $course_id ) );
						?>
						</select>

					</div>
				</p>
				<?php
			}
			?>
			<p>
				<label>
					<?php esc_html_e( 'Date: ', 'eb-textdomain' ); ?>
				</label>
				<?php echo get_the_date( 'Y-m-d H:i', $order_id ); ?>
			</p>
		</div>
		<?php
	}


	/**
	 * Function to get all users array.
	 *
	 * @param int $user_id user_id.
	 * @return returns array of users
	 */
	public function get_all_users( $user_id = '' ) {
		$users = get_users();
		$html  = "<option value='' disabled selected> Select User</option>";
		foreach ( $users as $user ) {
			if ( $user_id ) {
				$selected = '';
				if ( $user_id === $user->ID ) {
					$selected = 'selected';
				}
				$html .= '<option value="' . $user->ID . '" ' . $selected . '> ' . $user->user_login . '</option>';
			} else {
				$html .= '<option value="' . $user->ID . '" > ' . $user->user_login . '</option>';
			}
		}

		return $html;
	}

	/**
	 * Function to get list of all courses
	 *
	 * @param int $course_id course_id.
	 *
	 * @return array of all courses with ID
	 */
	public function get_all_courses( $course_id = '' ) {
		$course_args = array(
			'post_type'      => 'eb_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		$courses     = get_posts( $course_args );
		$html        = "<option value='' disabled selected> Select Course </option>";

		foreach ( $courses as $course ) {
			if ( $course_id ) {
				$selected = '';
				if ( $course_id === $course->ID ) {
					$selected = 'selected';
				}
				$html .= '<option value="' . $course->ID . '" ' . $selected . '> ' . $course->post_title . '</option>';
			} else {
				$html .= '<option value="' . $course->ID . '" > ' . $course->post_title . '</option>';
			}
		}

		return $html;
	}
}
