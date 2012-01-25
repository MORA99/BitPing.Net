<?
require(dirname(__FILE__)."/../www/system/shared.php");

$db = Database::getInstance();
$opts = array(
  'http'=> array(
  'method'=>   "GET",
  'user_agent'=>    "MozillaXYZ/1.0"));

$context = stream_context_create($opts);
$json = file_get_contents('https://mtgox.com/code/data/ticker.php', false, $context);
$json = json_decode($json);
if (filter_var($json->{'ticker'}->{'last'},FILTER_VALIDATE_FLOAT) !== FALSE)
{
    $last = filter_var($json->{'ticker'}->{'last'},FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
    $key = "USDBTC";

    $stmt = $db->prepare("REPLACE INTO `exchange_rate` (`key`, `value`, `last_update`) VALUES (?, ?, NOW())");
    $stmt->bind_param('ss', $key, $last);
    $db->update($stmt);
}
?>

