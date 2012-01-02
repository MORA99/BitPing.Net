<?
require(dirname(__FILE__)."/../www/system/shared.php");

$db = Database::getInstance();
$bbe = filter_var(file_get_contents("http://blockexplorer.com/q/getblockcount"), FILTER_VALIDATE_INT);
$key = "BBE";
if ($bbe !== FALSE)
{
    $stmt = $db->prepare("REPLACE INTO `cache` (`key`, `value`, `last_update`) VALUES (?, ?, NOW())");
    $stmt->bind_param('ss', $key, $bbe);
    $db->update($stmt);

    $db->query("DELETE FROM `reset_pass_requests` WHERE `expires` < NOW()");
}
?>
