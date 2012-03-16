<?php require("system/shared.php"); ?>
<?php $title=" - OneShot adhoc notification"; require("header.php"); ?>
<body>
    <? topbar("home"); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>OneShot "API"</h1>
            </div>
            <div class="row">
                <div class="span10">
		There is a adhoc notification system on BPN aswell as the regular one, this may be preferably if you

		<ul>
			<li>Require a limited amount of notifications.</li>
			<li>Do not wish to list all your addresses at once.</li>
			<li>Generate the addresses on the fly (DANGEROUS).</li>
		</ul>

		To request a oneshot notification, you need to call http(s)://bitping.net/c/oneshot.php<br>
		You can call it using GET or POST, and need to include these 3-4arguments.<br>
		I recommend you use POST and HTTPS to keep the key secret, but its really up to you.<br>
		If you need a notification for a small amount, its probaly not worth the effort for someone to intercept your GET call.<br>
		On the other hand, if you are expecting a few BTC for digital delivered goods, you need to use POST over HTTPS with a key.<br>
		<br>
		If you need a notification for more than one address you can call the address multiple times.<br>
		Please rate limit your requests to 1per second, and 1000 per 24hours.<br>
		If you need more than this, you can either run the system locally, or pay me to handle the server load, and I will white list your ip(s) :)<br><br>

                <table>
	                <tr><td>address</td><td>The receiver address monitored</td></tr>
	                <tr><td>url</td><td>URL to be called when a payment is received, http or https</td></tr>
	                <tr><td>confirmations</td><td>The number of confirmations needed, 1-6</td></tr>
	                <tr><td>key</td><td>OPTIONAL: A key that will be used to sign the signature upon calling your URL</td></tr>
                </table>


		When a payment is recieved, the url is called just as with a regular notification.<br>
                When you choose to receive HTTP POST events for a order, we will contact your webserver when a payment has received the required amount of confirmations.<br>
                The HTTP POST event will contain the following variables<br><br>

                <table>
	                <tr><td>to_address</td><td>The receiver address monitored</td></tr>
	                <tr><td>amount</td><td>Amount in satoshi (100 million satoshi make a bitcoin)</td></tr>
	                <tr><td>btc_amount</td><td>Amount in BTC (. as decimal seperator)</td></tr>
	                <tr><td>confirmations</td><td>The number of current confirmations.<br>Note that this may be higher than requested.</td></tr>
	                <tr><td>txhash</td><td>The hash of the transaction that included the address</td></tr>
	                <tr><td>block</td><td>The block that included the transaction</td></tr>
	                <tr><td>signature</td><td>A signature generated using your key (if provided)<br>You should validate this to make sure its not a fraud attempt.<br><br>The signature is generated as follows<br>sha1(to_address . $amount . $confirmations . $txhash . $block . $key)</td></tr>
                </table>

                </div>
                <?php require("system-status.php"); ?>
            </div>
            <?php require("footer.php"); ?>
        </div>
</body>
</html>
