<?php
class DB {
    public const TBL_IMGS = "images";
    public const TBL_PETS = "petitions";
    public const TBL_SIGNS = "signatures";
    public const TBL_ADMINS = "system_admins";
    public static function Create($tbl, $c, $v) {
        $db = mypdo();
        $db->setTable($tbl);
        return [$db->insert($c, $v), $db->lastId()];
    }
    public static function Update($tbl, $c, $v, $wc, $wv) {
        $db = mypdo();
        $db->setTable($tbl);
        return $db->update($c, $v, $wc, $wv);
    }
    public static function ReadAll($tbl, $c=null, $v=null) {
        $db = mypdo();
        $db->setTable($tbl);
        return $db->fetchAll($c, $v, '*');
    }
    public static function Read($tbl, $c, $v) {
        $db = mypdo();
        $db->setTable($tbl);
        return $db->fetchOne($c, $v, '*');
    }
    public static function Delete($tbl, $c, $v) {
        $db = mypdo();
        $db->setTable($tbl);
        return $db->delete($c, $v);
    }
    public static function Count($tbl, $c=null, $v=null) {
        $db = mypdo();
        $db->setTable($tbl);
        return $db->countAll($c, $v);
    }
    public static function ExecuteQuery($query, $values=[], $bind_param_type=PDO::PARAM_STR) {
        $db = mypdo();
        $stmt = $db->prepare($query);
        if(!is_array($values)) $values = [$values];
        foreach($values as $i => $value) {
            $stmt->bindValue($i+1, $value, $bind_param_type);
        }
        $stmt->execute();
        return $stmt;
    }
}
?>
