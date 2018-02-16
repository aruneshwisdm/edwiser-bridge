<?php
/**
 * This class defines all code necessary to manage user's course orders meta'.
 *
 * @link       https://edwiser.org
 * @since      1.0.0
 *
 * @author     WisdmLabs <support@wisdmlabs.com>
 */
namespace app\wisdmlabs\edwiserBridge;

class EBOrderMeta
{

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

    public function __construct($pluginName, $version)
    {
        $this->plugin_name = $pluginName;
        $this->version     = $version;
    }

    public function addEbOrderMetaBoxes()
    {
        $statusHit = new EBOrderHistory($this->plugin_name, $this->version);
        add_meta_box("eb_order_status_update_history_meta", __("Order status history", "eb-textdomain"), array($statusHit, "addOrderStatusHistoryMeta"), "eb_order", 'side', 'default');
        add_meta_box("eb_order_refund_meta", __("Refund order", "eb-textdomain"), array($this, "addOrderRefundMeta"), "eb_order", 'advanced', 'default');
    }

    /**
     * Function adds the functionality to add the refund button on the eb_order meta box.
     * @param type $args contains the post types array.
     */
    public function addOrderRefundButton($args)
    {
        return;
        if ($args['args']['post_type'] == 'eb_order') {
            ?>
            <div class="eb-order-refund">
                <input class="eb-order-refund-btn-secondary" type="button" id="eb_order_refund" name="eb_order_refund" value="<?php _e("Refund", "eb-textdomain"); ?>" />
            </div>
            <?php
        }
    }

    public function addOrderRefundMeta()
    {
        global $post;
        $currency        = getCurrentPayPalcurrencySymb();
        $price           = $this->getCoursePrice($post->ID);
        $refunds         = $this->getOrdersAllRefund($post->ID);
        $refundedAmt     = $this->getTotalRefuncdAmt($refunds);
        $avlRefundAmt    = $price - $refundedAmt;
        ?>
        <div class="eb-order-refund-data">
            <?php $this->dispRefunds($refunds); ?>
            <table class="eb-order-refund-unenroll">
                <tbody>
                    <?php do_action("eb_before_order_refund_meta"); ?>
                    <tr>
                        <td>
                            <?php _e("Unenroll user from purchased courses?: ", "eb-textdomain"); ?>
                        </td>
                        <td>
                            <input type="checkbox" name="eb_order_meta_unenroll_user" id="eb_order_meta_unenroll_user" value="ON" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e("Purchase cost: ", "eb-textdomain"); ?>
                        </td>
                        <td>
                            <label class="eb-ord-cost"><?php echo $currency . $price; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e("Amount already refunded: ", "eb-textdomain"); ?>
                        </td>
                        <td>
                            <label class="eb-ord-refunded-amt">- <?php echo $currency . $refundedAmt; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e("Total available to refund: ", "eb-textdomain"); ?>
                        </td>
                        <td>
                            <label class="eb-ord-avlb-refund-amt"><?php echo $currency . $avlRefundAmt; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e("Refund amount: ", "eb-textdomain"); ?>
                        </td>
                        <td>
                            <input type="text" id="eb_ord_refund_amt" min="0" max="<?php echo $avlRefundAmt ?>" name="eb_ord_refund_amt" placeholder="0.00"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e("Reason for refund (optional): ", "eb-textdomain"); ?>
                        </td>
                        <td>
                            <input type="text" id="eb_order_refund_note" name="eb_order_refund_note" />
                        </td>
                    </tr>
                    <?php do_action("eb_after_order_refund_meta"); ?>
                </tbody>
            </table>
            <div class="eb-ord-refund-btn-cont">
                <?php do_action("eb_before_order_refund_meta_button"); ?>
                <button type="button" class="button-primary" id="eb_order_refund_btn" name="eb_order_refund_btn" >
                    <?php echo __("Refund", "eb-textdomain") . " " . $currency . " "; ?>
                    <span id="eb-ord-refund-amt-btn-txt">0.00</span>
                </button>
                <?php
                do_action("eb_after_order_refund_meta_button");
                wp_nonce_field("eb_order_refund_nons_field", "eb_order_refund_nons");
                ?>
            </div>
        </div>
        <?php
    }

    private function getCoursePrice($orderId)
    {
        $orderData = get_post_meta($orderId, 'eb_order_options', true);
        $price     = getArrValue($orderData, "price", "0.00");
        return (float) $price;
    }

    private function getOrdersAllRefund($orderId)
    {
        $refunds = get_post_meta($orderId, "eb_order_refund_hist", true);
        if (!is_array($refunds)) {
            $refunds = array();
        }
        return $refunds;
    }

    private function dispRefunds($refunds)
    {
        ?>
        <ul class="eb-order-refund-hist-cont">
            <?php
            foreach ($refunds as $refund) {
                $refndBy  = getArrValue($refund, "by");
                $time     = getArrValue($refund, "time");
                $amt      = getArrValue($refund, "amt");
                $currency = getArrValue($refund, "currency");
                ?>
                <li>
                    <div class="eb-order-refund-hist-stmt"><?php printf(__("Refunded by %s on %s", "eb-textdomain"), $refndBy, date("F j, Y, g:i a", $time)); ?></div>
                    <div class="eb-order-refund-hist-amt"><?php echo "$currency$amt"; ?></div>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    }

    public function getTotalRefuncdAmt($refunds)
    {
        $totalRefund = (float) "0.00";
        foreach ($refunds as $refund) {
            $refundAmt   = getArrValue($refund, "amt", "0.00");
            $totalRefund += (float) $refundAmt;
        }
        return $totalRefund;
    }

    /**
     * get details of an order by order id.
     *
     * @since  1.0.0
     *
     * @param int $order_id id of an order
     *
     * @return string order details
     */
    public function getOrderDetails($order_id)
    {
        //      get order billing id & email
        $order_data = get_post_meta($order_id, 'eb_order_options', true);

        if (!is_array($order_data)) {
            $order_data = array();
        }

        $byerDetails = get_userdata($order_data['buyer_id']);
        foreach ($order_data as $key => $value) {
            $value;
            if ($key == 'buyer_id') {
                $this->printByerDetails($byerDetails->data);
                $this->printProductDetails($order_id, $order_data);
            } else {
                continue;
            }
        }

        // get ordered item id
        $course_id = $order_data['course_id'];
        // return if order does not have an item(course) associated
        if (!is_numeric($course_id)) {
            return;
        }
    }

    private function printByerDetails($byerDetails)
    {
        ?>
        <div class='eb-order-meta-byer-details'>
            <p>
                <strong><?php _e('Buyer Details: ', 'eb-textdomain'); ?></strong>
            </p>
            <p>
                <label><?php _e('Name: ', 'eb-textdomain'); ?></label>
                <?php echo $byerDetails->user_login ?>
            </p>
            <p>
                <label><?php _e('Email: ', 'eb-textdomain'); ?></label>
                <?php echo $byerDetails->user_email ?>
            </p>
        </div>
        <?php
    }

    private function printProductDetails($order_id, $order_data)
    {
        ?>
        <div class='eb-order-meta-details'>
            <p>
                <strong><?php _e('Order Details: ', 'eb-textdomain'); ?></strong>
            </p>
            <p>
                <label><?php _e('Id: ', 'eb-textdomain'); ?></label>
                <?php echo $order_id; ?>
            </p>
            <p>
                <label><?php _e('Course Name: ', 'eb-textdomain') ?></label>
                <a href='<?php echo get_permalink($order_data['course_id']) ?>'>
                    <?php echo get_the_title($order_data['course_id']); ?>
                </a>
            </p>
            <p>
                <label>
                    <?php _e('Date: ', 'eb-textdomain'); ?> 
                </label>
                <?php echo get_the_date("Y-m-d H:i", $order_id); ?>
            </p>
        </div>
        <?php
    }
}
