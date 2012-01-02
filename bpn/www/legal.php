<? require("system/shared.php"); ?>
<? require("header.php"); ?>
<body>
    <? topbar("legal"); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Legal</h1>
            </div>
            <div class="row">
                <div class="span10">
		First of all BPN cannot know what the payments monitored are for, nor do we request that our users tell us, it could be napkins or something worse.<br>
                    <br>
		We cannot identify who sent or received a payment, other than what is publicly known in the bitcoin blockchain.<br>
                    <br>
		Requesting a monitor on BPN does not prove that the address is acutally owned by this individual.<br>
		As soon as an address have received 1 payment, it is publicly known in the block chain.<br>
                    <br>
		We log which IP signs in to which account for general security measures, as well as which pages are seen by which IP.<br>
		We make no attempt to prove if this is the actual IP of the user, or a proxy/tor/vpn/etc.<br>
                    <br>
		Given a court order we will release all known information about an monitor account, including, but not limited to, username, hashed password, monitored addresses, HTTP POST targets.
                    <br><br>
		Before contacting us, please consider than anyone who wants to monitor addresses, that may be sensitive could easily download the project and run it themselves, and thereby publish nothing more than they are downloading the blockchain.
                    <br><br>

                    <h1>License</h1>
                    BitPing.Net will be released into the public domain within a few weeks, the monitor part is already on github.
                </div>
                <? require("system-status.php"); ?>
            </div>
            <? require("footer.php"); ?>
        </div>
</body>
</html>
