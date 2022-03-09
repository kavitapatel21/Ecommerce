<?php
require_once("wp-config.php");
require_once("wp-load.php");

$msg = "First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
$var=wp_mail("kavita.patel@plutustec.com","My subject",$msg);

if($var)
{
    echo "true";
}
else{
    echo "false";
}


?>