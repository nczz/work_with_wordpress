<?php
function mxp_woocommerce_version_check($version = '3.3') {
    if (class_exists('WooCommerce')) {
        global $woocommerce;
        if (version_compare($woocommerce->version, $version, ">=")) {
            return true;
        }
    }
    return false;
}

function mxp_remove_default_useless_fields($fields) {
    //最上層總管欄位的方法
    //unset($fields['country']); 3.3.5 後不能遮蓋國家，會影響判斷運費
    unset($fields['last_name']);
    unset($fields['address_2']);
    //unset($fields['city']);//用來儲存下拉選單資料
    //unset($fields['state']);//用來儲存下拉選單資料
    return $fields;
}
add_filter('woocommerce_default_address_fields', 'mxp_remove_default_useless_fields', 999, 1);

function mxp_custom_override_my_account_billing_fields($fields) {
    //補上欄位定義就好，不需指定權重
    $fields['billing_company'] = array(
        'type'        => 'text',
        'label'       => '公司名稱',
        'placeholder' => '公司名稱',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'label_class' => array(),
        'clear'       => true,
        'options'     => array(),
    );
    $fields['billing_company_tax_id'] = array(
        'type'        => 'text',
        'label'       => '公司統編',
        'placeholder' => '公司統編',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'label_class' => array(),
        'clear'       => true,
        'options'     => array(),
    );
    //指定排序權重
    $fields['billing_first_name']['priority'] = 1;
    $fields['billing_state']['priority']      = 2;
    $fields['billing_city']['priority']       = 3;
    $fields['billing_postcode']['priority']   = 4;
    $fields['billing_address_1']['priority']  = 5;

    $billing_display_fields_order = array(
        "billing_first_name"     => "",
        "billing_state"          => "",
        "billing_city"           => "",
        "billing_postcode"       => "",
        "billing_address_1"      => "",
        "billing_email"          => "",
        "billing_phone"          => "",
        "billing_company"        => "",
        "billing_company_tax_id" => "",
    );
    foreach ($billing_display_fields_order as $field_key => $field_value) {
        if ($field_key != "") {
            $billing_display_fields_order[$field_key] = $fields[$field_key];
        }
    }

    return $billing_display_fields_order;
}
add_filter('woocommerce_billing_fields', 'mxp_custom_override_my_account_billing_fields', 999, 1);

function mxp_custom_override_my_account_shipping_fields($fields) {
    //補上欄位定義就好，不需指定權重
    $fields['shipping_phone'] = array(
        'label'       => '收件人電話',
        'placeholder' => '收件人手機格式：0912345678',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
    );
    $fields['shipping_email'] = array(
        'label'       => '收件人信箱',
        'placeholder' => '請輸入收件人信箱',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
    );
    $fields['shipping_company'] = array(
        'label'       => '公司名稱',
        'placeholder' => '公司名稱',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
    );
    $fields['shipping_company_tax_id'] = array(
        'label'       => '公司統編',
        'placeholder' => '公司統編',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
    );
    //指定排序
    $fields['shipping_first_name']['priority'] = 1;
    $fields['shipping_state']['priority']      = 2;
    $fields['shipping_city']['priority']       = 3;
    $fields['shipping_postcode']['priority']   = 4;
    $fields['shipping_address_1']['priority']  = 5;

    $shipping_display_fields_order = array(
        "shipping_first_name"     => "",
        "shipping_state"          => "",
        "shipping_city"           => "",
        "shipping_postcode"       => "",
        "shipping_address_1"      => "",
        "shipping_email"          => "",
        "shipping_phone"          => "",
        "shipping_company"        => "",
        "shipping_company_tax_id" => "",
    );
    foreach ($shipping_display_fields_order as $field_key => $field_value) {
        if ($field_key != "") {
            $shipping_display_fields_order[$field_key] = $fields[$field_key];
        }
    }
    return $shipping_display_fields_order;
}
add_filter('woocommerce_shipping_fields', 'mxp_custom_override_my_account_shipping_fields', 999, 1);

function mxp_custom_override_checkout_fields($fields) {
    $fields['billing']['billing_first_name'] = array(
        'label'       => '姓名',
        'placeholder' => '姓名',
        'required'    => true,
        'class'       => array('form-row-first'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 1,
    );
    $fields['billing']['billing_state'] = array(
        'label'       => '縣/市',
        'placeholder' => '縣/市',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 2,
    );
    $fields['billing']['billing_city'] = array(
        'label'       => '鄉鎮市區',
        'placeholder' => '鄉鎮市區',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 3,
    );
    $fields['billing']['billing_address_1'] = array(
        'label'       => '地址',
        'placeholder' => '地址',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 4,
    );
    $fields['billing']['billing_postcode'] = array(
        'label'       => '郵遞區號',
        'placeholder' => '郵遞區號',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 5,
    );
    $fields['billing']['billing_email'] = array(
        'label'       => '結帳信箱',
        'placeholder' => '請輸入信箱',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 6,
    );
    $fields['billing']['billing_phone'] = array(
        'label'       => '電話',
        'placeholder' => '手機格式：0912345678',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 7,
    );
    $fields['billing']['billing_tax_checkbox'] = array(
        'label'       => '是否需要統編(三聯發票)？',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'type'        => 'checkbox',
        'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
        'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
        'priority'    => 8,
    );
    $fields['billing']['billing_company'] = array(
        'label'       => '公司名稱(抬頭)',
        'placeholder' => '公司名稱(抬頭)',
        'required'    => false,
        'class'       => array('form-row-wide', 'hidden'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 9,
    );
    $fields['billing']['billing_company_tax_id'] = array(
        'label'       => '公司統編',
        'placeholder' => '公司統編',
        'required'    => false,
        'class'       => array('form-row-wide', 'hidden'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 10,
    );

    $sm = wc_get_chosen_shipping_method_ids();
    if (!empty($sm)) {
        if ($sm[0] == 'ecpay_shipping') {
            $cvsInfo                             = WC()->session->get('ecpay_cvs_info');
            $fields['billing']['purchaserStore'] = array(
                'label'    => '超商取貨門市名稱',
                'default'  => isset($cvsInfo['CVSStoreName']) ? sanitize_text_field($cvsInfo['CVSStoreName']) : '',
                'required' => true,
                'class'    => array('hidden'),
            );
            $fields['billing']['purchaserAddress'] = array(
                'label'    => '超商取貨門市地址',
                'default'  => isset($cvsInfo['CVSAddress']) ? sanitize_text_field($cvsInfo['CVSAddress']) : '',
                'required' => true,
                'class'    => array('hidden'),
            );
            $fields['billing']['purchaserPhone'] = array(
                'label'   => '超商取貨門市電話',
                'default' => isset($cvsInfo['CVSTelephone']) ? sanitize_text_field($cvsInfo['CVSTelephone']) : '',
                'class'   => array('hidden'),
            );
            $fields['billing']['CVSStoreID'] = array(
                'label'    => '超商取貨門市代號',
                'default'  => isset($cvsInfo['CVSStoreID']) ? sanitize_text_field($cvsInfo['CVSStoreID']) : '',
                'required' => true,
                'class'    => array('hidden'),
            );
        }
    }
    $fields['shipping']['shipping_first_name'] = array(
        'label'       => '姓名',
        'placeholder' => '姓名',
        'required'    => true,
        'class'       => array('form-row-first'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 1,
    );
    $fields['shipping']['shipping_state'] = array(
        'label'       => '縣/市',
        'placeholder' => '縣/市',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 2,
    );
    $fields['shipping']['shipping_city'] = array(
        'label'       => '鄉鎮市區',
        'placeholder' => '鄉鎮市區',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 3,
    );
    $fields['shipping']['shipping_address_1'] = array(
        'label'       => '地址',
        'placeholder' => '地址',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 4,
    );
    $fields['shipping']['shipping_email'] = array(
        'label'       => '收件人信箱',
        'placeholder' => '請輸入收件人信箱',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 5,
    );
    $fields['shipping']['shipping_phone'] = array(
        'label'       => '收件人電話',
        'placeholder' => '收件人手機格式：0912345678',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 6,
    );
    $fields['shipping']['shipping_postcode'] = array(
        'label'       => '郵遞區號',
        'placeholder' => '郵遞區號',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 7,
    );
    // $fields['shipping']['shipping_company'] = array(
    //     'label'       => '公司名稱',
    //     'placeholder' => '公司名稱',
    //     'required'    => false,
    //     'class'       => array('form-row-wide'),
    //     'clear'       => true,
    //     'type'        => 'text',
    //     'label_class' => array(),
    //     'options'     => array(),
    //     'priority'    => 8,
    // );
    // $fields['shipping']['shipping_company_tax_id'] = array(
    //     'label'       => '公司統編',
    //     'placeholder' => '公司統編',
    //     'required'    => false,
    //     'class'       => array('form-row-wide'),
    //     'clear'       => true,
    //     'type'        => 'text',
    //     'label_class' => array(),
    //     'options'     => array(),
    //     'priority'    => 9,
    // );

    //reorder fields
    $billing_fields               = array();
    $shipping_fields              = array();
    $billing_display_fields_order = array(
        "billing_first_name",
        "billing_state",
        "billing_city",
        "billing_address_1",
        "billing_postcode",
        "billing_email",
        "billing_phone",
        "billing_tax_checkbox",
        "billing_company",
        "billing_company_tax_id",
    );
    if (!empty($sm)) {
        if ($sm[0] == 'ecpay_shipping') {
            $billing_display_fields_order[] = "purchaserStore";
            $billing_display_fields_order[] = "purchaserAddress";
            $billing_display_fields_order[] = "purchaserPhone";
            $billing_display_fields_order[] = "CVSStoreID";
        }
    }
    foreach ($billing_display_fields_order as $field) {
        $billing_fields[$field] = $fields["billing"][$field];
    }
    $shipping_display_fields_order = array(
        "shipping_first_name",
        "shipping_state",
        "shipping_city",
        "shipping_address_1",
        "shipping_postcode",
        "shipping_email",
        "shipping_phone",
        // "shipping_company",
        // "shipping_company_tax_id",
    );
    foreach ($shipping_display_fields_order as $field) {
        $shipping_fields[$field] = $fields["shipping"][$field];
    }
    $fields["billing"]  = $billing_fields;
    $fields["shipping"] = $shipping_fields;
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'mxp_custom_override_checkout_fields', 999, 1);

function mxp_custom_checkout_field_display_admin_order_billing_meta($order) {
    $display_meta = '<p><strong>開立發票:</strong> ' . (get_post_meta($order->get_id(), 'billing_tax_checkbox', true) == 1 ? '是' : '否') . '</p>';
    $display_meta .= '<p><strong>公司名稱:</strong> ' . get_post_meta($order->get_id(), '_billing_company', true) . '</p>';
    $display_meta .= '<p><strong>公司統編:</strong> ' . get_post_meta($order->get_id(), '_billing_company_tax_id', true) . '</p>';
    echo $display_meta;
}
add_action('woocommerce_admin_order_data_after_billing_address', 'mxp_custom_checkout_field_display_admin_order_billing_meta', 10, 1);

function mxp_woocommerce_save_account_details_required_fields($fields) {
    return array(
        'account_first_name'   => __('First name', 'woocommerce'),
        'account_display_name' => __('Display name', 'woocommerce'),
        'account_email'        => __('Email address', 'woocommerce'),
    );
}
add_filter('woocommerce_save_account_details_required_fields', 'mxp_woocommerce_save_account_details_required_fields', 11, 1);

function mxp_custom_checkout_field_display_admin_order_shipping_meta($order) {
    $display_meta = '';
    // $display_meta .= '<p><strong>公司名稱:</strong> ' . get_post_meta($order->get_id(), '_shipping_company', true) . '</p>';
    // $display_meta .= '<p><strong>公司統編:</strong> ' . get_post_meta($order->get_id(), '_shipping_company_tax_id', true) . '</p>';
    $display_meta .= '<p><strong>收貨人手機:</strong> ' . get_post_meta($order->get_id(), '_shipping_phone', true) . '</p>';
    $display_meta .= '<p><strong>收貨人信箱:</strong> ' . get_post_meta($order->get_id(), '_shipping_email', true) . '</p>';
    echo $display_meta;
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'mxp_custom_checkout_field_display_admin_order_shipping_meta', 10, 1);

function mxp_checkout_page_footer() {
    if (is_checkout() || is_account_page()):
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            jQuery('#billing_tax_checkbox').change(function() {
                if(this.checked) {
                    jQuery('#billing_company_field>label>span').text('(必填)');
                    jQuery('#billing_company_tax_id_field>label>span').text('(必填)');
                    jQuery('#billing_company_field').show();
                    jQuery('#billing_company_tax_id_field').show();
                } else {
                    jQuery('#billing_company_field').hide();
                    jQuery('#billing_company_tax_id_field').hide();
                }
            });
            jQuery(document).ready(function() {
            //感謝 essoduke 大的郵遞區號專案 https://github.com/essoduke/jQuery-TWzipcode
            //路徑視使用需求而改，預設是抓取目前使用的主題 /js/ 目錄下的 jquery.twzipcode.min.js 檔案
            //可以從 https://raw.githubusercontent.com/essoduke/jQuery-TWzipcode/master/jquery.twzipcode.min.js 這裡抓下來放進去
            jQuery.getScript("<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.twzipcode.min.js", function() {
                jQuery(function($) {
                    $('<div>')
                        .attr({ id: 'billing-zipcode-fields' })
                        .insertBefore('#billing_address_1_field');
                    $('<div>')
                        .attr({ id: 'shipping-zipcode-fields' })
                        .insertBefore('#shipping_address_1_field');
                    var billingAddress = $('input[name="billing_address_1"]').val(),
                        shippingAddress = $('input[name="shipping_address_1"]').val();

                    function selectChangeCallback() {
                        if ($(this).attr('name').match(/^shipping_/)) {
                            $('#shipping-zipcode-fields').twzipcode('get', function(country, district, zipcode) {
                                /*$('#shipping_address_1').val(country + district);*/
                                $('input[name="shipping_postcode"]').val(zipcode);
                            });
                        } else {
                            $('#billing-zipcode-fields').twzipcode('get', function(country, district, zipcode) {
                                /*$('#billing_address_1').val(country + district);*/
                                $('input[name="billing_postcode"]').val(zipcode);
                            });
                        }
                        jQuery(document.body).trigger("update_checkout");
                    }
                    var $billingZipcodeFields = $('#billing-zipcode-fields'),
                        $shippingZipcodeFields = $('#shipping-zipcode-fields'),
                        $billingStateField = $('#billing_state_field'),
                        $shippingStateField = $('#shipping_state_field'),
                        $billingCityField = $('#billing_city_field'),
                        $shippingCityField = $('#shipping_city_field'),
                        $billingPostcodeField = $('#billing_postcode_field'),
                        $shippingPostcodeField = $('#shipping_postcode_field');

                    var billingState = $('input[name="billing_state"]').val(),
                        billingCity = $('input[name="billing_city"]').val(),
                        shippingState = $('input[name="shipping_state"]').val(),
                        shippingCity = $('input[name="shipping_city"]').val(),
                        billingPostcode = $('input[name="billing_postcode"]').val(),
                        shippingPostcode = $('input[name="shipping_postcode"]').val();

                    $billingZipcodeFields.twzipcode({
                        countyName: 'billing_state',
                        districtName: 'billing_city',
                        zipcodeName: 'billing_postcode',
                        readonly: true,
                        detect: false,
                        onCountySelect: selectChangeCallback,
                        onDistrictSelect: selectChangeCallback
                    });

                    $shippingZipcodeFields.twzipcode({
                        countyName: 'shipping_state',
                        districtName: 'shipping_city',
                        zipcodeName: 'shipping_postcode',
                        readonly: true,
                        detect: false,
                        onCountySelect: selectChangeCallback,
                        onDistrictSelect: selectChangeCallback
                    });

                    $billingStateField.find('input[name="billing_state"]').remove();
                    $shippingStateField.find('input[name="shipping_state"]').remove();
                    $billingCityField.find('input[name="billing_city"]').remove();
                    $shippingCityField.find('input[name="shipping_city"]').remove();
                    $billingPostcodeField.find('input[name="billing_postcode"]').remove();
                    $shippingPostcodeField.find('input[name="shipping_postcode"]').remove();

                    $billingStateField.append($billingZipcodeFields.find('select[name="billing_state"]'));
                    $shippingStateField.append($shippingZipcodeFields.find('select[name="shipping_state"]'));
                    $billingCityField.append($billingZipcodeFields.find('select[name="billing_city"]'));
                    $shippingCityField.append($shippingZipcodeFields.find('select[name="shipping_city"]'));
                    $billingPostcodeField.append($billingZipcodeFields.find('input[name="billing_postcode"]').addClass('input-text').attr('id', 'billing_postcode'));
                    $shippingPostcodeField.append($shippingZipcodeFields.find('input[name="shipping_postcode"]').addClass('input-text').attr('id', 'shipping_postcode'));


                    $billingZipcodeFields.twzipcode('set', {
                        'county': billingState,
                        'district': billingCity,
                        'zipcode': billingPostcode
                    });

                    $shippingZipcodeFields.twzipcode('set', {
                        'county': shippingState,
                        'district': shippingCity,
                        'zipcode': shippingPostcode
                    });

                    /*$('input[name="billing_address_1"]').val(billingAddress);
                    $('input[name="shipping_address_1"]').val(shippingAddress);*/
                });
            });
        });
    });
    </script>
<?php
endif;
}
add_action('wp_footer', 'mxp_checkout_page_footer');

//加入免運權重，避免多運費選項出現
function hide_shipping_when_free_is_available($rates) {
    $free = array();
    foreach ($rates as $rate_id => $rate) {
        if ('free_shipping' === $rate->method_id) {
            $free           = array();
            $free[$rate_id] = $rate;
            break;
        }
        if ('flat_rate' === $rate->method_id) {
            if ($rate->cost == 0) {
                $free           = array();
                $rate->label    = '免運費';
                $free[$rate_id] = $rate;
                break;
            }
        }
    }
    return !empty($free) ? $free : $rates;
}
add_filter('woocommerce_package_rates', 'hide_shipping_when_free_is_available', 100, 1);

//購物車自動更新數量
function mxp_auto_cart_update_qty_script() {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            jQuery('div.woocommerce').on('change', '.qty', function(){
               jQuery("[name='update_cart']").removeAttr('disabled');
               jQuery("[name='update_cart']").trigger("click");
            });
        });
   </script>
<?php
}
add_action('woocommerce_after_cart', 'mxp_auto_cart_update_qty_script');

//更改運送方式1
function filter_woocommerce_shipping_package_name($sprintf, $i, $package) {
    // make filter magic happen here...
    return '運送方式';
};
add_filter('woocommerce_shipping_package_name', 'filter_woocommerce_shipping_package_name', 10, 3);

// 啟用訂單備註功能（舊）
// function custom_shop_order_column($columns) {
//     $ordered_columns = array();
//     foreach ($columns as $key => $column) {
//         $ordered_columns[$key] = $column;
//         if ('order_date' == $key) {
//             $ordered_columns['order_notes'] = '備註';
//         }
//     }
//     return $ordered_columns;
// }

// function custom_shop_order_list_column_content($column) {
//     global $post, $the_order;
//     $customer_note = $post->post_excerpt;

//     if ($column == 'order_notes') {
//         if ($the_order->get_customer_note()) {
//             echo '<span class="note-on customer tips" data-tip="' . wc_sanitize_tooltip($the_order->get_customer_note()) . '">' . __('Yes', 'woocommerce') . '</span>';
//         }

//         if ($post->comment_count) {

//             $latest_notes = wc_get_order_notes(array(
//                 'order_id' => $post->ID,
//                 'limit'    => 1,
//                 'orderby'  => 'date_created_gmt',
//             ));

//             $latest_note = current($latest_notes);

//             if (isset($latest_note->content) && 1 == $post->comment_count) {
//                 echo '<span class="note-on tips" data-tip="' . wc_sanitize_tooltip($latest_note->content) . '">' . __('Yes', 'woocommerce') . '</span>';
//             } elseif (isset($latest_note->content)) {
//                 // translators: %d: notes count
//                 echo '<span class="note-on tips" data-tip="' . wc_sanitize_tooltip($latest_note->content . '<br/><small style="display:block">' . sprintf(_n('Plus %d other note', 'Plus %d other notes', ($post->comment_count - 1), 'woocommerce'), $post->comment_count - 1) . '</small>') . '">' . __('Yes', 'woocommerce') . '</span>';
//             } else {
//                 // translators: %d: notes count
//                 echo '<span class="note-on tips" data-tip="' . wc_sanitize_tooltip(sprintf(_n('%d note', '%d notes', $post->comment_count, 'woocommerce'), $post->comment_count)) . '">' . __('Yes', 'woocommerce') . '</span>';
//             }
//         }
//     }
// }

// // 設定樣式
// function add_custom_order_status_actions_button_css() {
//     echo '<style>
//     td.order_notes > .note-on { display: inline-block !important;}
//     span.note-on.customer { margin-right: 4px !important;}
//     span.note-on.customer::after { font-family: woocommerce !important; content: "\e026" !important;}
//     </style>';
// }
// //判斷 WC 版本是否大於 v3.3 版才啟用附註功能
// if (mxp_woocommerce_version_check('3.3')) {
//     //Ref: https://stackoverflow.com/a/49047149
//     add_filter('manage_edit-shop_order_columns', 'custom_shop_order_column', 90);
//     add_action('manage_shop_order_posts_custom_column', 'custom_shop_order_list_column_content', 10, 1);
//     add_action('admin_head', 'add_custom_order_status_actions_button_css');
// }

// 訂單備註直接呈現版本
function add_order_notes_column($columns) {
    $new_columns                   = (is_array($columns)) ? $columns : array();
    $new_columns['order_notes']    = '訂單備註';
    $new_columns['order_products'] = "購買商品";
    return $new_columns;
}
add_filter('manage_edit-shop_order_columns', 'add_order_notes_column', 90);

function add_order_notes_column_style() {
    $css = '.post-type-shop_order table.widefat.fixed { table-layout: auto; width: 100%; }';
    $css .= 'table.wp-list-table .column-order_notes { min-width: 280px; text-align: left; }';
    $css .= '.column-order_notes ul { margin: 0 0 0 18px; list-style-type: disc; }';
    $css .= '.order_customer_note { color: #ee0000; }'; // red
    $css .= '.order_private_note { color: #0000ee; }'; // blue
    wp_add_inline_style('woocommerce_admin_styles', $css);
}
add_action('admin_print_styles', 'add_order_notes_column_style');

function add_order_notes_content($column) {
    if ($column != 'order_notes') {
        return;
    }

    global $post, $the_order;
    if (empty($the_order) || $the_order->get_id() != $post->ID) {
        $the_order = wc_get_order($post->ID);
    }
    $args             = array();
    $args['order_id'] = $the_order->get_id();
    $args['order_by'] = 'date_created';
    $args['order']    = 'ASC';
    $notes            = wc_get_order_notes($args);
    if ($notes) {
        print '<ul>';
        foreach ($notes as $note) {
            if ($note->customer_note) {
                print '<li class="order_customer_note">';
            } else {
                print '<li class="order_private_note">';
            }
            $date = date('Y-m-d H:i:s', strtotime($note->date_created));
            print $date . ' by ' . $note->added_by . '<br>' . $note->content . '</li>';
        }
        print '</ul>';
    }
}
add_action('manage_shop_order_posts_custom_column', 'add_order_notes_content', 10, 1);

function mxp_order_items_column_cnt($colname) {
    global $the_order;
    if ($colname == 'order_products') {
        $order_items = $the_order->get_items();
        if (!is_wp_error($order_items)) {
            foreach ($order_items as $order_item) {
                $ticket_meta = get_post_meta($order_item['product_id'], '_tribe_wooticket_for_event', true);
                if ($ticket_meta != '') {
                    echo $order_item['quantity'] . '&nbsp;&times;&nbsp;<a href="' . admin_url('post.php?post=' . $order_item['product_id'] . '&action=edit') . '">' . get_the_title($ticket_meta) . '</a><br />';
                } else {
                    echo $order_item['quantity'] . '&nbsp;&times;&nbsp;<a href="' . admin_url('post.php?post=' . $order_item['product_id'] . '&action=edit') . '">' . $order_item['name'] . '</a><br />';
                }
            }
        }
    }
}
add_action('manage_shop_order_posts_custom_column', 'mxp_order_items_column_cnt');

//移除在購物車計算運費的方法
function disable_shipping_calc_on_cart($show_shipping) {
    if (is_cart()) {
        return false;
    }
    return $show_shipping;
}
add_filter('woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99);

//解決整合綠界物流會有結帳資料被清空的問題 Ref: https://www.mxp.tw/8582/ 還要補上自己觸發儲存的前端事件
function mxp_wc_save_session_data($value) {
    $data = $_POST['post_data'];
    parse_str(html_entity_decode($data), $pdata);
    if (isset($pdata['billing_first_name']) && $pdata['billing_first_name'] != "") {
        WC()->session->set('billing_first_name', $pdata['billing_first_name']);
    }
    if (isset($pdata['billing_phone']) && $pdata['billing_phone'] != "") {
        WC()->session->set('billing_phone', $pdata['billing_phone']);
    }
    if (isset($pdata['billing_company']) && $pdata['billing_company'] != "") {
        WC()->session->set('billing_company', $pdata['billing_company']);
    }
    if (isset($pdata['billing_company_tax_id']) && $pdata['billing_company_tax_id'] != "") {
        WC()->session->set('billing_company_tax_id', $pdata['billing_company_tax_id']);
    }
    if (isset($pdata['billing_email']) && $pdata['billing_email'] != "") {
        WC()->session->set('billing_email', $pdata['billing_email']);
    }
    if (isset($pdata['billing_postcode']) && $pdata['billing_postcode'] != "") {
        WC()->session->set('billing_postcode', $pdata['billing_postcode']);
    }
    $value['#billing_first_name']     = '<input type="text" class="input-text " name="billing_first_name" id="billing_first_name" placeholder="請填入最多中文5個字或英文10個字" value="' . WC()->session->get('billing_first_name') . '" autocomplete="given-name" autofocus="autofocus">';
    $value['#billing_phone']          = '<input type="tel" class="input-text " name="billing_phone" id="billing_phone" placeholder="手機號碼格式為:0912345678" value="' . WC()->session->get('billing_phone') . '" autocomplete="tel">';
    $value['#billing_company']        = '<input type="text" class="input-text " name="billing_company" id="billing_company" placeholder="" value="' . WC()->session->get('billing_company') . '" autocomplete="organization">';
    $value['#billing_email']          = '<input type="email" class="input-text " name="billing_email" id="billing_email" placeholder="" value="' . WC()->session->get('billing_email') . '" autocomplete="email">';
    $value['#billing_company_tax_id'] = '<input type="text" class="input-text " name="billing_company_tax_id" id="billing_company_tax_id" placeholder="公司統編" value="' . WC()->session->get('billing_company_tax_id') . '">';
    $value['#billing_postcode']       = '<input type="text" id="billing_postcode" name="billing_postcode" placeholder="郵遞區號" readonly="" value="' . WC()->session->get('billing_postcode') . '" class="input-text">';
    return $value;
}
add_filter('woocommerce_update_order_review_fragments', 'mxp_wc_save_session_data', 10, 1);

// 主題繼承覆蓋翻譯（如有放置語言檔案才啟用覆蓋功能）
function mxp_load_custom_wc_translation_file($mofile, $domain) {
    if ('woocommerce' === $domain) {
        $mofile = get_stylesheet_directory() . '/languages/woocommerce/' . get_locale() . '.mo';
        if (file_exists($mofile)) {
            return $mofile;
        }
    }
    return $mofile;
}
add_filter('load_textdomain_mofile', 'mxp_load_custom_wc_translation_file', 11, 2);

// 檢查結帳表單送出資料
function mxp_check_checkout_post_data() {
    if (!empty($_POST['billing_tax_checkbox']) && (empty($_POST['billing_company']) || empty($_POST['billing_company_tax_id']))) {
        wc_add_notice('請輸入三聯發票抬頭與統一編號', 'error');
    }
    //整合綠界物流外掛的驗證補強
    $sm = wc_get_chosen_shipping_method_ids();
    if (!empty($sm)) {
        if ($sm[0] == 'ecpay_shipping') {
            if ($_POST['billing_first_name']) {
                $s = $_POST['billing_first_name'];
                if (preg_match("/\p{Han}+/u", $s)) {
                    if (mb_strlen($s) > 5) {
                        wc_add_notice(__('帳單姓名請輸入最多 5 個中文字'), 'error');
                    }
                } else {
                    if (mb_strlen($s) > 10) {
                        wc_add_notice(__('帳單姓名請輸入最多 10 個英文字'), 'error');
                    }
                }
            }
            if ($_POST['shipping_first_name']) {
                $s = $_POST['shipping_first_name'];
                if (preg_match("/\p{Han}+/u", $s)) {
                    if (mb_strlen($s) > 5) {
                        wc_add_notice(__('收件人姓名請輸入最多 5 個中文字'), 'error');
                    }
                } else {
                    if (mb_strlen($s) > 10) {
                        wc_add_notice(__('收件人姓名請輸入最多 10 個英文字'), 'error');
                    }
                }
            }
            if ($_POST['billing_phone']) {
                $s = $_POST['billing_phone'];
                if (!preg_match('/^09[0-9]{8}$/', $s)) {
                    wc_add_notice(__('帳單手機格式錯誤，格式為 0912345678'), 'error');
                }
            }
            if ($_POST['shipping_phone']) {
                $s = $_POST['shipping_phone'];
                if (!preg_match('/^09[0-9]{8}$/', $s)) {
                    wc_add_notice(__('收件人手機格式錯誤，格式為 0912345678'), 'error');
                }
            }
        }
    }
}
add_action('woocommerce_checkout_process', 'mxp_check_checkout_post_data');

// 密碼強度改不建議
function mxp_min_password_strength($strength) {
    return 0;
}
add_filter('woocommerce_min_password_strength', 'mxp_min_password_strength', 99, 1);

// 後台支援搜尋包含 SKU 貨號的訂單
function mxp_order_search_by_sku($order_ids, $term, $search_fields) {
    global $wpdb;
    if (!empty($term)) {
        // 確認有沒有這個商品，沒有就跳開了
        $product_id = wc_get_product_id_by_sku($wpdb->esc_like(wc_clean($term)));
        if (!$product_id) {
            return $order_ids;
        }
        // 找找看關聯資料，輸出不重複的訂單編號
        $order_ids = array_unique(
            $wpdb->get_col(
                $wpdb->prepare("SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key IN ( '_product_id', '_variation_id' ) AND meta_value = %d ) AND order_item_type = 'line_item'", $product_id)
            )
        );
    }
    return $order_ids;
}
add_filter('woocommerce_shop_order_search_results', 'mxp_order_search_by_sku', 9999, 3);

if (!function_exists('mxp_getOrderDetailById')) {

    //to get full order details
    function mxp_getOrderDetailById($id, $fields = null, $filter = array()) {

        if (is_wp_error($id)) {
            return false;
        }
        // Get the decimal precession
        $dp    = (isset($filter['dp'])) ? intval($filter['dp']) : 2;
        $order = wc_get_order($id); //getting order Object
        if ($order === false) {
            return false;
        }
        $order_data = array(
            'id'                        => $order->get_id(),
            'order_number'              => $order->get_order_number(),
            'created_at'                => $order->get_date_created()->date('Y-m-d H:i:s'),
            'updated_at'                => $order->get_date_modified()->date('Y-m-d H:i:s'),
            'completed_at'              => !empty($order->get_date_completed()) ? $order->get_date_completed()->date('Y-m-d H:i:s') : '',
            'status'                    => $order->get_status(),
            'currency'                  => $order->get_currency(),
            'total'                     => wc_format_decimal($order->get_total(), $dp),
            'subtotal'                  => wc_format_decimal($order->get_subtotal(), $dp),
            'total_line_items_quantity' => $order->get_item_count(),
            'total_tax'                 => wc_format_decimal($order->get_total_tax(), $dp),
            'total_shipping'            => wc_format_decimal($order->get_total_shipping(), $dp),
            'cart_tax'                  => wc_format_decimal($order->get_cart_tax(), $dp),
            'shipping_tax'              => wc_format_decimal($order->get_shipping_tax(), $dp),
            'total_discount'            => wc_format_decimal($order->get_total_discount(), $dp),
            'shipping_methods'          => $order->get_shipping_method(),
            'order_key'                 => $order->get_order_key(),
            'payment_details'           => array(
                'method_id'    => $order->get_payment_method(),
                'method_title' => $order->get_payment_method_title(),
                'paid_at'      => !empty($order->get_date_paid()) ? $order->get_date_paid()->date('Y-m-d H:i:s') : '',
            ),
            'billing_address'           => array(
                'first_name'       => $order->get_billing_first_name(),
                'last_name'        => $order->get_billing_last_name(),
                'company'          => $order->get_billing_company(),
                'address_1'        => $order->get_billing_address_1(),
                'address_2'        => $order->get_billing_address_2(),
                'city'             => $order->get_billing_city(),
                'state'            => $order->get_billing_state(),
                'formated_state'   => WC()->countries->states[$order->get_billing_country()][$order->get_billing_state()], //human readable formated state name
                'postcode'         => $order->get_billing_postcode(),
                'country'          => $order->get_billing_country(),
                'formated_country' => WC()->countries->countries[$order->get_billing_country()], //human readable formated country name
                'email'            => $order->get_billing_email(),
                'phone'            => $order->get_billing_phone(),
            ),
            'shipping_address'          => array(
                'first_name'       => $order->get_shipping_first_name(),
                'last_name'        => $order->get_shipping_last_name(),
                'company'          => $order->get_shipping_company(),
                'address_1'        => $order->get_shipping_address_1(),
                'address_2'        => $order->get_shipping_address_2(),
                'city'             => $order->get_shipping_city(),
                'state'            => $order->get_shipping_state(),
                'formated_state'   => WC()->countries->states[$order->get_shipping_country()][$order->get_shipping_state()], //human readable formated state name
                'postcode'         => $order->get_shipping_postcode(),
                'country'          => $order->get_shipping_country(),
                'formated_country' => WC()->countries->countries[$order->get_shipping_country()], //human readable formated country name
            ),
            'note'                      => $order->get_customer_note(),
            'customer_ip'               => $order->get_customer_ip_address(),
            'customer_user_agent'       => $order->get_customer_user_agent(),
            'customer_id'               => $order->get_user_id(),
            'view_order_url'            => $order->get_view_order_url(),
            'line_items'                => array(),
            'shipping_lines'            => array(),
            'tax_lines'                 => array(),
            'fee_lines'                 => array(),
            'coupon_lines'              => array(),
        );

        //getting all line items
        foreach ($order->get_items() as $item_id => $item) {

            $product = $item->get_product();

            $product_id  = null;
            $product_sku = null;
            // Check if the product exists.
            if (is_object($product)) {
                $product_id  = $product->get_id();
                $product_sku = $product->get_sku();
            }

            $order_data['line_items'][] = array(
                'id'                    => $item_id,
                'subtotal'              => wc_format_decimal($order->get_line_subtotal($item, false, false), $dp),
                'subtotal_tax'          => wc_format_decimal($item['line_subtotal_tax'], $dp),
                'total'                 => wc_format_decimal($order->get_line_total($item, false, false), $dp),
                'total_tax'             => wc_format_decimal($item['line_tax'], $dp),
                'price'                 => wc_format_decimal($order->get_item_total($item, false, false), $dp),
                'quantity'              => wc_stock_amount($item['qty']),
                'tax_class'             => (!empty($item['tax_class'])) ? $item['tax_class'] : null,
                'name'                  => $item['name'],
                'product_id'            => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type)) ? $product->get_parent_id() : $product_id,
                'variation_id'          => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type)) ? $product_id : 0,
                'product_url'           => get_permalink($product_id),
                'product_thumbnail_url' => wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'thumbnail', TRUE)[0],
                'sku'                   => $product_sku,
                'meta'                  => wc_display_item_meta($item, array('echo' => false)),
            );
        }

        //getting shipping
        foreach ($order->get_shipping_methods() as $shipping_item_id => $shipping_item) {
            $order_data['shipping_lines'][] = array(
                'id'           => $shipping_item_id,
                'method_id'    => $shipping_item['method_id'],
                'method_title' => $shipping_item['name'],
                'total'        => wc_format_decimal($shipping_item['cost'], $dp),
            );
        }

        //getting taxes
        foreach ($order->get_tax_totals() as $tax_code => $tax) {
            $order_data['tax_lines'][] = array(
                'id'       => $tax->id,
                'rate_id'  => $tax->rate_id,
                'code'     => $tax_code,
                'title'    => $tax->label,
                'total'    => wc_format_decimal($tax->amount, $dp),
                'compound' => (bool) $tax->is_compound,
            );
        }

        //getting fees
        foreach ($order->get_fees() as $fee_item_id => $fee_item) {
            $order_data['fee_lines'][] = array(
                'id'        => $fee_item_id,
                'title'     => $fee_item['name'],
                'tax_class' => (!empty($fee_item['tax_class'])) ? $fee_item['tax_class'] : null,
                'total'     => wc_format_decimal($order->get_line_total($fee_item), $dp),
                'total_tax' => wc_format_decimal($order->get_line_tax($fee_item), $dp),
            );
        }

        //getting coupons
        foreach ($order->get_items('coupon') as $coupon_item_id => $coupon_item) {

            $order_data['coupon_lines'][] = array(
                'id'     => $coupon_item_id,
                'code'   => $coupon_item['name'],
                'amount' => wc_format_decimal($coupon_item['discount_amount'], $dp),
            );
        }

        return array('order' => apply_filters('woocommerce_api_order_response', $order_data, $order, $fields));
    }

}

// 訂單列表控制顯示的訂單狀態
function mxp_woocommerce_my_account_my_orders_query($args) {
    $args['post_status'] = array('wc-failed', 'wc-refunded', 'wc-cancelled', 'wc-on-hold', 'wc-processing', 'wc-pending', 'wc-completed');
    // $order_statuses      = array(
    //     'wc-pending'    => _x('Pending payment', 'Order status', 'woocommerce'),
    //     'wc-processing' => _x('Processing', 'Order status', 'woocommerce'),
    //     'wc-on-hold'    => _x('On hold', 'Order status', 'woocommerce'),
    //     'wc-completed'  => _x('Completed', 'Order status', 'woocommerce'),
    //     'wc-cancelled'  => _x('Cancelled', 'Order status', 'woocommerce'),
    //     'wc-refunded'   => _x('Refunded', 'Order status', 'woocommerce'),
    //     'wc-failed'     => _x('Failed', 'Order status', 'woocommerce'),
    // );
    return $args;
}
add_filter('woocommerce_my_account_my_orders_query', 'mxp_woocommerce_my_account_my_orders_query', 11, 1);

// 自動完成只有虛擬商品的訂單狀態
function mxp_check_order_status_completed($order_id, $old_status, $new_status) {
    if ($new_status == 'processing') {
        $check_virtual_product = true;
        $order                 = wc_get_order($order_id);
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product(); // Get the product object
            // Check if the product is virtual
            if ($product && $product->is_virtual()) {
                // The product is virtual
                // $check_virtual_product = true;
            } else {
                // The product is not virtual
                $check_virtual_product = false;
                break;
            }
        }
        if ($order && $check_virtual_product === true) {
            $order->update_status('completed', '（系統）已自動完成訂單。', true);
        }
    }
}
add_action('woocommerce_order_status_changed', 'mxp_check_order_status_completed', 10, 3);

// 禁用 WC 背景縮圖功能
add_filter('woocommerce_background_image_regeneration', '__return_false');

// 顯示使用者帳號的註冊時間，移植此款外掛 https://tw.wordpress.org/plugins/recently-registered/
function mxp_admin_init_for_user_recently_registered() {
    if (is_admin()) {
        add_filter('manage_users_columns', function ($columns) {
            $columns['registerdate'] = '註冊時間';
            return $columns;
        });
        add_action('manage_users_custom_column', function ($value, $column_name, $user_id) {
            global $mode;
            $list_mode = empty($_REQUEST['mode']) ? 'list' : sanitize_text_field($_REQUEST['mode']);

            if ('registerdate' !== $column_name) {
                return $value;
            } else {
                $user = get_userdata($user_id);
                if (is_multisite() && ('list' === $list_mode)) {
                    $formated_date = 'Y/m/d';
                } else {
                    $formated_date = 'Y/m/d g:i:s a';
                }
                $registered = strtotime(get_date_from_gmt($user->user_registered));
                // If the date is negative or in the future, then something's wrong, so we'll be unknown.
                if (($registered <= 0) || (time() <= $registered)) {
                    $registerdate = '<span class="recently-registered invalid-date">未知時間</span>';
                } else {
                    $registerdate = '<span class="recently-registered valid-date">' . date_i18n($formated_date, $registered) . '</span>';
                }
                return $registerdate;
            }
        }, 10, 3);
        add_filter('manage_users_sortable_columns', function ($columns) {
            $custom = array(
                // meta column id => sortby value used in query
                'registerdate' => 'registered',
            );
            return wp_parse_args($custom, $columns);
        });
        add_filter('request', function ($vars) {
            if (isset($vars['orderby']) && 'registerdate' == $vars['orderby']) {
                $new_vars = array(
                    'meta_key' => 'registerdate',
                    'orderby'  => 'meta_value',
                );
                $vars = array_merge($vars, $new_vars);
            }
            return $vars;
        });
    }
}
add_action('admin_init', 'mxp_admin_init_for_user_recently_registered');

// function mxp_woocommerce_ecpay_available_payment_gateways($available_gateways) {
// // 判斷是否選取綠界物流，是的話取消「貨到付款」的選項避免錯誤。（此為超商取貨（無付款）功能處理）
//     $sm = null;
//     if (function_exists('wc_get_chosen_shipping_method_ids')) {
//         $sm = wc_get_chosen_shipping_method_ids();
//     }
//     if (!empty($sm)) {
//         if ($sm[0] == 'ecpay_shipping') {
//             unset($available_gateways['cod']);
//         }
//     }
// //回傳付款方式
//     return $available_gateways;
// };
// add_filter('woocommerce_available_payment_gateways', 'mxp_woocommerce_ecpay_available_payment_gateways', 11, 1);

//取消選擇國家會改變本地化結帳欄位的功能（注意，此功能有機會跟主題衝突，要小心使用）
// function mxp_remove_wc_address_i18n_script() {
//     if (is_checkout() || is_account_page()) {
//         wp_deregister_script('wc-address-i18n');
//     }
// }
// add_action('wp_head', 'mxp_remove_wc_address_i18n_script');

// function mxp_shipping_fee_discount() {
//     if (is_admin() && !defined('DOING_AJAX')) {
//         // 避免在管理介面下被觸發
//         return;
//     }
//     $total_price = 0;
//     $total_price = intval(WC()->cart->get_cart_contents_total());
//     foreach (WC()->session->get('shipping_for_package_0')['rates'] as $method_id => $rate) {
//         // 判斷當前選擇的運送方法與目前購物車總金額是否大於 700
//         if (WC()->session->get('chosen_shipping_methods')[0] == $method_id && $total_price >= 700) {
//             $rate_label         = $rate->label; // 當前運費標籤名稱
//             $rate_cost_excl_tax = floatval($rate->cost); // 不含稅率的運費
//             // 紀錄稅率費用
//             $rate_taxes = 0;
//             foreach ($rate->taxes as $rate_tax) {
//                 $rate_taxes += floatval($rate_tax);
//             }
//             // 包含稅率費用的總運費
//             $rate_cost_incl_tax = $rate_cost_excl_tax + $rate_taxes;
//             if ($rate_cost_incl_tax != 0) {
//                 WC()->cart->add_fee('消費滿 700 免運費', -$rate_cost_incl_tax, false);
//             }
//             break;
//         }
//     }
// }
// add_action('woocommerce_cart_calculate_fees', 'mxp_shipping_fee_discount');
