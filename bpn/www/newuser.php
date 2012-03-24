<?php
require("system/shared.php");

if (isset($_POST['register'])) {
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $password = $_POST["password"];
    $passwordc = $_POST["passwordc"];
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    if (users::isUsernameAvaliable($username)) {
        if (strlen($username) >= 3) {
            if ($password == $passwordc) {
                if ($email !== FALSE && $email != "") {
                    if (users::isEmailInUse($email)==false) {
                        $user = new User();
                        $user->username = $username;
                        $user->password = $password; //gets hashed in user class
                        $user->email = $email;
                        $user->save();

                        header("Location: ./?infomsg=usercreated");
                        die();
                    } else {
                        $errmsg = "This email address is already in use, please login or use a different one.<br>If you need more than 1000addresses, please contact us instead of making multiple accounts.";
                    }
                } else {
                    $errmsg = "Invalid email address";
                }

            } else {
                $errmsg = "Passwords does not match.";
            }
        } else {
            $errmsg = "Username must be at least 3 chars long.";
        }
    } else {
        $errmsg = "This username is already taken, please select another one.";
    }
}

$title=" - Register";
require("header.php");
?>
<body>
<?php topbar("register"); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Register</h1>
            </div>
            <div class="row">
                <div class="span10">

<?php
if ($errmsg != "") {
    ?>
                    <div class="alert-message error">
                        <p><?php echo $errmsg?></p>
                    </div>
    <?php
}

                    ?>




                    <form method="POST">
                        <fieldset>

                            <div class="clearfix">
                                <label>Username</label>
                                <div class="input">
                                    <input type="text" name="username">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Password</label>
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
                                    <input type="text" name="email">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Confirm email</label>
                                <div class="input">
                                    <input type="text" name="emailc">
                                </div>
                            </div>

                            Registering for BPN is free, and allows you to monitor up to 1000 addresses.<br><br>
			    You may prefer to use <a href="./oneshot.php">adhoc notification</a> instead of registering as a user.<br>
                            This is a soft limit, its mainly here to protect the service from being overrun, if you need to monitor more than 1000, just send me an email with a estimate, and I will most likely approve it.<br>
                            <br>We may add services that require payment(sms, etc) later on<br>-But the email and HTTP POST will remain free.<br>
                            <br>We will not give/sell/rent/abuse your email address and other provided data, except as outlined in "Legal".
                            <br><br>

                            <div class="clearfix">
                                <div class="input">
                                    <input type="submit" name="register" value="Register">
                                </div>
                            </div>
                        </fieldset>
                </div>
<?php require("system-status.php"); ?>
            </div>
<?php require("footer.php"); ?>
        </div>
</body>
</html>
