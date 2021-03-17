  <?php
  require_once 'core/initialization.php';

  // Set your secret key. Remember to switch to your live secret key in production!
  // See your keys here: https://dashboard.stripe.com/account/apikeys
  \Stripe\Stripe::setApiKey(STRIPE_PRIVATE);

  // Token is created using Stripe Checkout or Elements!
  // Get the payment token ID submitted by the form:
  // var_dump($_POST);
  $token = $_POST['stripeToken'];

  //get the rest of the post data
  $full_name=sanitize($_POST['full_name']);
  $email=sanitize($_POST['email']);
  $street=sanitize($_POST['street']);
  $city=sanitize($_POST['city']);
  $country=sanitize($_POST['country']);
  $kupon=sanitize($_POST['kupon']);
  $total_price=sanitize($_POST['total_price']);
  $final_price=sanitize($_POST['final_price']);
  $cart_id=sanitize($_POST['cart_id']);
  $description=sanitize($_POST['description']);
  // ovako moram saljem valjda jer swvift trazi dtako da im saljes koliko para se skida nisam siguran dal mora
  $charge_amount=number_format($final_price,2)*100;
  $metadata=array(
    "cart_id"     => $cart_id,
    "kupon"       => $kupon,
    "total_price" => $total_price,
  );


  try{
  $charge = \Stripe\Charge::create(array(
    "amount"      => $charge_amount,
    "currency"    => CURRENCY,
    "source"      => $token,
    "description" => $description,
    "metadata"    => $metadata,)
  );
// adjust inventory
$itemQ = $db->query("SELECT * FROM cart WHERE id= '{$cart_id}'");
$iresults=mysqli_fetch_assoc($itemQ);
$items=json_decode($iresults['items'],true);
foreach($items as $item){
  $newSizes= array();
  $item_id = $item['id'];
  $productQ=$db->query("SELECT sizes FROM products WHERE id='{$item_id}'");
  $product=mysqli_fetch_assoc($productQ);
  $sizes=sizesToArray($product['sizes']);
  // $b=array('1',"2",'3');var_dump($b);
  // $c=sizesToString($b);
  // var_dump($c);
  // var_dump($sizes);
  foreach($sizes as $size){
    if($size['size'] == $item['size']){
      $q= $size['quantity'] - $item['quantity'];
      $newSizes[]= array('size'=>$size['size'],'quantity'=>$q);
    }else{
      $newSizes[]=array('size'=>$size['size'],'quantity'=>$size['quantity']);
      // $newSizes2=implode($newSizes);
    }
  }
  // var_dump($newSizes);
  // var_dump($newSizes2);
  $sizeString= sizesToString($newSizes);
  $db->query("UPDATE products SET sizes='{$sizeString}' WHERE id='{$item_id}'");
}
  var_dump($sizeString);


// update the cart
  $db->query("UPDATE cart SET paid=1 WHERE id='{$cart_id}'");
  $db->query("INSERT INTO transactions
     (charge_id,cart_id,full_name,email,street,city,country,total_price,kupon,final_price,description,txn_type) VALUES
     ('$charge->id','$cart_id','$full_name','$email','$street','$city','$country','$total_price','$kupon','$final_price','$description','$charge->object')");


     $domain=($_SERVER['HTTP_HOST'] !='localhost')?'.'.$_SERVER['HTTP_POST'] :false;
     setcookie(CART_COOKIE,'',1,"/",$domain,false);
     include 'includes/head.php';
     include 'includes/navigation.php';
     include 'includes/headerpartial.php';
    ?>
    <h1 class="text-center text-success">Hvala najlepse</h1>
    <p>Sa Vase kartice je skinuto <?=money($final_price); ?>.Na email adresu vam je poslat racun.</p><br>
    <p>Broj Vaseg fiskalnog racuna je <strong><?=$cart_id; ?></strong></p><br>
    <p>Adresa na koju saljemo proizvod:</p><br>
    <address class="">
      <?=$full_name; ?><br>
      <?=$street; ?><br>
      <?=$city.','.$country; ?><br>
    </address>
    <?php
     include 'includes/footer.php';
  }catch(\Stripe\Error\Card $e){
    // the card has been declined
    echo $e;
  }

   ?>
