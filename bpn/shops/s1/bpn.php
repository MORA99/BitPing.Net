<?
require("shared.php");

$db = Database::getInstance();
$secret = "DEMO";

              $address       = $_POST["to_address"];
              $amount        = $_POST["amount"];
              $btc           = $_POST["btc_amount"];
              $confirmations = $_POST["confirmations"];
              $txhash        = $_POST["txhash"];
              $block         = $_POST["block"];
              $sig           = $_POST["signature"];
              $mysig = sha1(
                $address .
                $amount .
                $confirmations .
                $txhash .
                $block .
                $secret
                );

                if ($mysig === $sig)
                {
			$stmt = $db->prepare("REPLACE INTO `s1_payment` (`address`, `value`, `confirmations`, `last_update`) VALUES (?, ?, ?, NOW())");
			$stmt->bind_param('sii', $address, $value, $confirmations);
			$db->insert($stmt);
                } else {
                  //log all post data, send warning email to administrator
                }
