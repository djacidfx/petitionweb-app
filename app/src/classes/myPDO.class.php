<?php
/**
 * myPDO.class.php
 * myPDO for php PDO. simple & easy usage. usage example in the test file.
 * @author: Primo <primo-universe@protonmail.com> (https://primo-businesses.blogspot.com/).
 * @license: This is a closed-source file. All Rights Reserved.
 * @copyright: Copyright (C) 2021. Unauthorized copying of this file is strictly prohibited. Proprietary and confidential.
 */
class myPDO {

    protected $db_host = null;
    protected $db_user = null;
    protected $db_pass = null;
    protected $db_name = null;
    protected $table = null;
    public $pdo = null;
    public $manual_error = null;
    # ------------------------------------------------------
    public function __construct(string $host, string $user, string $pass, string $name) {
        $this->db_host = $host;
        $this->db_user = $user;
        $this->db_pass = $pass;
        $this->db_name = $name;
        $this->manual_error = null;
        $connect_try = $this->connect_try();
        if(!$connect_try[0]) die('[myPDO] Database Connection Error: '.$connect_try[1]);
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
    }
    public function connect() {
        $this->pdo = new PDO('mysql:host='.$this->db_host.'; dbname='.$this->db_name.'; charset=utf8mb4', $this->db_user, $this->db_pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
    public function connect_try():array {
        try {
            $this->connect();
            return [true, null];
        } catch(PDOException $e) {
            return [false, $e->getMessage()];
        }
    }
    # ------------------------------------------------------
    # handling
    protected function handleColumns($c) {
        if($c) {
            $cols = "";
            if(is_array($c)) {
                for($i = 0; $i < count($c); $i++) {
                    $cols .= $c[$i]." = ?";
                    if($i != count($c)-1) $cols .= ", ";
                    else $cols .= "";
                }
            } else {
                $cols = $c." = ?";
            }
            return $cols;
        }
        return $c;
    }
    protected function handleSelectColumns($c) {
        if($c) {
            if($c != '*') {
                $cols = "";
                if(is_array($c)) {
                    $cols = implode(', ', $c);
                } else {
                    $cols = $c;
                }
                return $cols;
            }
            return $c;
        }
        return $c;
    }
    protected function handleValues($v) {
        if($v) {
            if(!is_array($v)) return [$v];
        }
        return $v;
    }
    protected function handleWhereColumns($c) {
        if($c) {
            if(!is_array($c)) $cols = [$c];
            else $cols = $c;
            $sc = str_replace(", ", " AND ", $this->handleColumns($cols));
            $w = " WHERE ".$sc;
        } else $w = "";
        return $w;
    }
    protected function handleSearchColumns($c) {
        if($c) {
            $cols = "";
            if(is_array($c)) {
                for($i = 0; $i < count($c); $i++) {
                    $cols .= $c[$i]." LIKE ?";
                    if($i != count($c)-1) $cols .= " OR ";
                    else $cols .= "";
                }
            } else {
                $cols = $c." LIKE ?";
            }
            return " WHERE ".$cols;
        }
        return $c;
    }
    // ready handling
    protected function handleInsertColumnsAndValues($c, $v) {
        $cols = $c;
        $vals = $v;
        $c_a = (is_array($c) ? $c : [$c]);
        if(is_array($c)) $cols = implode(', ', $c);
        if(!is_array($v)) $vals = [$v];
        $qp = "";
        for($i = 0; $i < count($c_a); $i++) {
            $qp .= "?";
            if($i != count($c_a)-1) $qp .= ", ";
        }
        return [$cols, $vals, $qp];
    }
    protected function handleUpdateColumnsAndValues($c, $v, $wc, $wv) {
        $cols = $c;
        $vals = $v;
        if(!is_array($c)) $cols = [$c];
        if(!is_array($v)) $vals = [$v];
        $cols_and_qp = $this->handleColumns($cols);
        $w = $this->handleWhereColumns($wc);
        $wv = $this->handleValues($wv);
        if(is_array($vals) && is_array($wv)) $vals = array_merge($vals, $wv);
        return [$cols, $vals, $w, $wv, $cols_and_qp];
    }
    protected function handleDeleteColumnsAndValues($c, $v) {
        $w = $this->handleWhereColumns($c);
        $wv = $this->handleValues($v);
        return [$w, $wv];
    }
    # ------------------------------------------------------
    # public set
    public function setTable($table) {
        $this->table = $table;
    }
    public function setError($error) {
        $this->manual_error = $error;
    }
    # ------------------------------------------------------
    # main processing
    public function mainSelect($cols, $vals, $x_type = 1, $scols = '*', $extra = null) {
        $t_table = $this->table;
        $t_cols = $this->handleWhereColumns($cols);
        $t_vals = $this->handleValues($vals);
        $t_scols = $this->handleSelectColumns($scols);
        if($x_type != 0) $x_type = $x_type >= 1 ? $x_type : 1;
        $l_type = null;
        if($x_type != 0) $l_type = "LIMIT ".$x_type;
        # "SELECT ".$t_scols." FROM ".$t_table.($t_cols && $t_vals ? $t_cols : "")
        $sql = $this->ready_query_string_select($scols, ($t_cols && $t_vals ? $cols : null)).($extra ? " ".$extra : "").($l_type ? " ".$l_type : "");
        $stmt = $this->pdo->prepare($sql);
        if($t_vals) for($i = 0; $i < count($t_vals); $i++) $stmt->bindValue($i+1, $t_vals[$i], PDO::PARAM_STR);
        $stmt->execute();
        if($x_type != 0) $row = $stmt->fetch(PDO::FETCH_ASSOC);
        else $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        if($row !== false) return $row;
        else return false;
    }
    public function mainCount($wc, $wv):int {
        if($wc && $wv) {
            if(!is_array($wc)) $wc = [$wc];
            if(!is_array($wv)) $wv = [$wv];
            $c = $wc[0];
            $wc = $this->handleWhereColumns($wc);
        } else {
            $wc = "";
            $c = "*";
        }
        $stmt = $this->pdo->prepare("SELECT count($c) FROM ".$this->table.$wc);
        $stmt->execute($wv);
        $res = (int)$stmt->fetchColumn();
        $stmt = null;
        return $res;
    }
    public function mainMath($wc, $wv, $m = 'sum', $e = null) {
        if(!$m) return;
        $wcx = $wc;
        if($wc && $wv) {
            if(!is_array($wc)) $wc = [$wc];
            if(!is_array($wv)) $wv = [$wv];
            $c = $wc[0];
            $wc = $this->handleWhereColumns($wc);
        } else {
            $wc = "";
            $c = "*";
        }
        $mx = strtoupper($m).'('.$wcx.')';
        $stmt = $this->pdo->prepare("SELECT ".$mx." FROM ".$this->table.$wc.($e ? ' '.$e : ''));
        $stmt->execute($wv);
        $res = $stmt->fetchColumn();
        $stmt = null;
        return $res;
    }
    public function mainSearch($cols, $vals, $x_type = 1, $scols = '*', $extra = null) {
        $t_table = $this->table;
        $t_cols = $this->handleSearchColumns($cols);
        $t_vals = $this->handleValues($vals);
        $t_scols = $this->handleSelectColumns($scols);
        if($x_type != 0) $x_type = $x_type >= 1 ? $x_type : 1;
        $l_type = null;
        if($x_type != 0) $l_type = "LIMIT ".$x_type;
        $sql = "SELECT ".$t_scols." FROM ".$t_table.($t_cols && $t_vals ? $t_cols : "").($extra ? " ".$extra : "").($l_type ? " ".$l_type : "");
        $stmt = $this->pdo->prepare($sql);
        if($t_vals) for($i = 0; $i < count($t_vals); $i++) $stmt->bindValue($i+1, '%'.$t_vals[$i].'%', PDO::PARAM_STR);
        $stmt->execute();
        if($x_type != 0) $row = $stmt->fetch(PDO::FETCH_ASSOC);
        else $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        if($row !== false) return $row;
        else return false;
    }
    # ------------------------------------------------------
    # public get
    public function getError() {
        return $this->manual_error;
    }
    public function lastId():string {
        return strval($this->pdo->lastInsertId());
    }
    public function error():string {
        return implode(":", $this->pdo->errorInfo());
    }
    public function getLastId($f = 'id'):int {
        $allData = $this->fetchAll(null, null, $f);
        $maxId = -1;
        if($allData) {
            $idss = [];
            foreach($allData as $k => $v) {
                $idss[] = intval($v[$f]);
            }
            $maxId = intval(max($idss));
        }
        return intval($maxId);
    }
    public function getLastData($f = 'id', $s = '*') {
        $lastId = $this->getLastId($f);
        if($lastId !== -1) {
            return $this->fetchOne($f, $lastId, $s);
        }
        return $lastId;
    }
    public function getNextId($f = 'id'):int {
        $lastId = $this->getLastId($f);
        $nextId = ($lastId === -1 ? 1 : $lastId+1);
        return intval($nextId);
    }
    # ------------------------------------------------------
    # maths
    public function countAll($c = null, $v = null):int {
        return $this->mainCount($c, $v);
    }
    public function countOne($wc, $wv):int {
        return $this->mainCount($wc, $wv);
    }
    public function exists($wc, $wv):bool {
        return $this->mainCount($wc, $wv) > 0;
    }
    public function sum($wc = null, $wv = null, $e = null) {
        return $this->mainMath($wc, $wv, 'sum', $e);
    }
    public static function getL2Keys($array) {
        $result = array();
        foreach($array as $sub) $result = array_merge($result, $sub);
        return array_keys($result);
    }
    # ------------------------------------------------------
    # selections
    public function selectOne($c, $v, $s = '*', $l, $e = null) {
        return $this->mainSelect($c, $v, $l, $s, $e);
    }
    public function selectAll($c = null, $v = null, $s = '*', $e = null) {
        return $this->mainSelect($c, $v, 0, $s, $e);
    }
    public function fetchOne($c, $v, $s = '*') {
        return $this->selectOne($c, $v, $s, 1, ($c ? "ORDER BY ".(is_array($c) ? $c[0] : $c)." DESC" : null));
    }
    public function fetchAll($c = null, $v = null, $s = '*') {
        return $this->selectAll($c, $v, $s, ($c ? "ORDER BY ".(is_array($c) ? $c[0] : $c)." DESC" : null));
    }
    # ------------------------------------------------------
    # search
    public function search($c = null, $v = null, $s = '*', $e = null) {
        return $this->mainSearch($c, $v, 0, $s, $e);
    }
    public function searchAll($keyword, $array, $s = '*', $e = null) {
        $keys = myPDO::getL2Keys($array);
        $aquery = "";
        foreach($keys as $key) $aquery .= $key." LIKE ? OR ";
        $aquery = substr($aquery, 0, -4);
        $sql = "SELECT $s FROM $this->table WHERE ".$aquery.($e ? " ".$e : '');
        $q = $this->pdo->prepare($sql);
        foreach($keys as $index => $key) $q->bindValue(($index+1), '%'.$keyword.'%');
        $q->execute();
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function filterAll($keys, $values, $equal_operator = 'LIKE', $and_or_or = 'AND', $s = '*', $e = null) {
        $aquery = "";
        if(!$keys || !$values) return false;
        $and_or_or = strtoupper($and_or_or);
        if($and_or_or !== 'OR' && $and_or_or !== 'AND') $and_or_or = 'AND';
        $equal_operator = strtoupper($equal_operator);
        if($equal_operator !== 'LIKE' && $equal_operator !== '=') $equal_operator = '=';
        foreach($keys as $key) $aquery .= $key." $equal_operator ? $and_or_or ";
        $aquery = substr($aquery, 0, -4);
        $sql = "SELECT $s FROM $this->table WHERE ".$aquery.($e ? " ".$e : '');
        $q = $this->pdo->prepare($sql);
        foreach($values as $index => $value) $q->bindValue(($index+1), ($equal_operator==='LIKE' ? '%'.$value.'%' : $value));
        $q->execute();
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function searchAndFilterAll($array, $keys, $values, $keyword, $equal_operator = 'LIKE', $and_or_or = 'AND', $s = '*', $e = null) {
        $search_all = $this->searchAll($keyword, $array, $s, $e);
        $filter_all = $this->filterAll($keys, $values, $equal_operator, $and_or_or, $s, $e);
        $a = array_merge((is_array($search_all) ? $search_all : []), (is_array($filter_all) ? $filter_all : []));
        return $a;
    }
    # ------------------------------------------------------
    # statement
    public function prepare($s) {
        return $this->pdo->prepare($s);
    }
    public function query($q) {
        return $this->pdo->query($q);
    }
    public function exec($e) {
        return $this->pdo->exec($e);
    }
    public function transaction($qs, $vs = null) {
        try {
            $this->pdo->beginTransaction();
            $stmts = [];
            for($i = 0; $i < count($qs); $i++) {
                $stmts[] = $this->pdo->prepare($qs[$i]);
                if(!$vs) $vs[$i] = "";
                if(!is_array($vs[$i])) $vs[$i] = [$vs[$i]];
                if(!$stmts[$i]->execute($vs[$i])) throw new Exception('Statement #'.($i+1).' Failed - Error Info: '.implode(':',$this->pdo->errorInfo()));
            }
            $stmts = [];
            $this->pdo->commit();
            return true;
        } catch(Exception $e) {
            $this->pdo->rollback();
            throw $e;
            return false;
        }
    }
    public function transaction_callback($callbacks) {
        try {
            $this->pdo->beginTransaction();
            foreach($callbacks as $callback) {
                if(!$callback($this)) throw new Exception('myPDO: Transaction Callback Failed - Error Info: '.implode(',',$this->pdo->errorInfo()));
            }
            $this->pdo->commit();
            return true;
        } catch(Exception $e) {
            $this->pdo->rollback();
            throw $e;
            return false;
        }
    }
    # ------------------------------------------------------
    # ready statements
    public function setAutoIncrement($number = 'NEXT', $f = 'id') {
        if(strtoupper($number) === 'NEXT') $number = $this->getNextId($f);
        $number = intval($number);
        return $this->query("ALTER TABLE ".$this->table." AUTO_INCREMENT = ".$number.";");
    }
    # ------------------------------------------------------
    # operations
    public function insert($c, $v) {
        list($cols, $vals, $qp) = $this->handleInsertColumnsAndValues($c, $v);
        $stmt = $this->pdo->prepare($this->ready_query_string_insert($c));
        $res = $stmt->execute($vals);
        $stmt = null;
        return $res;
    }
    public function insert_checkDublicate($c, $v) {
        try {
            return $this->insert($c, $v);
        } catch(Exception $e) {
            $this->setError(implode(",",$e->errorInfo));
            if(intval($e->errorInfo[1]) === 1062) return -1;
            return false;
        }
    }
    public function update($c, $v, $wc, $wv) {
        list($cols, $vals, $w, $wvv, $cols_and_qp) = $this->handleUpdateColumnsAndValues($c, $v, $wc, $wv);
        $stmt = $this->pdo->prepare($this->ready_query_string_update($c, $wc));
        $res = $stmt->execute($vals); //, $stmt->rowCount()];
        $stmt = null;
        return $res;
    }
    public function delete($c, $v) {
        list($w, $wv) = $this->handleDeleteColumnsAndValues($c, $v);
        $stmt = $this->pdo->prepare($this->ready_query_string_delete($c));
        $res = $stmt->execute($wv);
        $stmt = null;
        return $res;
    }
    # ------------------------------------------------------
    // ready string queries
    public function ready_query_string_select($s, $w, $tbl=null) {
        if(!$tbl) $tbl = $this->table;
        $s_cols = $this->handleSelectColumns($s);
        $w_cols = $this->handleWhereColumns($w);
        $q = "SELECT ".$s_cols." FROM ".$tbl.($w ? $w_cols : "");
        return $q;
    }
    public function ready_query_string_insert($c, $tbl=null) {
        if(!$tbl) $tbl = $this->table;
        list($cols, $vals, $qp) = $this->handleInsertColumnsAndValues($c, null);
        $q = "INSERT INTO ".$tbl." (".$cols.") VALUES (".$qp.")";
        return $q;
    }
    public function ready_query_string_update($c, $wc, $tbl=null) {
        if(!$tbl) $tbl = $this->table;
        list($cols, $vals, $w, $wv, $cols_and_qp) = $this->handleUpdateColumnsAndValues($c, null, $wc, null);
        $q = "UPDATE ".$tbl." SET ".$cols_and_qp.$w;
        return $q;
    }
    public function ready_query_string_delete($c, $tbl=null) {
        if(!$tbl) $tbl = $this->table;
        list($w, $wv) = $this->handleDeleteColumnsAndValues($c, null);
        $q = "DELETE FROM ".$tbl.$w;
        return $q;
    }
    # ------------------------------------------------------
}
?>
