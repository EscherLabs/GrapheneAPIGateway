<?php

namespace App\Libraries;

class OracleDB {

  public static $objConnect = null;
  private static $databases = [];

  private static function find_params_in_query($query,$params) {
    $query_params = array();
    foreach($params as $param_name => $param_value) {
      if (stristr($query, ':'.$param_name)) {
        $query_params[$param_name] = $param_value;
      }
    }
    return $query_params;
  }

    public static function config_database($db_name, $db_config) {
        self::$databases[$db_name] = $db_config;
    }

    public static function connect($db_name)
    {
        self::$objConnect = \oci_connect(self::$databases[$db_name]['user'],
                                        self::$databases[$db_name]['pass'],
                                        self::$databases[$db_name]['tns_name'],
                                        'AL32UTF8');

        if (!self::$objConnect) {
            $e = \oci_error();
            if (ini_get('display_errors') == 'On') { trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR); } 
        }
    }

  public static function select($query,$params=array())
  {   
    $stid = \oci_parse(self::$objConnect, $query);
    $params = self::find_params_in_query($query,$params);
    if (!$stid) {
      $e = \oci_error(self::$objConnect);
      throw new Excepton($e['message']);
    }

    foreach($params as $param_name => $param_value) {
      $r = \oci_bind_by_name($stid,':'.$param_name,$params[$param_name]);
      if (!$r) {
        echo "Something Broke!\n";
      }
    }

    $r = \oci_execute($stid);
    if (!$r) {
      $e = \oci_error($stid);
      throw new Excepton($e['message']);
    }

    $row_num = 0;
    $results = array();
    while ($row = \oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {
      foreach ($row as $column_name => $value) {
        $results[$row_num][$column_name] = $value;
      }
      $row_num++;
    }
    \oci_free_statement($stid);
    return $results;
  }

  public static function insert($query,&$params=array(),$index_name=null)
  {
    $params = self::find_params_in_query($query,$params);
    if (!is_null($index_name)) {
      $query .= " RETURNING ".$index_name." INTO :rid";
    }

    $stid = \oci_parse(self::$objConnect, $query);
    foreach($params as $param_name => $param_value) {
      \oci_bind_by_name($stid,':'.$param_name,$params[$param_name]);
    }
    if (!is_null($index_name)) {
      \oci_bind_by_name($stid, ":rid", $rowid, SQLT_RDD);
    }

    $objExecute = \oci_execute($stid, OCI_DEFAULT);
    if($objExecute)
    {
      \oci_commit(self::$objConnect);
    }
    else 
    {
      \oci_rollback(self::$objConnect);
      $e = \oci_error($stid);
      throw new Exception($e['message']);
    }
    if (!is_null($index_name)) {
      return $rowid;
    } else {
      return true;
    }
  }

  public static function delete($query,$params=array()) { return self::insert($query,$params); }
  public static function update($query,$params=array()) { return self::insert($query,$params); }

  public static function close()
  {
    \oci_close(self::$objConnect);
    self::$objConnect = null;
  }
}
?>
