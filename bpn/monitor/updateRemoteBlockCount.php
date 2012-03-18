<?
require(dirname(__FILE__)."/../www/system/shared.php");

$db = Database::getInstance();

$context = stream_context_create(array(
    'http' => array(
        'timeout' => 10
    )
));

$bbe = filter_var(file_get_contents("http://blockexplorer.com/q/getblockcount", 0, $context), FILTER_VALIDATE_INT);
$key = "BBE";
if ($bbe !== FALSE)
{
    $stmt = $db->prepare("REPLACE INTO `cache` (`key`, `value`, `last_update`) VALUES (?, ?, NOW())");
    $stmt->bind_param('ss', $key, $bbe);
    $db->update($stmt);
}

$bci = filter_var(file_get_contents("http://blockchain.info/q/getblockcount", 0, $context), FILTER_VALIDATE_INT);
$key = "BCI";
if ($bci !== FALSE)
{
    $stmt = $db->prepare("REPLACE INTO `cache` (`key`, `value`, `last_update`) VALUES (?, ?, NOW())");
    $stmt->bind_param('ss', $key, $bci);
    $db->update($stmt);
}

$db->query("DELETE FROM `reset_pass_requests` WHERE `expires` < NOW()");
?>
