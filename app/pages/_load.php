<?php
function extract_requested_params($requested_page_name) {
    $page = $requested_page_name;
    $requested_params = explode("?", $page);
    if(is_array_key_exist(1, $requested_params)) {
        $requested_params = explode("&", $requested_params[1]);
        foreach($requested_params as $param) {
            $param_array = explode("=", $param);
            $key = $param_array[0];
            $val = $param_array[1];
            $_REQUEST[$key] = $val;
        }
    }
}

function page_header() {

}
function page_footer() {
    echo <<<EOT
    <script>
    </script>
EOT;
}

if($_REQUEST && isset($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
    if($page) {
        require_once __DIR__.'/../src/config.php';
        $page_file = __DIR__.'/'.explode("?", $page)[0];
        $lang = load_requested_lang();
        extract_requested_params($page);
        echo "<div id='_load-page'>";
            echo "<div id='_load-page--header'>";
                page_header();
            echo "</div>";
            echo "<div id='_load-page--content'>";
                if($page_file) require_once $page_file;
            echo "</div>";
            echo "<div id='_load-page--footer'>";
                page_footer();
            echo "</div>";
        echo "</div>";
    }
}
?>