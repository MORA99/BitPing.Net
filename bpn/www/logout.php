<?
require("system/shared.php");

$_SESSION["AUTH_USER_NAME"] = null;
$_SESSION = array();
session_regenerate_id();
session_destroy();

header("Location: /");  
?>