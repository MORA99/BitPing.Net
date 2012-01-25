<? require("system/shared.php"); ?>
<?checklogin();?>
<?
$activeuser = Users::getActiveUser();

$title=" - Event test"; require("header.php");
?>
<body>
<? topbar("api", true); ?>
    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Send a test HTTP POST event</h1> <small>A "test" tag is added the request, but other than that its valid.</small>
            </div>
            <div class="row">
                <div class="span10">
                    <?
                    if (isset($_POST["go"]))
                    {
                            $data = array();

                            $data["TEST"] = "TRUE";
                            $data["to_address"] = filter_var($_POST["address"], FILTER_SANITIZE_STRING);
                            $data["amount"] = (filter_var($_POST["value"], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 100000000);
                            $data["btc_amount"] = filter_var($_POST["value"], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                            $data["confirmations"] = filter_var($_POST["confirmations"], FILTER_SANITIZE_NUMBER_INT);
                            $data["txhash"] = filter_var($_POST["txhash"], FILTER_SANITIZE_STRING);
                            $data["block"] = filter_var($_POST["block"], FILTER_SANITIZE_STRING);
                            $url = filter_var($_POST["url"], FILTER_SANITIZE_STRING);

                            $data["signature"] = sha1( $data["to_address"] . $data["amount"] . $data["confirmations"] . $data["txhash"] . $data["block"] . $activeuser->secret );
                            if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE)
                            {
                                $result = httpPost($url, $data);
                                if ($result === FALSE)
                                {
                                    echo '
                                    <div class="alert-message error">
                                        <p>Message failed.</p>
                                    </div>
                                    ';
                                } else {
                                    echo '
                                    <div class="alert-message success">
                                        <p>Message sent.</p>
                                    </div>
                                    ';
				}
                            } else {
                                    echo '
                                    <div class="alert-message error">
                                        <p>Invalid url - '.$url.'</p>
                                    </div>
                                    ';
                            }
                    }
                    ?>



                    <form method="POST">
                        <fieldset>
                            <div class="clearfix">
                                <label>HTTP URL<br>Defaults to the one from your profile</label>
                                <div class="input">
                                    <input type="text" name="url" value="<?=$activeuser->url;?>">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Monitored address</label>
                                <div class="input">
                                    <input type="text" name="address" value="test">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Value (In BTC)</label>
                                <div class="input">
                                    <input type="text" name="value" value="1.5">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Confirmations</label>
                                <div class="input">
                                    <input type="text" name="confirmations" value="2">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Block</label>
                                <div class="input">
                                    <input type="text" name="block" value="15250">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Txhash</label>
                                <div class="input">
                                    <input type="text" name="txhash" value="testhash-tx">
                                </div>
                            </div>

                            <div class="clearfix">
                                <div class="input">
                                    <input type="submit" name="go" value="Go">
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>
<? require("system-status.php"); ?>
            </div>
<? require("footer.php"); ?>
        </div>
</body>
</html>
