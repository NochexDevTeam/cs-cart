<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Http;

if (!defined('BOOTSTRAP') && is_array($_POST)) {
    require './init_payment.php';

    $order_id = $_REQUEST['order_id'];

    if (strpos($order_id, '_')) {
        $order_id = substr($order_id, 0, strpos($order_id, '_'));
    }

    if (!fn_check_payment_script('nochex.php', $order_id)) {
        exit;
    }

    $order_info = fn_get_order_info($order_id);

    if (empty($order_info)) {
        exit;
    }

if ($_REQUEST["optional_2"] == "Enabled") {

	$postvars = http_build_query($_POST);

    $result = Http::post('https://secure.nochex.com/callback/callback.aspx', $postvars);
    $result = str_replace("\n", '&', $result);


	$order_info['total'] = fn_format_price($order_info['total']);
    $_REQUEST['amount']  = fn_format_price($_REQUEST['amount']);

    $pp_response['order_status'] = ($result == 'AUTHORISED' && $order_info['total'] == $_REQUEST['amount']) ? 'P' : 'F';
    $pp_response["reason_text"] = "SecurityKey: {$_REQUEST['security_key']}, Transaction Date: {$_REQUEST['transaction_date']}";

    if ($order_info['total'] != $_REQUEST['amount']) {
        $pp_response['reason_text'] .= '; ' . __('order_total_not_correct');
    }

    $pp_response['transaction_id'] = $_REQUEST['transaction_id'];

} else {

    $post = array(
        'transaction_id' => $_REQUEST['transaction_id'],
        'transaction_date' => $_REQUEST['transaction_date'],
        'from_email' => $_REQUEST['from_email'],
        'to_email' => $_REQUEST['to_email'],
        'order_id' => $_REQUEST['order_id'],
        'amount' => $_REQUEST['amount'],
        'security_key' => $_REQUEST['security_key']
    );

    // Post a request and analyse the response
    $result = Http::post('https://secure.nochex.com/apc/apc.aspx', $post);
    $result = str_replace("\n", '&', $result);

    $order_info['total'] = fn_format_price($order_info['total']);
    $_REQUEST['amount']  = fn_format_price($_REQUEST['amount']);

    $pp_response['order_status'] = ($result == 'AUTHORISED' && $order_info['total'] == $_REQUEST['amount']) ? 'P' : 'F';
    $pp_response["reason_text"] = "SecurityKey: {$_REQUEST['security_key']}, Transaction Date: {$_REQUEST['transaction_date']}";

    if ($order_info['total'] != $_REQUEST['amount']) {
        $pp_response['reason_text'] .= '; ' . __('order_total_not_correct');
    }

    $pp_response['transaction_id'] = $_REQUEST['transaction_id'];
}

    fn_finish_payment($order_id, $pp_response);
    exit;

} elseif (defined('PAYMENT_NOTIFICATION')) {

    if ($mode == 'notify') {

        if (fn_check_payment_script('nochex.php', $_REQUEST['order_id'])) {
            $order_info = fn_get_order_info($_REQUEST['order_id']);

            if ($order_info['status'] == 'O') {
                $pp_response['order_status'] = 'F';
                fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
            }
        }

        fn_order_placement_routines('route', $_REQUEST['order_id']);
    }

} else {
    if (!defined('BOOTSTRAP')) { die('Access denied'); }

    $return_url_s = fn_url("payment_notification.notify?payment=nochex&order_id=$order_id", AREA, 'current');
    $return_url_c = fn_url("payment_notification.notify?payment=nochex&order_id=$order_id", AREA, 'current');
    $responder_url = fn_payment_url('current', "nochex.php");
    $_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;
	
	if ($processor_data['processor_params']['testmode'] === "Y") {
		$testmode = "100";
	} else {
		$testmode = "";
	}
	
	$ordered_products = "";
	$xml_collection = "<items>";
	
	foreach ($order_info['products'] as $key => $value){	
		$ordered_products .= $value['product'] . " - " . $value['amount'] . " X " . $value['price'];
		$xml_collection .= "<item><id></id><name>".$value['product']."</name><description></description><quantity>".$value['amount']."</quantity><price>".$value['price']."</price></item>";
	}
	
	$xml_collection .= "</items>";
	
	if ($processor_data['processor_params']['detailmode'] === "Y") {
		$ordered_products = $processor_data['processor_params']['payment_description'];
	} else {
		$xml_collection = "";
	}
	
	if ($processor_data['processor_params']['hidemode'] === "Y") {
		$hide_billing = "true";
	} else {
		$hide_billing = "";
	}
	
	if ($processor_data['processor_params']['postmode'] === "Y") {
		$orderTotal = $order_info['total'] - $order_info['shipping_cost'];
		$postage = $order_info['shipping_cost'];
	} else {
		$orderTotal = $order_info['total'];
		$postage = 0;
	}
	
	$cleanNumber = str_replace("+","",$order_info['phone']);
	$cleanNumber = str_replace("(","",$cleanNumber);
	$cleanNumber = str_replace(")","",$cleanNumber);
	$cleanNumber = str_replace("-","",$cleanNumber);
	
    $post_data = array(
        'merchant_id' => $processor_data['processor_params']['merchantid'],
        'amount' => $orderTotal,
        'postage' => $postage,
        'description' => $ordered_products,
        'xml_item_collection' => $xml_collection,
        'order_id' => $_order_id,
        'test_transaction' => $testmode,
        'test_success_url' => $return_url_s,
        'success_url' => $return_url_s,
        'cancel_url' => $return_url_c,
        'callback_url' => $responder_url,
		'hide_billing_details' => $hide_billing,
        'billing_fullname' => $order_info['b_firstname'] . ' ' . $order_info['b_lastname'],
        'billing_address' => $order_info['b_address'] . ' ' . $order_info['b_address_2'],
        'billing_city' => $order_info['b_city'],
        'billing_postcode' => $order_info['b_zipcode'],
        'delivery_fullname' => $order_info['s_firstname'] . ' ' . $order_info['s_lastname'],
        'delivery_address' => $order_info['s_address'] . ' ' . $order_info['s_address_2'],
        'delivery_city' => $order_info['s_city'],
        'delivery_postcode' => $order_info['s_zipcode'],
        'email_address' => $order_info['email'],
        'customer_phone_number' => $cleanNumber,
        'optional_2' => "Enabled",
    );

    fn_create_payment_form('https://secure.nochex.com/default.aspx', $post_data, 'Nochex');

}

exit;
