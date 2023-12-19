<?php
class SystemAdmins {
    // Database Module
    public const DB_TABLE = DB::TBL_ADMINS;
    public static function Create($user, $password) {
        return DB::Create(self::DB_TABLE, ['user', 'password', 'creation_time'], [$user, self::HashPassword($password), time()]);
    }
    public static function Read($key, $val) {
        return DB::Read(self::DB_TABLE, $key, $val);
    }
    public static function ReadAll($key, $val) {
        return DB::ReadAll(self::DB_TABLE, $key, $val);
    }
    public static function Update($id, $user, $password=null) {
        $c = ['user'];
        $v = [$user];
        if($password) {
            $c[] = 'password';
            $v[] = self::HashPassword($password);
        }
        return DB::Update(self::DB_TABLE, $c, $v, ['id'], [$id]);
    }
    public static function Delete($id) {
        return DB::Delete(self::DB_TABLE, ['id'], [$id]);
    }
    // Core
    public const PASSWORD_SALT = '4dT@k&%3pkhx2eZdls2HgVdVmb2_';
    public static function HashPassword($password) {
        return hash_hmac('whirlpool', $password, self::PASSWORD_SALT);
    }
    public static function IsLoggedIn() {
        if(self::IsSessionSet()) {
            $auth = new AdministrationAuthentication();
            return $auth->ValidateAuthentication($_SESSION['token'], $_SESSION['id'], $_SESSION['user'], $_SESSION['random']);
        }
    }
    public static function Logout() {
        session_destroy();
        session_unset();
        header('location: '.admin_path_join('/auth'));
        exit;
    }
    public static function IsSessionSet() {
        return (
            isset($_SESSION['id']) && isset($_SESSION['user']) && isset($_SESSION['token']) && isset($_SESSION['random'])
            && $_SESSION['id'] && $_SESSION['user'] && $_SESSION['token'] && $_SESSION['random']
        );
    }
}
?>
