<?php
// route
function path_join($routes) {
    return rtrim(PATH,'/').(is_array($routes) ? '/'.implode('/', $routes) : $routes);
}
// lang
function load_lang($lang) {
    if(!$lang) $lang = 'en';
    return json_decode(file_get_contents(__DIR__.'/../lang/'.$lang.'.json'));
}
function load_requested_lang() {
    $lang = (isset($_REQUEST['lang']) && $_REQUEST['lang'] ? $_REQUEST['lang'] : null);
    return load_lang($lang);
}
// html
function js_onclick_load_page($name) {
    return ' onclick="load_page_content(\''.$name.'\')" ';
}
function js_onclick_load_page_args($name, $args=['code'=>'xxx']) {
    return ' onclick="load_page_content_with_args(\''.$name.'\', '.str_replace('"',"'",json_encode($args)).')" ';
}
function showResponseAlert($status='info', $str="", $el='h5') {
    return "<$el class='alert alert-$status'>$str</$el>";
}
// core
function protectInput($input) {
    if(!is_array($input) && !is_object($input)) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input);
    }
    return $input;
}
function is_array_key_exist($key, $array) {
    return (isset($array[$key]) || array_key_exists($key, $array));
}
function string_contains($string, $query) {
    return strpos($string, $query) !== false;
}
function sanitize_string($string) {
    return filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
}
function random_sstr($length) {
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for($i = 0; $i < $length; ++$i) {
        $pieces[] = $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}
function items_in_array($items, $array) {
    return count(array_intersect($items, $array)) === count($items);
}
function check_base64_image($base64, $ext) {
    $mime = 'image/jpeg';
    if($ext === 'png') $mime = 'image/png';
    $base64 = str_replace("data:$mime;base64,", "", $base64);

    $img = @imagecreatefromstring(base64_decode($base64));
    if(!$img) return false;

    $tmp_img_name = 'tmp.'.$ext;
    if($ext === 'jpg') $ext = 'jpeg';

    if($ext === 'png') imagepng($img, $tmp_img_name);
    else imagejpeg($img, $tmp_img_name);

    $info = getimagesize($tmp_img_name);
    if(file_exists($tmp_img_name)) unlink($tmp_img_name);

    if($info[0] > 0 && $info[1] > 0 && $info['mime'] && preg_match(IMAGE_MIME_REGEX, $info['mime'])) {
        return true;
    }
    return false;
}
function extract_image_type_from_base64($data) {
    return explode("/", str_replace("data:", "", explode(";", $data)[0]))[1];
}
function sendEmail($from, $to, $subject, $body) {
    $full_body = str_replace("\n.", "\n..", $body);
    $full_body = wordwrap($full_body, 70, "\r\n");
    $full_body = "
    <html>
    <head>
        <title>$subject</title>
    </head>
    <body>
        <div>
            $full_body
        </div>
    </body>
    </html>
    ";
    if(!$from) $from = 'webmaster@none.no';
    $headers = [
        'From' => $from,
        'X-Mailer' => 'PHP/'.phpversion(),
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=utf-8'
    ];
    return [mail($to, $subject, $full_body, $headers), error_get_last()['message']];
}
function convertCountryNameToFontIconName($country) {
    return str_replace(" ", "-", strtolower($country));
}
// admin
function admin_path_join($routes) {
    return rtrim(PATH,'/').'/'.ADMIN_DIR_NAME.(is_array($routes) ? '/'.implode('/', $routes) : $routes);
}
function admin_path_back() {
    $a = explode("/", $_SERVER['REQUEST_URI']);
    $r = $a[count($a)-2];
    if($r != ADMIN_DIR_NAME) return admin_path_join('/'.$r);
    return '';
}
function check_admin_route_path($path) {
    $paths = explode('/', $_REQUEST['route']);
    return end($paths) === $path;
}
function timestamp2Date($timestamp) {
    return date("Y-m-d H:i:s", intval($timestamp));
}
?>
