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



$json = file_get_contents('https://raw.github.com/currencybot/open-exchange-rates/master/latest.json', false, $context);
$json = json_decode($json, true);

$key = "USDBTC";
$stmt = $db->prepare("SELECT `value` FROM exchange_rate WHERE `key`=?");
$stmt->bind_param('s', $key);
$db->select($stmt);
$stmt->bind_result($btc);
$stmt->fetch();

$stmt = $db->prepare("REPLACE INTO `exchange_rate` (`key`, `value`, `last_update`) VALUES (?, ?, NOW())");

foreach ($json['rates'] as $cur=>$rate)
{
    $key = $cur."USD";
    $stmt->bind_param('ss', $key, $rate);
    $db->update($stmt);

    $key = $cur."BTC";
    $btcrate = $btc * $rate;
    $stmt->bind_param('ss', $key, $btcrate);
    $db->update($stmt);
}



?>

