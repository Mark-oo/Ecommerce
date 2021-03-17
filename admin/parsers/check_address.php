<?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php';
 $name = sanitize($_POST['full_name']);
 $email = sanitize($_POST['email']);
 $country = sanitize($_POST['country']);
 $city = sanitize($_POST['city']);
 $street = sanitize($_POST['street']);
 $errors = array();
 $required = array(
   'full_name' => 'Ime i Prezime',
   'email'     => 'Email',
   'country'   => 'Drzava',
   'city'      => 'Grad',
   'street'    => 'Ulica',
 );
// proveri dal su sva polja popunjena
 foreach($required as $f => $d){
  if(empty($_POST[$f]) || $_POST[$f]  == '' ){
    $errors[]=$d.' je potrebno popuniti.';
  }
 }
 // dal j email validan
 if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
   $errors[]='Unesi validnu email adresu';
 }


// ispisi greske
 if(!empty($errors)){
   echo display_errors($errors);
 }else{
   echo 'passed';
 }
 ?>
