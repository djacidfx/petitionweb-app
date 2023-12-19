<?php
function Lang_showResponseAlert($status='info', $langs=['en'=>''], $el='h5') {
    $h = "<$el class='alert alert-$status'>";
    foreach($langs as $lang => $label) {
        $h .= "<span class='for-lang--$lang'>$label</span>";
    }
    $h .= "</$el>";
    return $h;
}
function Lang_showLangLabels($langs, $callback, $el='div') {
    $h = "";
    foreach($langs as $lang => $label) {
        $h .= "<$el class='for-lang--$lang'>".$callback($lang, $label)."</$el>";
    }
    return $h;
}
?>