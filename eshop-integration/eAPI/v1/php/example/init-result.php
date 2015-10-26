<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<pre><?php
require_once ('logger.php');
require_once ('crypto.php');
require_once ('setup.php');

echo "preparing payment init data ...\n\n";

$merchantId = $_POST ['merchant_id'];
$orderNo = $_POST ['order_no'];
$totalAmount = $_POST ['total_amount'];
$shippingAmount = $_POST ['shipping_amount'];
$returnUrl = $_POST ['return_url'];
$goods_desc = $_POST ['goods_desc'];
$description = $_POST ['description'];
$customerId = $_POST ['customer_id'];
$returnMethodPOST = "yes";
$closePayment = false;
$merchantData = null;

$dttm = (new DateTime ())->format ( "YmdHis" );

$cart = createCartData($goods_desc, $totalAmount, $shippingAmount);
echo "preparing cart data:\n";
echo htmlspecialchars(json_encode($cart, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE)) . "\n\n";

$data = createPaymentInitData($merchantId, $orderNo, $dttm, $totalAmount, $returnUrl, $cart, $description,
		$customerId, $privateKey, $privateKeyPassword, $closePayment, $merchantData, $returnMethodPOST);

echo "prepared payment/init request:\n";
echo json_encode($data, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE) . "\n\n";

echo "processing payment/init request ...\n\n";

$ch = curl_init ( $url . NativeApiMethod::$init );
curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode ( $data ) );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
		'Content-Type: application/json',
		'Accept: application/json;charset=UTF-8'
) );

$result = curl_exec ( $ch );

if(curl_errno($ch)) {
	echo 'payment/init failed, reason: ' . htmlspecialchars(curl_error($ch));
	return;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($httpCode != 200) {
	echo 'payment/init failed, http response: ' . htmlspecialchars($httpCode);
	return;
}

curl_close($ch);

echo "payment/init result:\n" . htmlspecialchars($result) . "\n\n";

$result_array = json_decode ( $result, true );
if(is_null($result_array ['resultCode'])) {
	echo 'payment/init failed, missing resultCode';
	return;
}

if (verifyResponse($result_array, $publicKey, "payment/init verify") == false) {
	echo 'payment/init failed, unable to verify signature';
	return;
}

if ($result_array ['resultCode'] != '0') {
	echo 'payment/init failed, reason: ' . htmlspecialchars($result_array ['resultMessage']);
	return;
}

$payId = $result_array ['payId'];
$params = createGetParams($merchantId, $payId, $dttm, $privateKey, $privateKeyPassword);

?>
</pre>
<a href="<?= htmlspecialchars($url . NativeApiMethod::$process . $params, ENT_QUOTES); ?>">payment/process</a><br/>
<a href="payment.php?action=status&merchant_id=<?= htmlspecialchars($merchantId, ENT_QUOTES); ?>&pay_id=<?= htmlspecialchars($payId, ENT_QUOTES); ?>">payment/status</a><br/>
<a href="payment.php?action=close&merchant_id=<?= htmlspecialchars($merchantId, ENT_QUOTES); ?>&pay_id=<?= htmlspecialchars($payId, ENT_QUOTES); ?>">payment/close</a><br/>
<a href="payment.php?action=reverse&merchant_id=<?= htmlspecialchars($merchantId, ENT_QUOTES); ?>&pay_id=<?= htmlspecialchars($payId, ENT_QUOTES); ?>">payment/reverse</a><br/>
<a href="payment.php?action=refund&merchant_id=<?= htmlspecialchars($merchantId, ENT_QUOTES); ?>&pay_id=<?= htmlspecialchars($payId, ENT_QUOTES); ?>">payment/refund</a><br/>
<br/>
<a href="index.php">new FORM payment/init</a><br/>
<a href="index-json.php">new JSON payment/init</a><br/>
</body>
</html>