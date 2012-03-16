<?php
require_once("system/shared.php");
$db = Database::getInstance();

$stmt = $db->prepareAbe("SELECT b.block_height
FROM block b
JOIN chain c ON c.chain_last_block_id = b.block_id
WHERE c.chain_id = 1");

$db->select($stmt);
$stmt->bind_result($id);

if ($stmt->fetch()) {
    $localblock = $id;
} else {
    $localblock = "?";
}

function getCacheValue($key) {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT `value` FROM `cache` WHERE `key`=?");
    $stmt->bind_param('s', $key);
    $db->select($stmt);
    $stmt->bind_result($id);

    if ($stmt->fetch()) {
        return $id;
    } else {
        return null;
    }
}

$bbeblock = getCacheValue("BBE");
if ($bbeblock == null) $bbeblock = "?";
$bciblock = getCacheValue("BCI");   //Anyone know how to get latest block from BCI ?
if ($bciblock == null) $bciblock = "?";

?>
<div class="span4">
    <h3>System status</h3>
    Latest local block : <?php echo$localblock;?><br>
    Latest <a href="http://blockexplorer.com/" target="_blank">BBE</a> block : <?php echo$bbeblock;?><br>
    <br>
    <small>The system does not use BBE for data collection, the information is simply to see if our database is updated.</small>
</div>
</div>

