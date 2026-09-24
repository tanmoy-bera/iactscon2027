<?php
include_once("includes/frontend.init.php");
include_once('includes/function.delegate.php');
include_once('includes/function.invoice.php');
include_once('includes/function.accompany.php');
include_once('includes/function.workshop.php');
include_once('includes/function.registration.php');
include_once('includes/function.messaging.php');
include_once('includes/function.messaging.php');
require_once(__DIR__ . '/vendor/autoload.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// Get user and slip info
$delegateId = $_REQUEST['delegate_id'];
$slipId = $_REQUEST['slip_id'];
$requestId = $_GET['HAND'] ?? $_POST['HAND'] ?? null;

// Sessions
$mycms->setSession('LOGGED.USER.ID', $delegateId);
$mycms->setSession('SLIP_ID', $slipId);

// User details & amounts
$userDetails = getUserDetails($delegateId);
$payCurrency = getRegistrationCurrency(getUserClassificationId($delegateId));
$pendingAmount = pendingAmountOfSlip($slipId);
$invoiceAmount = invoiceAmountOfSlip($slipId);
$amount = ($pendingAmount > 0 ? $pendingAmount : $invoiceAmount) * 100; // in paise

// Razorpay return URL

// INSERTING PAYMENT REQUEST
$sqlInsertPaymentRequest        = array();
$sqlInsertPaymentRequest['QUERY']       = "INSERT INTO " . _DB_PAYMENT_REQUEST_ . " 
										       SET `transaction_date` = ?, 
											       `delegate_id` = ?,
												   `slip_id` = ?,
												   `payment_gateway` = ?, 
												   `status` = ?, 
												   `created_ip` = ?, 
												   `created_sessionId` = ?, 
												   `created_browser` = ?,
												   `created_dateTime` = ?";

$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'transaction_date', 'DATA' => date('Y-m-d'),                'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'delegate_id',      'DATA' => $delegateId,                  'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'slip_id',          'DATA' => $slipId,                      'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'payment_gateway',  'DATA' => 'RAZORPAY',                       'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'status',           'DATA' => 'A',                          'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_ip',       'DATA' => $_SERVER['REMOTE_ADDR'],      'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_sessionId', 'DATA' => session_id(),                 'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_browser',  'DATA' => $_SERVER['HTTP_USER_AGENT'],  'TYP' => 's');
$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_dateTime', 'DATA' => date('Y-m-d H:i:s'),           'TYP' => 's');

$requestId = $mycms->sql_insert($sqlInsertPaymentRequest, false);


$sqlProcessUpdateStep  = array();
$sqlProcessUpdateStep['QUERY']          = " UPDATE  " . _DB_PROCESS_STEP_ . "
												   SET `payment_status` = ?
												 WHERE `id` = ?";

$sqlProcessUpdateStep['PARAM'][]  = array('FILD' => 'payment_status', 'DATA' => 'COMPLETE',                                  'TYP' => 's');
$sqlProcessUpdateStep['PARAM'][]  = array('FILD' => 'id',             'DATA' => $mycms->getSession('PROCESS_FLOW_ID_FRONT'), 'TYP' => 's');

$mycms->sql_update($sqlProcessUpdateStep, false);


$mycms->removeSession('PROCESS_FLOW_ID_FRONT');

$transactionId                  = $cfg['invoive_number_format'] . number_pad($requestId, 5) . date('Ymdhi') . $slipId;

$returnUrl = _BASE_URL_ . "razorpay_payment_return.php?HAND=$requestId";

// Create Razorpay order
 $api = new Api($cfg['RAZORPAY_KEY_ID'], $cfg['RAZORPAY_SECRET']);
// $api = new Api('rzp_test_SFffDin9pNz04S', 's4H0hc0b8Plsnwm1RjN5aPZw'); //test

$orderData = [
    'receipt' => $delegateId . '_' . $slipId,
    'amount' => $amount,
    'currency' => $payCurrency,
    'payment_capture' => 1
];
$razorpayOrder = $api->order->create($orderData);

// Save order_id in DB
$mycms->sql_update([
    'QUERY' => "UPDATE " . _DB_PAYMENT_REQUEST_ . " SET transaction_id=? WHERE id=?",
    'PARAM' => [
        ['FILD'=>'transaction_id','DATA'=>$razorpayOrder->id,'TYP'=>'s'],
        ['FILD'=>'id','DATA'=>$requestId,'TYP'=>'s']
    ]
], false);
?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?=$cfg['RAZORPAY_KEY_ID']?>",
    "amount": "<?= $amount ?>",
    "currency": "<?= $payCurrency ?>",
    "name": "IACTSCON 2027",
    "description": "Conference Registration",
    "order_id": "<?= $razorpayOrder->id ?>",
    "handler": function(response){
		console.log(response);
    // alert("Handler triggered");
        // On successful payment, post to return page
        var form = document.createElement('form');
        form.method = "POST";
        form.action = "<?= $returnUrl ?>";
        ['razorpay_payment_id','razorpay_order_id','razorpay_signature'].forEach(function(k){
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = k;
            input.value = response[k];
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
    },
    "modal": {
        "ondismiss": function(){
            // Redirect to failure page if user closes the checkout
            
            window.location.href = "<?= _BASE_URL_ ?>online.payment.failure.php?HAND=<?= $requestId ?>";
        }
    },
    "prefill": {
        "name": "<?= $userDetails['user_full_name'] ?>",
        "email": "<?= $userDetails['user_email_id'] ?>",
        "contact": "<?= $userDetails['user_mobile_no'] ?>"
    },
    "theme": { "color": "#F37254" }
};

var rzp = new Razorpay(options);
rzp.open(); // auto-launch checkout
</script>
