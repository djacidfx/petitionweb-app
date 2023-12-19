<?php
if(!isset($_REQUEST['route']) || !$_REQUEST['route']) {
    header("location: dashboard");
    exit;
}
?>
