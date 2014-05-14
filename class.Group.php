<?
class Group extends DBObj {
  protected static $__table="groups";
  public static $mod="user";
  public static $sub="groups";
  
  public static $elements=array(
    "name"=>array("title"=>"Name","mode"=>"string","dbkey"=>"name"),
    "description"=>array("title"=>"Beschreibung","mode"=>"string","dbkey"=>"description"),
  );
  
  public static $link_elements=array(
    "name","description"
  );
  public static $list_elements=array(
    "name","description"
  );
  public static $detail_elements=array(
    "name","description"
  );
  public static $edit_elements=array(
    "name","description"
  );
  public static $links=array(
    "User"=>array("title"=>"Mitglieder","table"=>"link_users_groups"),
  );

}
plugins_register_backend_handler($plugin,"groups","list",array("Group","listView"));
plugins_register_backend_handler($plugin,"groups","edit",array("Group","editView"));
plugins_register_backend_handler($plugin,"groups","view",array("Group","detailView"));
plugins_register_backend_handler($plugin,"groups","submit",array("Group","processSubmit"));
