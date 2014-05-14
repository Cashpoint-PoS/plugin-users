<?
class User extends DBObj {
  protected static $__table="users";
  public static $mod="user";
  public static $sub="users";
  
  //the properties this object exposes to extern
  //this is NOT the list of the properties accessible on the object,
  //rather it is a list of the properties available for the views
  //array(title=property pretty-name,
  //      dbkey=database key of this property(if mode=pass)
  //      data=
  //           mode=select|radio => value-title mapping array
  //           mode=string => HTML input type for edit view (password, email, tel etc.), default="text"
  //      mode=
  //           string=pass dbkey as single-line string
  //           text=pass dbkey as multi-line string
  //           select=use data as pair for <select>
  //           radio=use data as pair for radiobuttons
  //           process=pass the key to processProperty to determine a value
  public static $elements=array(
    "name"=>array("title"=>"Benutzername","mode"=>"string","dbkey"=>"name"),
    "dname"=>array("title"=>"Name","mode"=>"string","dbkey"=>"displayname"),
    "active"=>array("title"=>"Aktiv","mode"=>"select","data"=>array(0=>"Nein",1=>"Ja"),"dbkey"=>"is_active"),
    "pass"=>array("title"=>"Passwort-Hash","mode"=>"string","dbkey"=>"password","data"=>"password"),
  );
  
  //static, the listview does not have a concrete object
  //array of the elements which appear as columns in listview
  public static $list_elements=array(
    "name","dname","active"
  );
  
  //not static, detail-view has an object
  //array of the elements which appear as rows in detailview
  public static $detail_elements=array(
    "name","dname","active","pass"
  );
  
  //Relationship with other objects (classname=>array(title=tab-title, table=>relation table)
  public static $links=array(
    "Group"=>array("title"=>"Gruppen","table"=>"link_users_groups"),
  );
  
  //Custom detail views
  //tab-title=>object function to display (with parameter: format=html|json)
  //function is supposed to be_error for unknown format
  public static $detail_views=array(
    "Rechte"=>"eacls_view",
  );
  
  //what elements to show in the linked-from view
  public static $link_elements=array(
    "name","dname"
  );
  
  //elements to show in edit-view (and accept in submits!!)
  public static $edit_elements=array(
    "name","dname","active","pass"
  );
  
  //processed keys (e.g. invoice sum, tax calculations etc)
  public function processProperty($key) {
    switch($key) {
      default: return NULL;
    }
  }
  
  //overrides parent for password-backup
  public function loadFrom($id,$recurse=true) {
    parent::loadFrom($id,$recurse);
    //back up the old password so we can compare against it
    //in commit() to save us a getById call there
    $this->__password=$this->password;
  }
  //pre-commit interceptor to check for password change
  public function commit() {
    if(!property_exists($this,"__password"))
      $this->__password="\0";
    
    if($this->__password!==$this->password) {
      $version=0;
      $iterations=1;
      $alg="sha256";
      $salt=generateSalt();
      $pass=$this->password;
      $hash=hash($alg,$pass.$salt);
      $newpw=sprintf("%d:%d:%s:%s:%s",0,1,"sha256",$hash,$salt);
      $this->password=$newpw;
    }
    parent::commit();
  }

  function eacls_view($format) {
    switch($format) {
      case "html":
        $this->eacls_view_html();
        break;
      default:
        be_error(500,"be_index.php?mod=index","Format unbekannt");
        break;
    }
  }
  //get the code for an effective-ACL tab in view mode
  //this shows all the ACLs which influence what this user can do on any objects
  //be warned, this method is just fucking expensive.
  //TODO: Refactor this shit.
  function eacls_view_html() {
    $list=ACL::getAll();
    $groups=get_user_groups($this->id);
  ?>
    <div class="box plain">
      <table class="table table-striped table-bordered">
        <thead><tr>
          <th>ID</th>
          <th>Gültig für Objekt(e)</th>
          <th>Betrifft Benutzer</th>
          <th>Rechte</th>
          <th>Erstellt von</th>
          <th>Erstellt</th>
          <th>Letzter Bearbeiter</th>
          <th>Letzte Bearbeitung</th>
        </tr></thead>
        <tbody>
  <?
    foreach($list as $acl) {
      $subject="";
      if($acl->object_type==="")
        $subject="Alle Objekte";
      else {
        if($acl->object_id===0)
          $subject="Alle des Typs ".$acl->object_type;
        else
          $subject="Nur das Objekt ".$acl->object_type."/".$acl->object_id;
      }
      
      $target="";
      if($acl->target_type==="")
        $target="Alle Benutzer und Gruppen";
      elseif($acl->target_type==="user") {
        if($acl->target_id!==$this->id)
          continue;
        $target="Benutzer ".$acl->target_id." (".$this->displayname.")";
      } elseif($acl->target_type==="group") {
        if(!in_array($acl->target_id,$groups))
          continue;
        $target="Alle Benutzer der Gruppe ".$acl->target_id." (".(Group::getById($acl->target_id,false)->description).")";
      }
      
      $fields=str_split($acl->acl);
      $aclstr="";
      foreach($fields as $field) {
        switch($field) {
          case "r": $aclstr.="<span class=\"icon-eye\" title=\"Zeige das Objekt an\"></span>"; break;
          case "w": $aclstr.="<span class=\"icon-pen\" title=\"Bearbeite das Objekt\"></span>"; break;
          case "c": $aclstr.="<span class=\"icon-document-alt-stroke\" title=\"Erstelle ein neues Objekt dieses Typs\"></span>"; break;
          case "d": $aclstr.="<span class=\"icon-trash-stroke\" title=\"Lösche das Objekt\"></span>"; break;
          default: $aclstr.="<span style=\"color:red\" title=\"Unbekanntes Flag\">$field</span>"; break;
        }
      }
  ?>
          <tr>
            <td><?= $acl->id ?></td>
            <td><?= $subject ?></td>
            <td><?= $target ?></td>
            <td><?= ($acl->negate===1)? "Entferne: " : "" ?><?= $aclstr ?></td>
            <td><?= esc($acl->creator["name"]) ?></td>
            <td><?= esc($acl->create_time) ?></td>
            <td><?= esc($acl->last_editor["name"]) ?></td>
            <td><?= esc($acl->modify_time) ?></td>
          </tr>
  <?
    }
  ?>
        </tbody>
      </table>
    </div> <!-- box -->
  <?
  }
  public function validate() {
    parent::validate();
    //check 1: check for another user with same login name
    $sameNameCheck=static::getByFilter("where name=? and id!=?",$this->name,$this->id);
    if(sizeof($sameNameCheck)>0)
      $this->__invalidFields[]="name";
  }
}

plugins_register_backend_handler($plugin,"users","list",array("User","listView"));
plugins_register_backend_handler($plugin,"users","edit",array("User","editView"));
plugins_register_backend_handler($plugin,"users","view",array("User","detailView"));
plugins_register_backend_handler($plugin,"users","submit",array("User","processSubmit"));
