<?php
/**
 * Class WC_Email_Awaiting_Delivery_Order file.
 *
 * @package WooCommerce\Emails
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WC_Email_Awaiting_Delivery_Order', false)):

    /**
     * Awaiting Delivery Order Email.
     *
     * 通知購買人商品已經出貨的信
     *
     * @class       WC_Email_Awaiting_Delivery_Order
     * @version     3.5.0
     * @package     WooCommerce\Classes\Emails
     * @extends     WC_Email
     */
    class WC_Email_Awaiting_Delivery_Order extends WC_Email {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id             = 'awaiting_delivery_order';
            $this->customer_email = true;

            $this->title          = '已出貨訂單';
            $this->description    = '通知購買人商品已經出貨的信';
            $this->template_html  = 'emails/customer-awaiting-delivery-order.php';
            $this->template_plain = 'emails/plain/customer-awaiting-delivery-order.php';
            $this->placeholders   = array(
                '{order_date}'   => '',
                '{order_number}' => '',
            );

            // Triggers for this email.
            add_action('woocommerce_order_status_processing_to_awaiting-delivery_notification', array($this, 'trigger'), 10, 2);
            add_action('woocommerce_order_status_cancelled_to_awaiting-delivery_notification', array($this, 'trigger'), 10, 2);
            add_action('woocommerce_order_status_failed_to_awaiting-delivery_notification', array($this, 'trigger'), 10, 2);
            add_action('woocommerce_order_status_on-hold_to_awaiting-delivery_notification', array($this, 'trigger'), 10, 2);
            add_action('woocommerce_order_status_pending_to_awaiting-delivery_notification', array($this, 'trigger'), 10, 2);

            // Call parent constructor.
            parent::__construct();
        }

        /**
         * Get email subject.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_subject() {
            return '商品已出貨，感謝您的惠顧';
        }

        /**
         * Get email heading.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_heading() {
            return '商品已出貨，感謝您的惠顧';
        }

        /**
         * Trigger the sending of this email.
         *
         * @param int            $order_id The order ID.
         * @param WC_Order|false $order Order object.
         */
        public function trigger($order_id, $order = false) {
            $this->setup_locale();

            if ($order_id && !is_a($order, 'WC_Order')) {
                $order = wc_get_order($order_id);
            }

            if (is_a($order, 'WC_Order')) {
                $this->object                         = $order;
                $this->recipient                      = $this->object->get_billing_email();
                $this->placeholders['{order_date}']   = wc_format_datetime($this->object->get_date_created());
                $this->placeholders['{order_number}'] = $this->object->get_order_number();
            }

            if ($this->is_enabled() && $this->get_recipient()) {
                $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
            }

            $this->restore_locale();
        }

        /**
         * Get content html.
         *
         * @return string
         */
        public function get_content_html() {
            return wc_get_template_html(
                $this->template_html,
                array(
                    'order'              => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin'      => false,
                    'plain_text'         => false,
                    'email'              => $this,
                )
            );
        }

        /**
         * Get content plain.
         *
         * @return string
         */
        public function get_content_plain() {
            return wc_get_template_html(
                $this->template_plain,
                array(
                    'order'              => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin'      => false,
                    'plain_text'         => true,
                    'email'              => $this,
                )
            );
        }

        /**
         * Default content to show below main email content.
         *
         * @since 3.7.0
         * @return string
         */
        public function get_default_additional_content() {
            return __('Thanks for using {site_url}!', 'woocommerce');
        }
    }

endif;

return new WC_Email_Awaiting_Delivery_Order();
