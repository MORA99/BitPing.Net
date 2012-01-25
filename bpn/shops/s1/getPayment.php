<?
require("shared.php");

$address = filter_var($_GET["address"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

if ($address === FALSE || $address == "")
	die();

$db = Database::getInstance();
$stmt = $db->prepare("SELECT `value`,`confirmations` FROM s1_payment WHERE address = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('s', $address);
$db->select($stmt);
$stmt->bind_result($value, $confirmations);
if ($stmt->fetch())
{
	$data = array();
	$data['value'] = $value;
	$data['confirmations'] = $confirmations;
	echo json_encode($data);
}
