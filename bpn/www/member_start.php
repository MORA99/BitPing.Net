<? require("system/shared.php"); ?>
<?checklogin();?>
<? $title=" - Member area"; require("header.php"); ?>
<body>
    <? topbar("start", true); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Introduction <small>to using BPN in a webshop</small></h1>
            </div>
            <div class="row">
                <div class="span10">
                    BitPing.Net (BPN) is a service that enables merchants to accept bitcoins with a minimum of effort.<br>
	    Users add a list of public addresses to be monitored, and BPN sends a notification when a payment is received.<br>
	    Notifications can currently be made using Email, HTTP POST and Pubnub<br>
                    <br>
	    To use BPN in a webshop, you need to generate a "large" amount of addresses.<br>
	    Then upload these addresses to BPN, and select the number of confirmations you require (1-6)<br>
	    When a customer selects to pay using bitcoin, you select a bitcoin address and assign it to the order.<br>
 	    If the user pays, BPN will send a confirmation email/HTTP POST(once it has been confirmed as many times as requested), which you can use to mark the order as payed.<br>
                    <br>
	    You can use Vanitygen to generate the addresses, then insert them into your database, and export the addresses to BPN (One address per line, no seperators).<br>
	    The code for the monitor is opensource, so you can run your own server instead of using BPN if you like.	    
                </div>
                <? require("system-status.php"); ?>
            </div>
            <? require("footer.php"); ?>
        </div>
</body>
</html>
