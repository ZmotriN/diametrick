<?php
/**
 * $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
 */
class DB {

    const TEXT_START = 'Début';
    const TEXT_END   = 'Fin';
    const TEXT_PREV  = 'Précédent';
    const TEXT_NEXT  = 'Suivant';

    public static $conn;

    public static function connect($host, $user, $pass, $db, $port) {
        self::$conn = @new mysqli($host, $user, $pass, $db, $port);
        return self::$conn->connect_errno ? false : true;
    }


    public static function escape($str) {
        return self::$conn->real_escape_string($str);
    }


    public static function query($query, $row=false, $cell=false) {
        if(!$result = self::$conn->query($query)) return [];
        if($row) {
            if($cell) return current($result->fetch_array());
            else return $result->fetch_object();
        }
        while($obj = $result->fetch_object()) $objs[] = $obj;
        return isset($objs) ? $objs : [];
    }


    public static function exec($query) {
        if(!self::$conn->query($query)) return false;
        else return true;
    }


    public static function insert($table, $values, $ignoreDups=false) {
        if(!$fields = self::getTableFields($table)) return false;
        foreach($values as $k => $v) if(!isset($fields[$k])) unset($values[$k]);
        $query = 'INSERT INTO ' . $table . '(' . join(',', array_keys($values)) . ') ' .
            'VALUES (' . join(',', array_fill(0, count($values), '?')) . ')';
        if($ignoreDups) $query .= ' ON DUPLICATE KEY UPDATE '.$ignoreDups.';';
        else $query .= ';';
        $mask = '';
        foreach($values as $k => $v) $mask .= $fields[$k];
        $params = [$mask];
        foreach($values as $k => $v) $params[] = $v;
        if(!$stmt = self::$conn->prepare($query)) return false;
        if(!@call_user_func_array([$stmt, 'bind_param'], self::refValues($params))) return false;
        if(!$stmt->execute()) return false;
        return self::$conn->insert_id;
    }


    public static function update($table, $ids, $values) {
        if(!$fields = self::getTableFields($table)) return false;
        foreach($values as $k => $v) if(!isset($fields[$k])) unset($values[$k]);
        $query = 'UPDATE ' . $table . ' SET ';
        foreach ($values as $k => $v) $upfields[] = $k . ' = ?';
        $query .= implode(', ', $upfields) . ' WHERE ';
        foreach ($ids as $k => $v) $whfields[] = $k . ' = ?';
        $query .= implode(' AND ', $whfields);
        $mask = '';
        foreach($values as $k => $v) $mask .= $fields[$k];
        foreach($ids as $k => $v) $mask .= $fields[$k];
        $params = [$mask];
        foreach($values as $k => $v) $params[] = $v;
        foreach($ids as $k => $v) $params[] = $v;
        if(!$stmt = self::$conn->prepare($query)) return false;
        if(!@call_user_func_array([$stmt, 'bind_param'], self::refValues($params))) return false;
        if(!$stmt->execute()) return false;
        return true;
    }


    public static function delete($table, $id) {
        return self::exec("DELETE FROM ".$table." WHERE id = ".$id);
    }


    public static function getObject($table, $id) {
        if(!$result = self::$conn->query("SELECT * FROM ".$table." WHERE id = ".$id)) return false;
        if(!$obj = $result->fetch_object()) return false;
        return $obj;
    }


    private static function getTableFields($table) {
        static $tables = [];
        if(empty($tables[$table])) {
            if(!$result = self::$conn->query('DESCRIBE '.$table)) return false;
            while($obj = $result->fetch_object()) {
                if(!preg_match('#^([^\(]+)#i', $obj->Type, $m)) continue;
                switch($m[1]) {
                    case 'int':
                    case 'tinyint':
                        $fields[$obj->Field] = 'i';
                        break;
                    default:
                        $fields[$obj->Field] = 's';
                }
            }
            $tables[$table] = $fields;
        }
        return $tables[$table];
    }

    public static function refValues($arr){
        foreach($arr as $key => $value) $refs[$key] = &$arr[$key];
        return isset($refs) ? $refs : [];
    }


    public static function getPage($query, $page=0) {
        if(!preg_match('# SQL_CALC_FOUND_ROWS #i', $query))
            $query = preg_replace('#^SELECT\s#i', 'SELECT SQL_CALC_FOUND_ROWS ', $query);
        $query .= " LIMIT ".($page * PAGE_SIZE).",".(PAGE_SIZE + 1).";";

        foreach(self::query($query) as $item) $items[] = $item;
        
        $response = new stdClass;
        $response->paging = new stdClass;
        $response->paging->page_size = PAGE_SIZE;
        $response->paging->page = $page + 1;
        $response->paging->total = self::query("SELECT FOUND_ROWS();", true, true);
        $response->paging->total_pages = ceil($response->paging->total / PAGE_SIZE);

        $response->paging->prev = $page == 0 ? false : true;
        if(isset($items[PAGE_SIZE])) {
            unset($items[PAGE_SIZE]);
            $response->paging->next = true;
        } else $response->paging->next = false;

        $response->paging_table = self::getPagingTable($response->paging);

        $response->items = $items ?? [];

        return $response;
    }


    public static function getPagingTable($paging, $brpage=5) {
        $str = '<table class="paging"><tr>';

        $str .= '<td>';
        if($paging->prev) {
            $str .= '<a href="?page=1">&lt;&lt;'.self::TEXT_START.'</a>';
            $str .= '&nbsp;&nbsp;';
            $str .= '<a href="?page='.($paging->page - 1).'">&lt;'.self::TEXT_PREV.'</a>';
            $str .= '&nbsp;&nbsp;';
            $start = $paging->page - $brpage;
            if($start < 1) $start = 1;
            for($i = $start; $i < $paging->page; $i++) {
                $str .= '<a href="?page='.$i.'">'.$i.'</a>&nbsp;';
            }
        } else $str .= '&nbsp;';
        $str .= '</td>';

        $str .= '<td>';
        $str .= $paging->page .'/'.($paging->total_pages ?: 1).' <em>('.number_format($paging->total, 0, '.', ' ').')</em>';
        $str .= '</td>';

        $str .= '<td>';
        if($paging->next) {
            $end = $paging->page + $brpage;
            if($end > $paging->total_pages) $end = $paging->total_pages;
            for($j = $paging->page + 1; $j <= $end ; $j++){
                $str .= '<a href="?page='.$j.'">'.$j.'</a>&nbsp;&nbsp';
            }
            $str .= '<a href="?page='.($paging->page + 1).'">'.self::TEXT_NEXT.'&gt;</a>';
            $str .= '&nbsp;&nbsp;';
            $str .= '<a href="?page='.$paging->total_pages.'">'.self::TEXT_END.'&gt;&gt;</a>';
        } else $str .= '&nbsp;';
        $str .= '</td>';

        $str .= '</tr></table>';
        return $str;
    }


    public static function dump($dest) {
        set_time_limit(0);
        $options = [
            '--host='.MYSQL_HOST,
            '--user='.MYSQL_USER,
            '--password='.MYSQL_PASS,
            '--default-character-set=utf8',
            '--opt',
            '--single-transaction',
            '--routines',
            '--triggers',
            '--events',
            '--add-drop-table',
            '--complete-insert',
            '--delayed-insert',
            '--tz-utc',
            MYSQL_DB
        ];
        exec('mysqldump '.join(' ',$options).' > ' .escapeshellarg($dest), $out, $code);
        return !$code;
    }

}