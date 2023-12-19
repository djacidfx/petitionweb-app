<?php
$code = (isset($_REQUEST['code']) && $_REQUEST['code'] ? $_REQUEST['code'] : null);
if($code) {
    echo '<div class="container" id="single-petition-page">';
    Petition::ShowSingleFull(Petition::Read('code', $code));
    echo '</div>';
}
?>