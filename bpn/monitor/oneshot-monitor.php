<?
require(dirname(__FILE__)."/../www/system/shared.php");

echo "One shot monitor\r\n";

$db = Database::getInstance();

$stmt = $db->prepareAbe("SELECT b.block_height
FROM block b
JOIN chain c ON c.chain_last_block_id = b.block_id
WHERE c.chain_id = 1");

$db->select($stmt);
$stmt->bind_result($id);

if ($stmt->fetch()) {
    $maxheight = $id;
} else {
    die("Didnt get max height");
}

$stmt = $db->prepare("SELECT `id`, `created_at`,`url`,`address`,`key`,`confirmations` FROM `oneshot` WHERE `completed` = 0 AND confirmations > 0");
$stmt->bind_result($shotid, $start, $url, $address, $key, $confirmations);
$db->select($stmt);

while ($stmt->fetch()) {
    echo "$start -- $url -- $address -- $key-- $confirmations \r\n";

    //Check if there are any payments made after $start with $confirmations
    $res = $db->queryAbe("SELECT `pubkey_hash` as `pubkey`, `txout_value` as `value`, `tx_id`, `block_id` FROM txout_detail WHERE `tx_id` > ".$start."");
    if ($res) {
        $num = $res->num_rows;
        echo "Found $num new transactions \r\n";
        flush();

        //For now we calculate the address from the hash of each transaction.
	//If there is alot of transactions it would be better to calculate the hash of the monitored addresses, and store it in the database.
        $bc = new Bitcoin();

        if ($num > 0) {
            while ($row = $res->fetch_array()) {
                $payee = $bc->hash160ToAddress($row['pubkey']);
                $tx = $row['tx_id'];
                $orderid = $row['order_id'];
                $value = $row['value'];
                $btc = round($value / 100000000,8);

                if ($address == $payee) {
                    echo "MATCH for $payee !!!";



                    $blockres = $db->queryAbe("SELECT block_id FROM block_tx WHERE tx_id = ".$row['tx_id']."");
                    if ($blockres) {
                        $blockrow = $blockres->fetch_array();
                        $block = $blockrow["block_id"];
                        echo "Block for tx is ".$block."\r\n";

                        $heightres = $db->queryAbe("SELECT block_height FROM chain_candidate WHERE block_id = ".$block." AND chain_id = 1");
                        if ($heightres) {
                            $heightrow = $heightres->fetch_array();
                            $currentheight = $heightrow["block_height"];
                            $currentconfirm = $maxheight - $currentheight + 1;
                            echo "confirmatins : ".$currentconfirm."\r\n";

                            if ($currentconfirm >= $confirmations) {
                                echo "TX IS CONFIRMED PLENTY \r\n";

                                //Send http call
                                $stmt = $db->prepareAbe("SELECT tx_hash FROM tx WHERE tx_id = ?");
                                $stmt->bind_param("i", $tx);
                                $db->select($stmt);
                                $stmt->bind_result($txhash);
                                $stmt->fetch();

                                $data = array();

                                $data["to_address"] = $payee;
                                $data["amount"] = $value;
                                $data["btc_amount"] = $btc;
                                $data["confirmations"] = $currentconfirm;
                                $data["txhash"] = $txhash;
                                $data["block"] = $currentheight;
                                $data["signature"] = sha1( $address . $value . $confirmations . $txhash . $currentheight . $key );

                                if(filter_var($url, FILTER_VALIDATE_URL) !== FALSE && $url != "") {
                                    $result = httpPost($url, $data);
                                    if ($result !== FALSE) {
                                        //update database, set completed=1
                                        $stmt = $db->prepare("UPDATE `oneshot` SET `completed`=1 WHERE `id`=?");
                                        $stmt->bind_param('i', $shotid);
                                        $db->update($stmt);

                                        $stmt = $db->prepare("INSERT INTO notifications_sent (`order_id`, `oneshot`, `tx`, `address`, `value`) VALUES (?, 1, ?, ?, ?)");
                                        $stmt->bind_param('issi', $shotid, $txhash, $address, $value);
                                        $db->insert($stmt);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
