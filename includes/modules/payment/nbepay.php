<?php
/**
 * MOLPay ZendCart Plugin
 * 
 * @package Payment Gateway
 * @author MOLPay Technical Team <technical@molpay.com>
 * @version 2.0.0
 */
 
class nbepay {
    public  $code,
            $title, 
            $description, 
            $enabled;

    function nbepay()  {
        global $db, $order;
        $this->code = 'nbepay';
        $this->title = MODULE_PAYMENT_NBEPAY_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_NBEPAY_TEXT_DESCRIPTION;
        $thiglobals->sort_order = MODULE_PAYMENT_NBEPAY_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_NBEPAY_STATUS == 'True') ? true : false);
        $this->form_action_url = "https://www.onlinepayment.com.my/NBepay/pay/".MODULE_PAYMENT_NBEPAY_ID."/";

        if ((int)MODULE_PAYMENT_NBEPAY_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_NBEPAY_ORDER_STATUS_ID;
        }

        if (is_object($order))
            $this->update_status();

    }


    function update_status() {
        global $order, $db;
        if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NBEPAY_ZONE > 0) ) {
            $check_flag = false;
            $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_NBEPAY_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while (!$check->EOF) {
                if ($check->fields['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                }
                elseif ($check->fields['zone_id'] == $order->billing['zone_id'])  {
                    $check_flag = true;
                    break;
                }
                $check->MoveNext();
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    /**
     * Check the user input submited on checkout_payment.php with javascript (client-side).
     * 
     * @return boolean
     */
    function javascript_validation() {
        return false;
    }
    
    /**
     * Display on payment selection
     * 
     * @global type $order
     * @return array
     */
    function selection() {
        global $order;

        return array('id' => $this->code,
                'module' => 'NBePay Online Payment Gateway(Visa, MasterCard, Maybank2u, MEPS, FPX, etc)'
        );
    }

    function pre_confirmation_check() {
        global $_POST;
        return false;
    }

    function confirmation() {
        return false;
    }

    function process_button() {
        global $_POST, $languages_id, $shipping_cost, $total_cost, $shipping_selected, $shipping_method, $currencies, $currency, $customer_id , $db, $order;

        $prod = $order->product;

        while ( list($key,$val) = each($order->products) ) {
            $pname.= $val[name]." x ".$val[qty]."\n";  
        }

        $zenId = zen_session_name() . '=' . zen_session_id();
        $cartId = zen_session_id();
        $curr_obj = $order->info;
        $currency = $curr_obj[currency];

        $OrderAmt = number_format($order->info['total'] * $currencies->get_value($currency), $currencies->get_decimal_places($currency), '.', '') ; 

        $oid_sql = "select Max(orders_id) as oid from ".TABLE_ORDERS." ";
        $oid = $db->Execute($oid_sql);
        $oid = $oid->fields['oid']+1;
        $returnurl = MODULE_PAYMENT_NBEPAY_RETURNURL;
        $vcode = md5($OrderAmt.MODULE_PAYMENT_NBEPAY_ID.$oid.MODULE_PAYMENT_NBEPAY_VKEY);

        $process_button_string = 
        zen_draw_hidden_field('currency', strtolower($currency)) . 
        zen_draw_hidden_field('bill_desc', $pname) .
        zen_draw_hidden_field('orderid', $oid) .
        zen_draw_hidden_field('returnurl', $returnurl).
        zen_draw_hidden_field('vcode', $vcode).
        zen_draw_hidden_field('amount', $OrderAmt) ;

        $language_code_raw = "select code from " . TABLE_LANGUAGES . " where languages_id ='$languages_id'";
        $language_code = $db->Execute($language_code_raw);

        $process_button_string.=	
        zen_draw_hidden_field('bill_name', $order->customer['firstname'] . ' ' . $order->customer['lastname']) .
        zen_draw_hidden_field('country', $order->customer['country']['iso_code_2']) .
        zen_draw_hidden_field('bill_mobile', $order->customer['telephone']) .
        zen_draw_hidden_field('bill_email', $order->customer['email_address']) ;

        return $process_button_string ;
    }


    function before_process() {
        //global $_POST;
    }

    function after_process() {
        return false;
    }

    function get_error() {
        global $_GET;

        $error = array('title'=>'NBePay Error',
                       'error'=>'Error Detail');

        //return false;
    }

    function check() {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_NBEPAY_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    function install() {
        global $db;
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values 
        ('Enable NBePay Module', 'MODULE_PAYMENT_NBEPAY_STATUS', 'True', 'Do you want to accept NBePay payments?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('NBePay Merchant ID', 'MODULE_PAYMENT_NBEPAY_ID', '', 'Your NBePay Merchant ID', '6', '2', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('NBePay verify key', 'MODULE_PAYMENT_NBEPAY_VKEY', '', 'Please refer your NBePay merchant profile to have this key', '6', '5', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('NBePay multiple return url', 'MODULE_PAYMENT_NBEPAY_RETURNURL', '', 'Provide NBePay Multi Return URL if you wish to have this fetaures. <i>e.g : http://www.yourdomain.com/process.php</i>', '6', '5', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values
         ('Sort order of display.', 'MODULE_PAYMENT_NBEPAY_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values
         ('Set Order Status', 'MODULE_PAYMENT_NBEPAY_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");

    }

    function remove() {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

////////////////////////////////////////////////////
// Create our Key - > Value Arrays
////////////////////////////////////////////////////
    function keys() {
        return array(
            'MODULE_PAYMENT_NBEPAY_STATUS'
            ,'MODULE_PAYMENT_NBEPAY_ID'
            ,'MODULE_PAYMENT_NBEPAY_VKEY'
            ,'MODULE_PAYMENT_NBEPAY_RETURNURL'
            ,'MODULE_PAYMENT_NBEPAY_SORT_ORDER'
            ,'MODULE_PAYMENT_NBEPAY_ORDER_STATUS_ID'
        );
    }
}
?>