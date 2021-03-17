<?php
$db = mysqli_connect('127.0.0.1','root','','andjelkineumotvorine');
// pocetak sesije
session_start();

if (mysqli_connect_errno()) {
  echo 'Database cennection failed with following errors:'.mysqli_connect_error();
  die();
}
require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/config.php';
require_once BASEURL.'helpers/helpers.php';
require BASEURL.'vendor/autoload.php';

// postavljamo cookie
$cart_id='';
if(isset($_COOKIE[CART_COOKIE])){
  $cart_id=sanitize($_COOKIE[CART_COOKIE]);
}

// odavde vucem sve za usera
if (isset($_SESSION['SBUser'])) {
  $user_id=$_SESSION['SBUser'];
  $query=$db->query("SELECT * FROM users WHERE id='$user_id'");
  $user_data=mysqli_fetch_assoc($query);
  $fn=explode(' ', $user_data['full_name']);
  $user_data['first']=$fn[0];
  $user_data['last']=$fn[1];
}
// akko uspe loggin
if(isset($_SESSION['success_flash'])){
  echo '<div class="alert alert-success"><p class="text-success text-center">'.$_SESSION['success_flash'].'</p></div>';
  unset($_SESSION['success_flash']);
}
// akko neuspe loggin
if(isset($_SESSION['error_flash'])){
  echo '<div class="alert alert-danger"><p class="text-danger text-center">'.$_SESSION['error_flash'].'</p></div>';
  unset($_SESSION['error_flash']);
}
// session_destroy();
?>
