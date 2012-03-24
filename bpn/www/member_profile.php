<?php require("system/shared.php"); ?>
<?php checklogin();?>
<?php 
$activeuser = Users::getActiveUser();

if (isset($_POST["delete"])) {
    $activeuser->delete();
    header("Location: ./logout.php");
    die();
}

$title=" - Profile"; require("header.php");

if (isset($_POST["save"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $url = filter_var($_POST["url"], FILTER_SANITIZE_URL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE)
        $activeuser->email = $email;
    if ($url == "" || filter_var($url, FILTER_VALIDATE_URL) !== FALSE)
        $activeuser->url = $url;

    if (isset($_POST["password"]) && $_POST["password"] == $_POST["passwordc"] && $_POST["password"] != "") {
        $activeuser->password = $_POST["password"];
    }
    $activeuser->save();
}
?>
<body>
<?php topbar("profile", true); ?>
    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Profile</h1>
            </div>
            <div class="row">
                <div class="span10">
                    <form method="POST">
                        <fieldset>
                            <legend>User details</legend>
                            <div class="clearfix">
                                <label>Username</label>
                                <div class="input">
                                    <input type="text" disabled value="<?php echo$activeuser->username;?>">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>New password</label>
                                <div class="input">
                                    <input type="password" name="password">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Confirm password</label>
                                <div class="input">
                                    <input type="password" name="passwordc">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Email</label>
                                <div class="input">
                                    <input type="text" name="email" value="<?php echo$activeuser->email;?>">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>POST URL</label>
                                <div class="input">
                                    <input type="text" name="url" value="<?php echo$activeuser->url;?>"><br>(ie. http://www.mydomain.tld/script.php)
                                </div>
                            </div>

                            <script LANGUAGE="JavaScript">
                                <!--
                                // Nannette Thacker http://www.shiningstar.net
                                function confirmSubmit()
                                {
                                    var agree=confirm("Are you sure you wish to delete your profile?");
                                    if (agree)
                                        return true ;
                                    else
                                        return false ;
                                }
                                // -->
                            </script>

                            <div class="clearfix">
                                <div class="input">
                                    <input type="submit" name="save" value="Save">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete" value="Delete profile" onClick="return confirmSubmit();">
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>
<?php require("system-status.php"); ?>
            </div>
<?php require("footer.php"); ?>
        </div>
</body>
</html>
