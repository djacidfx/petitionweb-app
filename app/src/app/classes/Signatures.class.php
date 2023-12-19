<?php
class Signatures {
    // Database Module
    public const DB_TABLE = DB::TBL_SIGNS;
    public static function _module_($petition_id, $signee, $time) {
        if(is_array($signee)) $signee = json_encode($signee);
        return [
            'c' => ['petition_id', 'signee', 'verified', 'creation_time'],
            'v' => [$petition_id, $signee, 0, $time]
        ];
    }
    public static function Create($petition_id, $signee) {
        $m = self::_module_($petition_id, $signee, time());
        return DB::Create(self::DB_TABLE, $m['c'], $m['v']);
    }
    public static function Read($key, $val) {
        return DB::Read(self::DB_TABLE, $key, $val);
    }
    public static function ReadAll($key, $val) {
        return DB::ReadAll(self::DB_TABLE, $key, $val);
    }
    public static function VerifySignature($signature_id) {
        return DB::Update(self::DB_TABLE, 'verified', 1, 'id', $signature_id);
    }
}
?>