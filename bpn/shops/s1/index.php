<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>

<?
require("shared.php");

$db = Database::getInstance();
$db->query("LOCK TABLE `s1_addresses` WRITE");

$stmt = $db->prepare("SELECT `address` FROM `s1_addresses` ORDER BY `id` DESC LIMIT 1");
$db->select($stmt);
$stmt->bind_result($address);
if ($stmt->fetch())
{
	echo "Monitoring $address for payment ...<br><br>";
}
/*
$stmt = $db->prepare("DELETE FROM `s1_addresses` WHERE `address`=?");
$stmt->bind_param('s', $address);
$db->update($stmt);
*/
$db->query("UNLOCK TABLES");
?>

<a href="bitcoin:<?=$address?>?amount=0.01">bitcoin:<?=$address?>?amount=0.01</a><br>
<img src="qr.php?text=bitcoin:<?=$address?>?amount=0.01">


<script>
function checkPayment()
{
	$.getJSON('getPayment.php?address=<?=$address?>', function(data) {
	  if (data != null)
	  {
	        var btc = data['value'] / 100000000;
		alert("Got payment of "+data['value']+" ("+btc+" BTC) with "+data['confirmations']+" confirmations");
		//redirect to download page or something, note that the download page also needs to check that the payment is ok, since this is clientside
		window.location.replace("/s1/completed.php?address=<?=$address?>");
	  }
	  else
	  {
	        setTimeout(function(){checkPayment(); }, 5000);
  	  }
	});
}

setTimeout(function(){checkPayment(); }, 5000);

</script>
