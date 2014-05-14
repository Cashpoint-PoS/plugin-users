<?
class UserToken extends DBObj {
  protected static $__table="user_tokens";
  public static $mod="user";
  public static $sub="tokens";
  
  public static $elements=array(
    "type"=>array("title"=>"Typ","mode"=>"select","data"=>array("MIFARE 1k","GPG SmartCard"),"dbkey"=>"type"),
    "users_id"=>array("title"=>"Benutzer","mode"=>"string","dbkey"=>"users_id"),
    "active"=>array("title"=>"Status","mode"=>"select","data"=>array("Gesperrt","Aktiv"),"dbkey"=>"active"),
    "serial"=>array("title"=>"Seriennummer","mode"=>"string","dbkey"=>"serial"),
    "data"=>array("title"=>"Zusatzdaten","mode"=>"string","dbkey"=>"data"),
  );
  
  public static $list_elements=array(
    "type",
    "user",
    "active",
    "serial",
  );
  public static $detail_elements=array(
    "type",
    "user",
    "active",
    "serial",
  );
  public static $links=array(
  );
  public static $detail_views=array(
  );
  public static $link_elements=array(
  );
  public static $edit_elements=array(
    "type",
    "user",
    "active",
    "serial",
    "data",
  );
  
  public function processProperty($key) {
    switch($key) {
      default: return NULL;
    }
  }
}

plugins_register_backend_handler($plugin,"tokens","list",array("UserToken","listView"));
plugins_register_backend_handler($plugin,"tokens","edit",array("UserToken","editView"));
plugins_register_backend_handler($plugin,"tokens","view",array("UserToken","detailView"));
plugins_register_backend_handler($plugin,"tokens","submit",array("UserToken","processSubmit"));
