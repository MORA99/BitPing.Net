<?
require("system/config.php");
require("system/database.php");

$db = Database::getInstance();

$stmt = $db->prepare("SELECT `key`,`value`,`last_update` FROM exchange_rate");
$db->select($stmt);
$stmt->bind_result($key, $value, $update);
$data = array();

$data["disclaimer"] = "This data is collected from various providers and provided free of charge for informational purposes only, with no guarantee whatsoever of accuracy, validity, availability or fitness for any purpose; use at your own risk. Other than that - have fun!";
$data["license"]    = "Data collected from various providers with public-facing APIs; copyright may apply; not for resale; no warranties given.";
$data["origin"]     = "BTC rate from mtgox.com, all others from https://raw.github.com/currencybot/open-exchange-rates/master/latest.json";
$data["rates"]      = array();

$show = filter_var($_GET["rates"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$format = filter_var($_GET["format"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
$disclaimer = filter_var($_GET["disclaimer"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);


$showrates = array();

if ($show != "")
{
	$showrates = explode(",",$show);
}

while ($stmt->fetch())
{
	if (count($showrates) == 0 || in_array($key,$showrates))
	{
		if (count($showrates) == 1 && $format=="rate_only" && $disclaimer=="accepted")
			die($value);
		else
			$data["rates"][] = array("pair"=>$key,"rate"=>$value,"updated"=>$update);
	}
}

echo json_encode($data);
