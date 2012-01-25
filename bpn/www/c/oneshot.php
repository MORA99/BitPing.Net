<?
require("../system/shared.php");

$address = filter_var($_REQUEST["address"], FILTER_SANITIZE_STRING);
$url = filter_var($_REQUEST["url"], FILTER_SANITIZE_URL);
$confirmations = filter_var($_REQUEST["confirmations"], FILTER_SANITIZE_NUMBER_INT);
$key = filter_var($_REQUEST["key"],  FILTER_SANITIZE_STRING);
$ip = $_SERVER['REMOTE_ADDR'];


if (
$address == "" || $address === FALSE
||
$url == "" || $url === FALSE
||
$confirmations == "" || $confirmations === FALSE || !is_numeric($confirmations)
)
die("ERROR");


$db = Database::getInstance();

$txseqkey = "last_tx";
$db = Database::getInstance();
$stmt = $db->prepare("SELECT `value` FROM `sequence` WHERE `key`=?");
$stmt->bind_param("s", $txseqkey);
$db->select($stmt);
$stmt->bind_result($lasttx);
$stmt->fetch();


$stmt = $db->prepare("INSERT INTO `oneshot` (`created_at`, `url`, `address`, `key`, `confirmations`, `created_from_ip`, `completed`) VALUES (?, ?, ?, ?, ?, ?, 0)");
$stmt->bind_param('sssis', $lasttx, $url, $address, $key, $confirmations, $ip);
$db->insert($stmt);
die("ACCEPTED");
