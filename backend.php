<?
plugins_register_backend($plugin,array("icon"=>"icon-user",
"sub"=>array(
  "users"=>"Benutzer",
  "groups"=>"Gruppen",
  "acls"=>"ACL",
  "tokens"=>"Auth-Token",
)));
require("class.User.php");
require("class.Group.php");
require("class.ACL.php");
require("class.Token.php");

//json only callback
function UT_encodeToken() {
  if(!isset($_GET["user_id"]))
    throw new Exception("target user id missing");
  $user_obj=User::getById($_GET["user_id"]);
  $tokens=UserToken::getByFilter("where users_id=? and active=1",$user_obj->id);
  if(sizeof($tokens)>0)
    throw new Exception("user has active tokens, deactivate these first");
  
  $key=GPG_Key::getById(1);
  $url="http://msbs.selfhost.eu/ks_services/api.php?action=rfid_auth";
  $content=@file_get_contents($url);
  if($content===false)
    throw new Exception("could not contact the terminal");
  $content=json_decode($content);
  if($content===null)
    throw new Exception("terminal returned garbage content");

  if(!isset($content->data) || !isset($content->data->card_uid)) {
    if($content->status=="error")
      throw new Exception("terminal returned error: ".$content->message);
    else
      throw new Exception("card uid not returned");
  }
  
  $uid=$content->data->card_uid;
  $sig="";
  openssl_sign($uid,$sig,$key->processProperty("privkey_handle"),OPENSSL_ALGO_SHA1);
  $sig=base64_encode($sig);
  $wd=serialize(array("sig"=>$sig,"ts"=>date("d.m.Y H:i:s"),"cr"=>$_SESSION["user"]["name"]));
  openssl_sign($wd,$dsig,$key->processProperty("privkey_handle"),OPENSSL_ALGO_SHA1);
  
  $dsig=urlencode(base64_encode($dsig));
  $wd=urlencode(base64_encode($wd));
  
  $url="http://msbs.selfhost.eu/ks_services/api.php?action=rfid_encode&data=$wd&sig=$dsig&card_uid=$uid";
  echo "loading $url\n";
  $content=@file_get_contents($url);
  if($content===false)
    throw new Exception("could not contact the terminal");
  $content=json_decode($content);
  if($content===null)
    throw new Exception("terminal returned garbage content");
  if($content->status=="error")
    throw new Exception("terminal returned error: ".$content->message);
  
  $ut=UserToken::fromScratch();
  $ut->users_id=$user_obj->id;
  $ut->serial=$uid;
  $ut->active=1;
  $ut->data="";
  $ut->type=1;
  $ut->commit();
}
plugins_register_backend_handler($plugin,"transactions","encodeToken","UT_encodeToken");

