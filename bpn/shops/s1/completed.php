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
?>
<center>
<img src="bitcoin.jpg" border=0><br>
Thank you for your donation !
</center>
<?
$stmt->close();
$stmt = $db->prepare("DELETE FROM s1_payment WHERE address = ?");
$stmt->bind_param('s', $address);
$db->update($stmt);
} else {
echo "You trying to cheat me?!?";
}
