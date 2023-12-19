<?php
function api_request_body() {
    return json_decode(file_get_contents('php://input'), true);
}
function api_headers() {
    error_reporting(E_ALL & ~E_NOTICE);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Access-Control-Allow-Headers, X-Auth-Token, Authorization, content-type, x-xsrf-token, X-Requested-With');
    header("Content-type: application/json; charset=utf-8");
}
function return_api($success=true,$msg='',$data=[],$code=200,$exit=true) {
    http_response_code($code);
    if(!is_array($data)) $data = [$data];
    $r = [
        'data' => $data,
        'success' => $success,
        'message' => $msg,
        'code' => $code
    ];
    $e = json_encode($r, JSON_PRETTY_PRINT);
    if($exit) die($e);
    return $r;
}

if($_REQUEST && isset($_REQUEST['path'])) {
    $path = $_REQUEST['path'];
    if($path) {
        $path_file = __DIR__.'/'.$path.'.php';
        require_once __DIR__.'/../../src/config.php';
        api_headers();
        $body = api_request_body();
        $lang = load_requested_lang();
        if($path_file) require_once $path_file;
    }
}
?>