<?php
 require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php';
 $product_id=sanitize($_POST['product_id']);
 $available=sanitize($_POST['available']);
 $quantity=sanitize($_POST['quantity']);
 $size=sanitize($_POST['size']);
 $item=array();
 $item[]= array(
   'id'       => $product_id,
   'size'     => $size,
   'quantity' => $quantity,
 );
 // ovo pomaze da mogu da pokrenm cookie u localhosthrom to nesto nece da dozvoli
$domain=($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false;

$query= $db->query("SELECT * FROM products WHERE id ='{$product_id}'");
$product= mysqli_fetch_assoc($query);
$_SESSION['success_flash']=$product['title']. ' dodato u Vasa kolica';

// proveri dal cookie postoji
if($cart_id!= ''){
  // error_log("jebi se");
  $cartQ=$db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
  $cart=mysqli_fetch_assoc($cartQ);
  $previous_items=json_decode($cart['items'],true);
  $item_match = 0;
  $new_items=array();
  // dodajemo item ako je isti item
  foreach($previous_items as $pitem){
    // provera jel ovo sto se unosi je isto kao prethodno uneseno
    if($item[0]['id'] == $pitem['id'] && $item[0]['size'] == $pitem['size']){
      $pitem['quantity']= $pitem['quantity']+$item[0]['quantity'];
      // ako je zbir veci od available onda samo da available
      if($pitem['quantity']>$available){
        $pitem['quantity']=$available;
      }
      $item_match= 1;
    }
    $new_items[]=$pitem;
  }
  // dodajem item ako nije isti kao prethodni item
  if($item_match !=1){
    $new_items=array_merge($item,$previous_items);
  }
  $items_json=json_encode($new_items);
  $cart_expire= date("Y-m-d H:i:s",strtotime("+30 days"));
  $db->query("UPDATE cart SET items='{$items_json}',expire_date='{$cart_expire}' WHERE id = '{$cart_id}'");
  setcookie(CART_COOKIE,'',1,"/",$domain,false);
  setcookie(CART_COOKIE,$cart_id,CART_COOKIE_EXPIRE,'/',$domain,false);
}else {
  // error_log ("'{$available}'" );
  // error_log ("'{$quantity}'" );
  // dodaj kolica bazi  i set cookie
  $items_json=json_encode($item);
  $cart_expire=date("Y-m-d H:i:s",strtotime("+30 days"));
  $db->query("INSERT INTO cart (items,expire_date) VALUES ('{$items_json}','{$cart_expire}')");
  $cart_id = $db->insert_id;

  // error_log("pusi ga");
  // error_log("$cart_id");
  // $db->mysqli_insert_id($cart_id);
   // setcookie(name, value, expire, path, domain, security);

// $cc = time() + (86400*30);
// $cc1 = "";

// $cc = CART_COOKIE;
// $cc1 = CART_COOKIE_EXPIRE;
   // error_log ("$cc");
   // error_log ("'{$cart_id}'" );
   // error_log ("$cc1");
   // error_log ("'{$domain}'" );
   // define('BASEURL',$_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/');
   //
   // define('CART_COOKIE','vSQyqjwDEdG3');
   // define('CART_COOKIE_EXPIRE',time() + (86400*30));
   setcookie(CART_COOKIE,$cart_id,CART_COOKIE_EXPIRE,'/',"",false);
   // error_log("$pera");
}
 ?>
