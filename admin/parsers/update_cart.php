<?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php';
 $pedo=isset($_POST['pedo'])? sanitize($_POST['pedo']) : '';
 $edit_size=isset($_POST['edit_size'])? sanitize($_POST['edit_size']) : '';
 $edit_id=isset($_POST['edit_id'])? sanitize($_POST['edit_id']) : '';
   var_dump($_POST['pedo']);
   var_dump($_POST['edit_size']);
   var_dump($_POST['edit_id']);

 $cartQ=$db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
 $result=mysqli_fetch_assoc($cartQ);
 var_dump($result);
 $items= json_decode($result['items'],true);
 var_dump($items);
 $updated_items= array();
 $domain = (($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false);
 // akko kliknes -
 if($pedo == 'smanji'){
   foreach($items as $item){
     if($item['id'] == $edit_id && $item['size'] == $edit_size){
       $item['quantity']=$item['quantity']-1;
     }
     if($item['quantity'] > 0){
       $updated_items[]=$item;
       echo("govno");
       error_log("'$updated_items'");
     }
   }
 }
 // akko klik +
 if($pedo == 'povecaj'){
   foreach($items as $item){
     if($item['id'] == $edit_id && $item['size'] == $edit_size){
       $item['quantity']=$item['quantity']+1;
     }
     $updated_items[]=$item;
     echo("govnar");
   }
 }
 // var_dump($updated_items);
 // ovde saljemo nazad pormene
 if(!empty($updated_items)){
   $json_updated = json_encode($updated_items);
   $db->query("UPDATE cart SET items = '{$json_updated}' WHERE id='{$cart_id}'");
   $_SESSION['success_flash']= 'Vasa kolica su izmenjena';
          error_log("'$updated_items'");
 }
 if(empty($updated_items)){
   echo("jedi govna");
   $db->query("DELETE FROM cart WHERE id= '{$cart_id}'");
   setcookie(CART_COOKIE,'',1,"/",$domain,false);
 }
 ?>
