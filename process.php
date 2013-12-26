<?php
/**
 * MOLPay ZendCart Plugin
 * 
 * @package Payment Gateway
 * @author MOLPay Technical Team <technical@molpay.com>
 * @version 2.0.0
 */

require('includes/application_top.php');

$info = ( $HTTP_POST_VARS )?$HTTP_POST_VARS:$_POST;

$domain	  = MODULE_PAYMENT_NBEPAY_ID;
$amount   = $info['amount'];
$orderid  = $info['orderid'];
$tranID   = $info['tranID'];
$appcode  = $info['appcode'];
$status	  = $info['status'];
$currency = $info['currency'];
$paydate  = $info['paydate'];
$channel  = $info['channel'];
$skey     = $info['skey'];
$password = MODULE_PAYMENT_NBEPAY_VKEY;

$key0 = md5($tranID.$orderid.$status.$domain.$amount.$currency);
$key1 = md5($paydate.$domain.$key0.$appcode.$password);

$ssl = "NONSSL";
if ( ENABLE_SSL != "false" ) {
    $ssl = "SSL";
}


if ($status=="00" && $skey==$key1) {
    // If success, will process.
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PROCESS, 'cID=' . $orderid, $ssl, true, false));
}
else {
    // Otherwise will go to checkout payment page.
    $nb_error = "Unsuccessfull Online Payment. Please make payment again. ";
    $messageStack->add_session('checkout_payment', $nb_error, 'error');
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', $ssl, true, false));
}
exit();
?>