<?php
  require_once '../core/initialization.php';
  if(!is_logged_in()){
    header('Location: login.php');
  }
  include 'includes/head.php';
  include 'includes/navigation.php';
 // COMPLETE ORDER
  if (isset($_GET['complete']) && $_GET['complete']==1) {
    $cart_id=sanitize((int)$_GET['cart_id']);
    $db->query("UPDATE cart SET shipped=1 WHERE id='{$cart_id}'");
    $_SESSION['success_flash']="Narudzbina je prosledjena.";
    header('Location: index.php');
  }


  $txn_id=sanitize((int)$_GET['txn_id']);
  $txnQuery=$db->query("SELECT * FROM transactions WHERE id='{$txn_id}'");
  $txn=mysqli_fetch_assoc($txnQuery);
  $cart_id=$txn['cart_id'];
  $cartQ=$db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
  $cart=mysqli_fetch_assoc($cartQ);
  $items=json_decode($cart['items'],true);
  $idArray=array();
  $products=array();
  foreach($items as $item){
    $idArray[]=$item['id'];
  }
  $ids=implode(',',$idArray);
  $productQ=$db->query(
   "SELECT i.id as 'id', i.title as 'title', c.id as 'cid', c.category as 'child', p.category as 'parent'
    FROM products i
    LEFT JOIN categories c ON i.categories = c.id
    LEFT JOIN categories p ON c.parent=p.id
    WHERE i.id IN ({$ids})
   ");
   while($p=mysqli_fetch_assoc($productQ)){

     foreach($items as $item){
       if($item['id'] == $p['id']){
         $x=$item;
         continue;
       }
     }
     $products[]=array_merge($x,$p);
   }
 ?>
 <h2 class="text-center">Naruceni Artikli</h2>
 <table class="table table-condensed table-bordered table-striped">
  <thead>
    <th>Kolicina</th>
    <th>Naziv</th>
    <th>Kategorija</th>
    <th> Velicina</th>
  </thead>
  <tbody>
    <?php foreach($products as $product): ?>
      <tr>
        <td><?=$product['quantity'];?></td>
        <td><?=$product['title'];?></td>
        <td><?=$product['parent'].'~'.$product['child'];?></td>
        <td><?=$product['size'];?></td>
      </tr>
  <?php endforeach; ?>
  </tbody>
 </table>
 <div class="row">
   <div class="col-md-6">
     <h3 class="text-center">Detalji narudzbine</h3>
     <table class="table table-condensed table-striped table-bordered">
       <tbody>
         <tr>
           <td>Cena</td>
           <td><?money($txn['total_price']);?></td>
         </tr>
         <tr>
           <td>KUPON</td>
           <td><?=money($txn['kupon']);?></td>
         </tr>
         <tr>
           <td>Cena sa kuponom</td>
           <td><?=money($txn['final_price']);?></td>
         </tr>
         <tr>
           <td>Datum porudzdbine</td>
           <td><?=pretty_date($txn['txn_date']);?></td>
         </tr>
       </tbody>
     </table>
   </div>
   <div class="col-md-6">
    <h3 class="text-center">Adresa</h3>
    <address>
      <?=$txn['full_name'];?><br>
      <?=$txn['street'];?><br>
      <?=$txn['city'];?><br>
      <?=$txn['country'];?><br>
    </address>
   </div>
 </div>
 <div class="pull-right">
   <a href="index.php" class="btn btn-large btn-secondary">Nazad</a>
   <a href="orders.php?complete=1&cart_id=<?=$cart_id;?>" class="btn btn-dark btn-large">Prodsledi narudzbinu</a>
 </div>

<?php include 'includes/footer.php'; ?>
