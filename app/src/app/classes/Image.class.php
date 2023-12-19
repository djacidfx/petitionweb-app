<?php
class Image {
    // Database Module
    public const DB_TABLE = DB::TBL_IMGS;
    public static function _module_($data, $time) {
        return [
            'c' => ['data', 'creation_time'],
            'v' => [$data, $time]
        ];
    }
    public static function Create($data) {
        $m = self::_module_($data, time());
        return DB::Create(self::DB_TABLE, $m['c'], $m['v']);
    }
    public static function Read($id) {
        return DB::Read(self::DB_TABLE, ['id'], [$id]);
    }
    public static function Delete($id) {
        return DB::Delete(self::DB_TABLE, ['id'], [$id]);
    }
    // Misc
    
}
?>