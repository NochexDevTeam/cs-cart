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

    $post = array();
    $post['transaction_id'] = $_REQUEST['transaction_id'];
    $post['transaction_date'] = $_REQUEST['transaction_date'];
    $post['from_email'] = $_REQUEST['from_email'];
    $post['to_email'] = $_REQUEST['to_email'];
    $post['order_id'] = $_REQUEST['order_id'];
    $post['amount'] = $_REQUEST['amount'];
    $post['security_key'] = $_REQUEST['security_key'];

    $order_id = (strpos($_REQUEST['order_id'], '_')) ? substr($_REQUEST['order_id'], 0, strpos($_REQUEST['order_id'], '_')) : $_REQUEST['order_id'];
    $order_info = fn_get_order_info($order_id);

    // Post a request and analyse the response
    $return = Http::post("https://www.nochex.com/apcnet/apc.aspx", $post);
    $result = str_replace("\n","&", $return);

    $order_info['total'] = fn_format_price($order_info['total']);
    $_REQUEST['amount']  = fn_format_price($_REQUEST['amount']);

    $pp_response['order_status'] = ($result == 'AUTHORISED' && $order_info['total'] == $_REQUEST['amount']) ? 'P' : 'F';
    $pp_response["reason_text"] = "SecurityKey: $_REQUEST[security_key], Transaction Date: $_REQUEST[transaction_date], Transaction Status: $_REQUEST[status], Result: $result";
    if ($order_info['total'] != $_REQUEST['amount']) {
        $pp_response["reason_text"] .= '; ' . __('order_total_not_correct');
    }
    $pp_response["transaction_id"] = $_REQUEST['transaction_id'];

    fn_finish_payment($order_id, $pp_response);
    exit;

} elseif (defined('PAYMENT_NOTIFICATION')) {
    if ($mode == 'notify') {
        $order_info = fn_get_order_info($_REQUEST['order_id']);
        if ($order_info['status'] == 'O') {
            $pp_response['order_status'] = 'F';
            fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
        }

        fn_order_placement_routines('route', $_REQUEST['order_id']);
    }

} else {
    if (!defined('BOOTSTRAP')) { die('Access denied'); }

    $return_url_s = fn_url("payment_notification.notify?payment=nochex&order_id=$order_id", AREA, 'current');
    $return_url_c = fn_url("payment_notification.notify?payment=nochex&order_id=$order_id", AREA, 'current');
    $responder_url = fn_payment_url('current', "nochex.php");
    $_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;


$products = $order_info['products'];


$description = "";
$xmlcollection = "<items>";

foreach($products as $key => $item ){

$description .= "Product: " . $item['product'] . ", Quantity: " . $item['amount'] . ", Price: " . $item["price"] . "  ";

$xmlcollection .= "<item><id>".$item['product_id']."</id><name>".$item['product']."</name><description>".$item['product']."</description><quantity>".$item['amount']."</quantity><price>".$item["price"]."</price></item> ";

}

$description .= "";
$xmlcollection .= "</items>";


if($processor_data['processor_params'][testmode] == "test"){

$testMode = "100";

}

if($processor_data['processor_params'][xmlMode] == "Yes"){

$xmlPcollection = $xmlcollection;
$descriptionP = "Order created for: " . $_order_id;

}else{

$descriptionP = $description;
$xmlPcollection = "";
}

/*if($processor_data['processor_params'][xmlMode] == "yes"){


}
*/
//print_r($order_info);

if($processor_data['processor_params'][postAmt] == "Yes"){

$pstAmt = $order_info['shipping_cost'];
$amt = $order_info['subtotal'];

}else{

$amt = $order_info['total'];
}

if($processor_data['processor_params'][HideM] == "Yes"){

$hideBilling = "1";

}
    $post_data = array(
        'merchant_id' => $processor_data['processor_params']['merchantid'],
        'amount' => $amt,
        'postage' => $pstAmt,
        'description' => $descriptionP,
        'xml_item_collection' => $xmlPcollection,
        'order_id' => $_order_id,
        'success_url' => $return_url_s,        
        'test_success_url' => $return_url_s,        
        'cancel_url' => $return_url_c,
		'hide_billing_details' => $hideBilling,
        'callback_url' => $responder_url,
        'billing_fullname' => $order_info['b_firstname'] . ' ' . $order_info['b_lastname'],
        'billing_address' => $order_info['b_address'] . ' ' . $order_info['b_address_2'],		
        'billing_city' => $order_info['b_city'],
        'billing_state' => $order_info['b_state'],
        'billing_postcode' => $order_info['b_zipcode'],
        'delivery_fullname' => $order_info['s_firstname'] . ' ' . $order_info['s_lastname'],
        'delivery_address' => $order_info['s_address'] . ' ' . $order_info['s_address_2'],
		'delivery_city' => $order_info['s_city'],
		'delivery_state' => $order_info['s_state'],
        'delivery_postcode' => $order_info['b_zipcode'],
        'email_address' => $order_info['email'],
        'test_transaction' => $testMode,
        'customer_phone_number' => $order_info['phone'],        
    );
//
    fn_create_payment_form('https://secure.nochex.com', $post_data, 'Nochex');
}
exit;
