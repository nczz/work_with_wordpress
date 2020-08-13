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
    $fields['billing']['billing_company'] = array(
        'label'       => '公司名稱',
        'placeholder' => '公司名稱',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 8,
    );
    $fields['billing']['billing_company_tax_id'] = array(
        'label'       => '公司統編',
        'placeholder' => '公司統編',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 9,
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
    $fields['shipping']['shipping_company'] = array(
        'label'       => '公司名稱',
        'placeholder' => '公司名稱',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 8,
    );
    $fields['shipping']['shipping_company_tax_id'] = array(
        'label'       => '公司統編',
        'placeholder' => '公司統編',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'type'        => 'text',
        'label_class' => array(),
        'options'     => array(),
        'priority'    => 9,
    );

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
        "shipping_company",
        "shipping_company_tax_id",
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
    $display_meta = '<p><strong>公司名稱:</strong> ' . get_post_meta($order->get_id(), '_billing_company', true) . '</p>';
    $display_meta .= '<p><strong>公司統編:</strong> ' . get_post_meta($order->get_id(), '_billing_company_tax_id', true) . '</p>';
    echo $display_meta;
}
add_action('woocommerce_admin_order_data_after_billing_address', 'mxp_custom_checkout_field_display_admin_order_billing_meta', 10, 1);

function mxp_custom_checkout_field_display_admin_order_shipping_meta($order) {
    $display_meta = '<p><strong>公司名稱:</strong> ' . get_post_meta($order->get_id(), '_shipping_company', true) . '</p>';
    $display_meta .= '<p><strong>公司統編:</strong> ' . get_post_meta($order->get_id(), '_shipping_company_tax_id', true) . '</p>';
    $display_meta .= '<p><strong>收貨人手機:</strong> ' . get_post_meta($order->get_id(), '_shipping_phone', true) . '</p>';
    $display_meta .= '<p><strong>收貨人信箱:</strong> ' . get_post_meta($order->get_id(), '_shipping_email', true) . '</p>';
    echo $display_meta;
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'mxp_custom_checkout_field_display_admin_order_shipping_meta', 10, 1);

function mxp_checkout_page_footer() {
    if (is_checkout() || is_account_page()):
    ?>
    <script>
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
        jQuery('div.woocommerce').on('change', '.qty', function(){
           jQuery("[name='update_cart']").removeAttr('disabled');
           jQuery("[name='update_cart']").trigger("click");
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
    $new_columns                = (is_array($columns)) ? $columns : array();
    $new_columns['order_notes'] = '訂單備註';
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
    $value['#billing_first_name']     = '<input type="text" class="input-text " name="billing_first_name" id="billing_first_name" placeholder="請填>入最多中文5個字或英文10個字" value="' . WC()->session->get('billing_first_name') . '" autocomplete="given-name" autofocus="autofocus">';
    $value['#billing_phone']          = '<input type="tel" class="input-text " name="billing_phone" id="billing_phone" placeholder="手機號碼格式為:0912345678" value="' . WC()->session->get('billing_phone') . '" autocomplete="tel">';
    $value['#billing_company']        = '<input type="text" class="input-text " name="billing_company" id="billing_company" placeholder="" value="' . WC()->session->get('billing_company') . '" autocomplete="organization">';
    $value['#billing_email']          = '<input type="email" class="input-text " name="billing_email" id="billing_email" placeholder="" value="' . WC()->session->get('billing_email') . '" autocomplete="email">';
    $value['#billing_company_tax_id'] = '<input type="text" class="input-text " name="billing_company_tax_id" id="billing_company_tax_id" placeholder="公司統編" value="' . WC()->session->get('billing_company_tax_id') . '">';
    $value['billing_postcode']        = '<input type="text" id="billing_postcode" name="billing_postcode" placeholder="郵遞區號" readonly="" value="' . WC()->session->get('billing_postcode') . '" class="input-text">';
    return $value;
}
add_filter('woocommerce_update_order_review_fragments', 'mxp_wc_save_session_data');

//主題繼承覆蓋翻譯
function load_custom_wc_translation_file($mofile, $domain) {
    if ('woocommerce' === $domain) {
        $mofile = get_stylesheet_directory() . '/languages/woocommerce/' . get_locale() . '.mo';
    }
    return $mofile;
}
add_filter('load_textdomain_mofile', 'load_custom_wc_translation_file', 11, 2);

//整合綠界物流外掛的驗證補強
// function mxp_check_checkout_post_data() {
//     $sm = wc_get_chosen_shipping_method_ids();
//     if (!empty($sm)) {
//         if ($sm[0] == 'ecpay_shipping') {
//             if ($_POST['billing_first_name']) {
//                 $s = $_POST['billing_first_name'];
//                 if (preg_match("/\p{Han}+/u", $s)) {
//                     if (mb_strlen($s) > 5) {
//                         wc_add_notice(__('帳單姓名請輸入最多 5 個中文字'), 'error');
//                     }
//                 } else {
//                     if (mb_strlen($s) > 10) {
//                         wc_add_notice(__('帳單姓名請輸入最多 10 個英文字'), 'error');
//                     }
//                 }
//             }
//             if ($_POST['shipping_first_name']) {
//                 $s = $_POST['shipping_first_name'];
//                 if (preg_match("/\p{Han}+/u", $s)) {
//                     if (mb_strlen($s) > 5) {
//                         wc_add_notice(__('收件人姓名請輸入最多 5 個中文字'), 'error');
//                     }
//                 } else {
//                     if (mb_strlen($s) > 10) {
//                         wc_add_notice(__('收件人姓名請輸入最多 10 個英文字'), 'error');
//                     }
//                 }
//             }
//             if ($_POST['billing_phone']) {
//                 $s = $_POST['billing_phone'];
//                 if (!preg_match('/^09[0-9]{8}$/', $s)) {
//                     wc_add_notice(__('帳單手機格式錯誤，格式為 0912345678'), 'error');
//                 }
//             }
//             if ($_POST['shipping_phone']) {
//                 $s = $_POST['shipping_phone'];
//                 if (!preg_match('/^09[0-9]{8}$/', $s)) {
//                     wc_add_notice(__('收件人手機格式錯誤，格式為 0912345678'), 'error');
//                 }
//             }
//         }
//     }
// }
// add_action('woocommerce_checkout_process', 'mxp_check_checkout_post_data');

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