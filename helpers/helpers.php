<?php
// errors display
function display_errors($errors){
  $display='<ul class="alert-warning" role="alert">';
  foreach ($errors as $error) {
   $display.='<li class="alert-danger" roel="alert">'.$error.'</li>';
  }
  $display.='</ul>';
  return $display;
}

// cisti
function sanitize($dirty){
  return htmlentities($dirty,ENT_QUOTES,"UTF-8");
}

// sama pise znak za valutu i nalazi decimale
function money($number){
  return  '$'.number_format($number,2);
}

// login user
function login($user_id){
  $_SESSION['SBUser']=$user_id;
  global $db;
  $date=date("Y-m-d H:i:s");
  $db->query("UPDATE users SET last_login='$date' WHERE id='$user_id'");
  $_SESSION['success_flash']='Sad ste ulogovani';
  header('Location: index.php');
}

// if is loged in
function is_logged_in(){
  // SBUser moze da bude bilo sta on ga je tako nazvao
  // >0 je provera dal je int
  if (isset($_SESSION['SBUser']) && $_SESSION['SBUser'] > 0) {
    return true;
  }
  return false;
}

// preusmeravanje sa index na login ako nisu ulogovan
function login_error_redirect($url='login.php'){
  $_SESSION['error_flash']='Moras biti ulogovan da pristupis ovoj strani';
  header('Location: '.$url);
}

// akko nemas dozvolu da pristupas tome rkne te negde drugde
function permission_error_redirect($url='login.php'){
  $_SESSION['error_flash']='Nisi dovoljno bitan da smes pristupiti onoj stranici';
  header('Location: '.$url);
}

// dozvole sta sme i ne sme
function has_permission($permission='bog'){
  global $user_data;
  $permissions=explode(',',$user_data['permissions']);
  if(in_array($permission,$permissions,true)){
    return true;
  }
  return false;
}
// ulepsava datum za user tabelu
function pretty_date($date){
  return date("D m Y - H:i",strtotime($date));
}

function get_category($child_id){
  global $db;
  $id= sanitize($child_id);
  $sql="SELECT p.id AS 'pid', p.category AS 'parent', c.id as 'cid', c.category AS 'child'
        FROM categories c
        INNER JOIN categories p
        ON c.parent=p.id
        WHERE c.id ='$id'";
  $query=$db->query($sql);
  $category=mysqli_fetch_assoc($query);
  return $category;
}

function sizesToArray($string){
  $sizesArray=explode(',',$string);
  $returnArray=array();
  foreach($sizesArray as $size){
    $s=explode(':',$size);
    $returnArray[]=array('size'=>$s[0],'quantity'=>$s[1], 'threshold'=>$s[2]);
  }
  return $returnArray;
}


function sizesToString($sizes){
  $sizeString ='';
  foreach($sizes as $size){
    $sizeString.=$size['size'].':'.$size['quantity'].':'.$size['threshold'].',';
  }
  $trimmed=rtrim($sizeString,',');
  return $trimmed;
}
