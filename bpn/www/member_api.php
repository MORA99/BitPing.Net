<?
require("system/shared.php");
checklogin();
require("header.php");
$activeuser = Users::getActiveUser();
?>
<body>
    <? topbar("api", true); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>API</h1>
            </div>
            <div class="row">
                <div class="span10">
                    To get the full benefit of BPN you need to implement the HTTP POST api callback.<br><br>
                    We provide a simple example in PHP to receive the events below, please note that the secret value is unique to your account, and should be kept secret.<br>
                    If you build a module for a webshop, the secret value should be user editable.
                    <br><br>
                    When you choose to receive HTTP POST events for a order, we will contact your webserver when a payment has received the required amount of confirmations.<br>
                    The HTTP POST event will contain the following variables<br><br>

                    <table>
                        <tr><td>TEST</td><td>Included in test reports, you should check that this is not set when done testing<br>However people still need your secret to make a valid test.</td></tr>
                        <tr><td>to_address</td><td>The receiver address monitored<br>If there are more than one monitored address in an transaction, a seperate call will be made for each</td></tr>
                        <tr><td>amount</td><td>Amount in satoshi (100 million satoshi make a bitcoin)</td></tr>
                        <tr><td>btc_amount</td><td>Amount in BTC (. as decimal seperator)</td></tr>
                        <tr><td>confirmations</td><td>The number of current confirmations.<br>Note that this may be higher than requested.</td></tr>
                        <tr><td>txhash</td><td>The hash of the transaction that included the address</td></tr>
                        <tr><td>block</td><td>The block that included the transaction</td></tr>
                        <tr><td>signature</td><td>A signature generated using your secret key<br>You should validate this to make sure its not a fraud attempt.</td></tr>
                    </table>

                    <br><a href="/member_test_http.php">You can send a test event from here</a><br><br>

                    <textarea style="width: 100%" rows=20>
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
                "<?=$activeuser->secret?>" 
                );

                if ($mysig === $sig)
                {
                  //check if number of confirmations is ok
                  //update order/send user notification                
                } else {
                  //log all post data, send warning email to administrator                
                }
                    </textarea>

                </div>
                <? require("system-status.php"); ?>
            </div>
            <? require("footer.php"); ?>
        </div>
</body>
</html>
