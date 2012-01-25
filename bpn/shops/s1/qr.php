<?
require("phpqrcode/phpqrcode.php");

QRcode::png($_GET["text"]);
