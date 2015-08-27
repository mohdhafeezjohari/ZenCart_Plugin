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

$nbcb 	  = $_POST['nbcb'];

$domain	  = MODULE_PAYMENT_MOLPAY_ID;
$amount   = $info['amount'];
$orderid  = $info['orderid'];
$tranID   = $info['tranID'];
$appcode  = $info['appcode'];
$status	  = $info['status'];
$currency = $info['currency'];
$paydate  = $info['paydate'];
$channel  = $info['channel'];
$skey     = $info['skey'];
$password = MODULE_PAYMENT_MOLPAY_VKEY;

$key0 = md5($tranID.$orderid.$status.$domain.$amount.$currency);
$key1 = md5($paydate.$domain.$key0.$appcode.$password);

if($skey==$key1)
{
	if ($status=="00") 
	{
		$db->Execute("update " . TABLE_ORDERS . "
							set orders_status = 3
	                        where orders_id = '" . (int)$orderid . "'");
	}
	elseif($status=="11")
	{
		$db->Execute("update " . TABLE_ORDERS . "
							set orders_status = 1
	                        where orders_id = '" . (int)$orderid . "'");
	}
	elseif($status=="22")
	{
		$db->Execute("update " . TABLE_ORDERS . "
							set orders_status = 2
	                        where orders_id = '" . (int)$orderid . "'");
	}
}

if($nbcb==1)
{
	echo 'CBTOKEN:MPSTATOK​';
	exit();
}

?>
