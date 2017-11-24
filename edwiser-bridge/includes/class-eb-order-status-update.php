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

class EBOrderStatus
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

    /**
     * Function initiates the refund it is ajax callback for the eb order refund refund.
     * @since 1.3.0
     * @param type $requestData
     */
    public function initEbOrderRefund()
    {
        check_ajax_referer("eb_order_refund_nons_field", "order_nonce");
        $orderId       = getArrValue($_POST, "eb_order_id");
        $refundData    = array(
            "refund_amt"            => getArrValue($_POST, "eb_ord_refund_amt"),
            "refund_note"           => getArrValue($_POST, "eb_order_refund_note", ""),
            "refund_unenroll_users" => getArrValue($_POST, "eb_order_meta_unenroll_user", "NO"),
        );
        $refundManager = new EBManageOrderRefund($this->plugin_name, $this->version);
        $refund        = $refundManager->initRefund();
        if ($refund) {
            $note = $this->getOrderRefundStatusMsg($orderId, $refundData);
            $this->saveOrderStatusHistory($orderId, $note);
            wp_send_json_success($refundData);
        } else {
            wp_send_json_error(array("msg" => "failed to refund"));
        }
    }

    /**
     * Callback function to save the order status history data.
     *
     * @since 1.3.0
     * @param numer $orderId current updated order id.
     * @return number order id
     */
    public function saveStatusUpdateMeta($orderId)
    {
        if (!current_user_can('edit_post', $orderId)) {
            return $orderId;
        }
        $nonce = getArrValue($_POST, 'eb_order_meta_nons');
        if (!wp_verify_nonce($nonce, "eb_order_history_meta_nons")) {
            return $orderId;
        }
        $note = $this->getStatusUpdateNote($orderId, $_POST);
        $this->saveOrderStatusHistory($orderId, $note);
    }

    public function saveNewOrderPlaceNote($orderId)
    {
        $ordDetail = get_post_meta($orderId, 'eb_order_options', true);
        $courseId  = getArrValue($ordDetail, "course_id");
        $msg       = sprintf(__("New order has been placed for the <strong>%s</strong> course.", "eb-textdomain"), get_the_title($courseId));
        $msg       = apply_filters("eb_order_history_save_status_new_order_msg", $msg);
        $note      = array(
            "type" => "new_order",
            "msg"  => $msg,
        );
        $this->saveOrderStatusHistory($orderId, $note);
    }

    /**
     * Function provides the functionality to create the notes formated array
     *
     * @since 1.3.0
     * @param number $orderId current eb_order post id.
     * @param array $data order update meta.
     * @return array returns an array of the new status note
     */
    private function getStatusUpdateNote($orderId, $data)
    {
        $ordDetail = get_post_meta($orderId, 'eb_order_options', true);
        $orderData = getArrValue($data, 'eb_order_options', false);
        if ($orderData == false) {
            return;
        }
        $oldStatus = getArrValue($ordDetail, "order_status", false);
        $newStatus = getArrValue($orderData, "order_status", false);
        $msg       = array(
            "old_status" => $oldStatus,
            "new_status" => $newStatus,
        );
        $msg       = apply_filters("eb_order_history_save_status_change_msg", $msg);
        $note      = array(
            "type" => "status_update",
            "msg"  => $msg,
        );
        return $note;
    }

    /**
     * Provides the functionality to prepate the refund note data in the format of
     * array(
     * "status"=>"",
     * "refund_amt"=>"",
     * "refund_note"=>"",
     * "refund_unenroll_users"=>"",
     * "currancy"=>"",
     * )
     * @since 1.3.0
     * @param number $orderId current eb_order post id.
     * @param array $data order update meta.
     * @return array returns an array of the refund status data
     */
    private function getOrderRefundStatusMsg($orderId, $data)
    {
        $refundAmt = getArrValue($data, 'refund_amt');
        $msg       = array(
            "refund_amt"            => $refundAmt,
            "refund_note"           => getArrValue($data, 'refund_note'),
            "refund_unenroll_users" => getArrValue($data, 'refund_unenroll_users'),
            "currency"              => getCurrentPayPalcurrencySymb(),
        );
        $msg       = apply_filters("eb_order_history_save_refund_status_msg", $msg);
        $note      = array(
            "type" => "refund",
            "msg"  => $msg
        );
        $this->saveOrderRefundAmt($orderId, $refundAmt);
        return $note;
    }

    private function saveOrderRefundAmt($orderId, $refundAmt)
    {
        $curUser = wp_get_current_user();
        $curUser->user_login;
        $refunds = get_post_meta($orderId, "eb_order_refund_hist", true);
        $refund  = array(
            "amt"      => $refundAmt,
            "by"       => $curUser->user_login,
            "time"     => current_time("timestamp"),
            "currency" => getCurrentPayPalcurrencySymb(),
        );
        if (is_array($refunds)) {
            $refunds[] = $refund;
        } else {
            $refunds = array($refund);
        }
        update_post_meta($orderId, "eb_order_refund_hist", $refunds);
    }

    /**
     * Function provides the functionality to edit the history data and add new
     * at first position. and save the value into the database.
     *
     * @since 1.3.0
     * @param type $orderId
     */
    private function saveOrderStatusHistory($orderId, $note)
    {
        $history = get_post_meta($orderId, "eb_order_status_history", true);
        $curUser = wp_get_current_user();
        if (!is_array($history)) {
            $history = array();
        }
        $newHist = array(
            "by"   => $curUser->user_login,
            "time" => current_time("timestamp"),
            "note" => $note,
        );
        array_unshift($history, $newHist);
        do_action("eb_before_order_refund_meta_save");
        update_post_meta($orderId, "eb_order_status_history", $history);
        do_action("eb_after_order_refund_meta_save");
    }
}
