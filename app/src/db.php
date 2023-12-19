<?php
$myPDO = new myPDO($db_host, $db_user, $db_pass, $db_name);
function mypdo() {
    global $myPDO;
    return $myPDO;
}
?>