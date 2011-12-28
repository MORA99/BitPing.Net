<?
require(dirname(__FILE__)."/../www/system/shared.php");

function httpPost($url, $params) {
    $options = "";
    foreach ($params as $key=>$val) {
        $options .= "&".$key."=".urlencode($val);
    }

    $curl_handle=curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,$url);
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,10);

    curl_setopt($curl_handle,CURLOPT_POST,TRUE);
    curl_setopt($curl_handle,CURLOPT_POSTFIELDS,$options);

    $result = curl_exec($curl_handle);
    curl_close($curl_handle);

    return $result;
}

$txseqkey = "last_tx";
$db = Database::getInstance();

$stmt = $db->prepare("SELECT `value` FROM `sequence` WHERE `key`=?");
$stmt->bind_param("s", $txseqkey);
$db->select($stmt);
$stmt->bind_result($lasttx);
$stmt->fetch();

//Build array of monitored addresses
$addresses = array();
$res = $db->query("SELECT address,order_id FROM `order_address`");

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_array()) {
        if (!isset($addresses[$row['address']]))
            $addresses[$row['address']] = array();

        $addresses[$row['address']][] = $row['order_id'];
    }
}


//Select 1000 new outputs, if there are more the script will slowly catch up (Unless bitcoin grows to where 10.000 outputs are used each minute)
$res = $db->queryAbe("SELECT `pubkey_hash` as `pubkey`, `txout_value` as `value`, `tx_id`, `block_id` FROM txout_detail WHERE `tx_id` > ".$lasttx." LIMIT 10000");
if ($res) {
    $num = $res->num_rows;
    echo "Found $num new transactions \r\n";
    flush();

//For now we calculate the address from the hash of each transaction, if there is alot of transactions it would be better to calculate the hash of the monitored addresses, and store it in the database.
    $bc = new Bitcoin();

    if ($num > 0) {
        $txid = $lasttx;

        while ($row = $res->fetch_array()) {
            $address = $bc->hash160ToAddress($row['pubkey']);
            $txid = $row['tx_id'];
            $value = $row['value'];

//		echo $row['tx_id']." - ".$row['pubkey']." (".$address.") = ".$row['value']."\r\n";
//		flush();

            if (isset($addresses[$address])) {
                echo "$address is a monitored address !! \r\n";
                foreach ($addresses[$address] as $orderid) {
                    $db->query("INSERT IGNORE INTO active_monitors (`order_id`, `tx_id`, `address`, `value`) VALUES (".$orderid.", ".$txid.", '".$address."', ".$value.")");
                }
            }
        }

        $stmt = $db->prepare("UPDATE `sequence` SET `value`=? WHERE `key`=?");
        $stmt->bind_param("is", $txid, $txseqkey);
        $db->update($stmt);
    }
}

//Check if any of the active monitors are ready to be sent a notification
$stmt = $db->prepareAbe("SELECT b.block_height
FROM block b
JOIN chain c ON c.chain_last_block_id = b.block_id
WHERE c.chain_id = 1");

$db->select($stmt);
$stmt->bind_result($id);

if ($stmt->fetch()) {
    $maxheight = $id;
} else {
    die();
}

$res = $db->query("SELECT order_id,tx_id,address,`value` FROM `active_monitors`");
while ($row = $res->fetch_array()) {
    $tx = $row['tx_id'];
    $orderid = $row['order_id'];
    $address = $row['address'];
    $value = $row['value'];

    echo "Check if $tx has enough confirmations for order $orderid\r\n";

    $blockres = $db->queryAbe("SELECT block_id FROM block_tx WHERE tx_id = ".$row['tx_id']."");
    if ($blockres) {
        $blockrow = $blockres->fetch_array();
        $block = $blockrow["block_id"];
        echo "Block for tx is ".$block."\r\n";

        $heightres = $db->queryAbe("SELECT block_height FROM chain_candidate WHERE block_id = ".$block." AND chain_id = 1");
        if ($heightres) {
            $heightrow = $heightres->fetch_array();
            $currentheight = $heightrow["block_height"];

            $confirmations = $maxheight - $currentheight + 1;
            echo "confirmatins : ".$confirmations."\r\n";

            $order = new Order($orderid);
            if ($confirmations >= $order->confirmations) {
                echo "$tx has enough confirmations for notification.";
                $success = true;

                $stmt = $db->prepareAbe("SELECT tx_hash FROM tx WHERE tx_id = ?");
                $stmt->bind_param("i", $tx);
                $db->select($stmt);
                $stmt->bind_result($txhash);
                $stmt->fetch();

                $user = new User($order->userid);

                foreach ($order->notifications as $notification) {
                    $btc = round($value / 100000000,8);

                    switch ($notification->id) {
                        case 1:
                        //Email
                            $btc = round($value / 100000000,8);
                            mail($user->email, "BitPing.Net notification for ".$address." (".$confirmations." confirmations)", "This is a requested notification from BitPing.Net\r\n\r\nThe address : $address has received a payment of $btc BTC in tx $txhash included in block $block", "FROM: monitor@BitPing.Net");
                            break;

                        case 2:
                        //HTTP
                            $data = array();

                            $data["to_address"] = $address;
                            $data["amount"] = $value;
                            $data["btc_amount"] = $btc;
                            $data["confirmations"] = $confirmations;
                            $data["txhash"] = $txhash;
                            $data["block"] = $block;
                            $data["signature"] = sha1( $address . $value . $confirmations . $txhash . $block . $user->secret );

                            $result = httpPost($user->url, $data);
                            if ($result !== TRUE)
                                $success = false;
                            break;
                    }
                }

                //START COMMENT HERE IF YOU ARE DEBUGGING DELIVERY PROBLEMS

                if ($success) {
                    echo "Completed notification for TX:$tx Order:$orderid\r\n";

                    $stmt = $db->prepare("INSERT INTO notifications_sent (`order_id`, `tx`, `address`, `value`) VALUE (?, ?, ?, ?)");
                    $stmt->bind_param("issi", $orderid, $txhash, $address, $value);
                    $db->update($stmt);

                    $stmt = $db->prepare("DELETE FROM `active_monitors` WHERE order_id=? AND tx_id=? LIMIT 1");
                    $stmt->bind_param("ii", $orderid, $tx);
                    $db->update($stmt);
                } else {
                    $stmt = $db->prepare("UPDATE `active_monitors` SET failures=failures+1 WHERE order_id=? AND tx_id=? LIMIT 1");
                    $stmt->bind_param("ii", $orderid, $tx);
                    $db->update($stmt);
                }

                //STOP COMMENT HERE

            }
        }
    }
}

//Cleanup - Delete any monitors with a failure count over 10 or a timestamp more than 24hours ago
$stmt = $db->query("DELETE FROM `active_monitors` WHERE failures > 10 OR NOW() > TIMESTAMPADD(HOUR,24,`timestamp`) LIMIT 1");
