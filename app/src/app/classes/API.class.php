<?php
class API {
    public $body;
    public $exit = true;
    public $lang = null;
    public function __construct($body, $exit, $lang=null) {
        $this->body = $body;
        $this->exit = $exit;
        if($lang) $this->lang = $lang;
        else $this->lang = load_requested_lang();
    }
    public function Response($success=true, $code=200, $msg='', $data=[]) {
        return return_api($success, $msg, $data, $code, $this->exit);
    }
}
?>