<?
require(dirname(__FILE__)."/../www/system/shared.php");
require(dirname(__FILE__)."/../www/system/Pubnub.php");

$db = Database::getInstance();
$confirmations = 0;

//Check for active monitors
$res = $db->query("SELECT order_id,tx_hash,address,`value` FROM `active_uncomfirmed_monitors`");
if ($res->num_rows > 0)
{
	$pubnub = new Pubnub(PUBNUB_PUB, PUBNUB_SUB, "", false);
}

while ($row = $res->fetch_array()) {
    $tx = $row['tx_hash'];
    $orderid = $row['order_id'];
    $address = $row['address'];
    $value = $row['value'];

    $success = true;

    $order = new Order($orderid);
    $user = new User($order->userid);

    foreach ($order->notifications as $notification) {
        $btc = round($value / 100000000,8);

        switch ($notification->id) {
            case 1:
            //Email
                $btc = round($value / 100000000,8);
                mail($user->email, "BitPing.Net notification for ".$address." (UNCONFIRMED)", "This is a requested notification from BitPing.Net\r\n\r\nThe address : $address has received a payment of $btc BTC in tx $tx", "FROM: monitor@BitPing.Net");
                break;

            case 2:
            //HTTP
                $data = array();

                $data["to_address"] = $address;
                $data["amount"] = $value;
                $data["btc_amount"] = $btc;
                $data["confirmations"] = $confirmations;
                $data["txhash"] = $txhash;
                $data["block"] = -1;
                $data["signature"] = sha1( $address . $value . $confirmations . $txhash . $currentheight . $user->secret );
                if(filter_var($user->url, FILTER_VALIDATE_URL) !== FALSE && $user->url != "")
                {
                    $result = httpPost($user->url, $data);
                    if ($result === FALSE)
                        $success = false;
                    else
                        $success = true;
                } else {
                    mail(SYS_ADMIN, "URL in monitor bad", "Skipping http attempt for ".$user->url, "FROM: monitor@bitping.net");
                }
                break;

            case 3:
            //Pubnub
                $data = array();

                $data["to_address"] = $address;
                $data["amount"] = $value;
                $data["btc_amount"] = $btc;
                $data["confirmations"] = $confirmations;
                $data["txhash"] = $txhash;
                $data["block"] = -1;
                $data["signature"] = sha1( $address . $value . $confirmations . $txhash . $currentheight . $user->secret );

            $pubnub->publish(array(
                'channel' => sha1 ( $user->secret ),
                'message' => $data
            ));
            break;
        }
    }

    //START COMMENT HERE IF YOU ARE DEBUGGING DELIVERY PROBLEMS

    if ($success) {
        echo "Sent notification for TX:".$tx." ($address)\r\n";

        $stmt = $db->prepare("INSERT INTO notifications_sent (`order_id`, `oneshot`, `tx`, `address`, `value`) VALUE (?, 1, ?, ?, ?)");
        $stmt->bind_param("issi", $orderid, $tx, $address, $value);
        $db->update($stmt);

        $stmt = $db->prepare("DELETE FROM `active_uncomfirmed_monitors` WHERE order_id=? AND tx_hash=? LIMIT 1");
        $stmt->bind_param("is", $orderid, $tx);
        $db->update($stmt);
    } else {
        $stmt = $db->prepare("UPDATE `active_uncomfirmed_monitors` SET failures=failures+1 WHERE order_id=? AND tx_hash=? LIMIT 1");
        $stmt->bind_param("is", $orderid, $tx);
        $db->update($stmt);
    }

    //STOP COMMENT HERE
}

//Cleanup - Delete any monitors with a failure count over 10 AND a timestamp more than 1hours ago
$stmt = $db->query("DELETE FROM `active_uncomfirmed_monitors` WHERE failures > 20 AND NOW() > TIMESTAMPADD(MINUTE,5,`timestamp`) LIMIT 1");
