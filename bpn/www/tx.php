<?
require("system/shared.php");

$arr = json_decode(file_get_contents('php://input'));

$data = $arr->params;

$bc = new Bitcoin();
$db = Database::getInstance();
$confirmations = 0;

foreach ($data as $tx)
{
        $outputs = $tx->out;

        foreach ($outputs as $output)
        {
                $btc = $output->value;
		$value = $btc * 100000000;

                $script = $output->scriptPubKey;
                $scriptparts = explode(" ", $script);
                $hash = "";

                for ($i=0; $i<count($scriptparts); $i++)
                {
                        if ($scriptparts[$i] == "OP_HASH160")
                        {
                                $hash = $scriptparts[$i+1];
                                break;
                        }
                }

                if ($hash != "")
                {
                        $address = $bc->hash160ToAddress($hash);
			$txhash = $tx->hash;
/*
                        $stmt = $db->prepare("INSERT INTO `unconfirmed_tx` (`txhash`, `to`, `value`, `timestamp`) VALUES (?, ?, ?, NOW())");
                        $stmt->bind_param('ssi', $txhash, $address, $value);
                        $db->insert($stmt);
*/
                        $stmt = $db->prepare("
                            SELECT order_address.order_id FROM `order_address`
                            JOIN orders on orders.order_id = order_address.`order_id`
                            WHERE orders.confirmations = 0 AND orders.active = 1
                            AND order_address.address = ?
                            ");
                        $stmt->bind_param('s', $address);
                        $db->select($stmt);
                        $stmt->bind_result($orderid);
                        while ($stmt->fetch())
                        {
                            $istmt = $db->prepare("INSERT INTO active_uncomfirmed_monitors (order_id, tx_hash, address, value) VALUES (?, ?, ?, ?)");
                            $istmt->bind_param('issi', $orderid, $txhash, $address, $value);
                            $db->insert($istmt);
                        }
                }
        }
}
