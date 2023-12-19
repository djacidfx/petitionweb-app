<?php
class AdministrationAuthentication {
    public function GenerateRandom(int $length) {
        return bin2hex(random_bytes($length));
    }
    public function GenerateToken(int $id, string $user, string $random) {
        return hash_hmac('ripemd320', json_encode(func_get_args()), $random, false);
    }
    public function ValidateAuthentication(string $token, int $id, string $user, string $random) {
        return $this->GenerateToken($id, $user, $random) === $token;
    }
}

function redirect_to_auth_page() {
    $page_name = explode("/", $_REQUEST['route']);
    $page_name = reset($page_name);
    if($page_name !== 'auth') {
        header('location: '.admin_path_join('/auth'));
        exit;
    }
}

function check_logout() {
    $page_name = explode("/", $_REQUEST['route']);
    $page_name = reset($page_name);
    if($page_name === 'logout') {
        SystemAdmins::Logout();
    }
}

check_logout();

if(!SystemAdmins::IsLoggedIn()) {
    if(
        $_POST && isset($_POST['_method']) && $_POST['_method'] === 'POST' && isset($_POST['_action']) && $_POST['_action'] === 'AUTH'
        && isset($_POST['user']) && $_POST['user'] && isset($_POST['password']) && $_POST['password']
    ) {
        $user = protectInput(sanitize_string($_POST['user']));
        $password = SystemAdmins::HashPassword($_POST['password']);
        $admin = SystemAdmins::Read(['user', 'password'], [$user, $password]);
        if($admin) {
            if($admin['id'] && $admin['user'] && $admin['password']) {
                if($admin['password'] === $password) {
                    $auth = new AdministrationAuthentication();
                    $random = $auth->GenerateRandom(32);
                    $token = $auth->GenerateToken($admin['id'], $admin['user'], $random);
                    $SID = session_create_id('po-');
                    session_commit();
                    session_id($SID);
                    session_start();
                    $_SESSION['id'] = $admin['id'];
                    $_SESSION['user'] = $admin['user'];
                    $_SESSION['token'] = $token;
                    $_SESSION['random'] = $random;
                    header('location: '.admin_path_join('/dashboard'));
                    exit;
                }
            }
        }
        $auth_response = '<p class="alert alert-danger">{{lang.admin.admins.incorrect_data}}</p>';
    } else {
        redirect_to_auth_page();
    }
}
?>
