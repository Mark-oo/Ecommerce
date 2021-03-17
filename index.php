<?php
require_once 'core/initialization.php';
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerfull.php';
include 'includes/leftbar.php';
$sql = "SELECT * FROM products WHERE featured = 1";
$featured = $db->query($sql);

// $cookie_name = "user";
// $cookie_value = "John Doe";
// setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/", false, false, false);
// setcookie("dusan","dusan",CART_COOKIE_EXPIRE,'/',"",false);
?>



  <!-- main content -->
   <div class="col-md-8">main

     <div class="row">
     <!-- proizvodi -->
     <div class="col-md-12">
     <h2 class="text-center">Kolekcija Jesen-Zima 2020</h2></div>
     </div>
          <!-- ne znam dal treba da ovde ili ispord ovog row pisem while
           zato sto searazlikuje njegov odmog koda -->
     <div class="row">
      <!-- probacu prvo ispod -->
      <?php
      // dwotacka je da ne mora sve unutar petlje da se stampa u php u
      // tj bolje je nego {  } i onda samo enwhile;
       while($product = mysqli_fetch_assoc($featured)) :
       ?>

       <!-- prvi -->
       <div class="col-md-3">
         <h4><?= $product['title']; ?></h4>
         <?php $photos=explode(',',$product['image']); ?>
         <img src="<?=$photos[0];?>" alt="<?=$product['title']; ?>" class="img-thumbnail"/>
         <!-- zavisno od toga dal ima LIST_PRICE radi jedno od -->
         <?php if($product['list_price'] != 0):?>
          <p class="list-price text-danger">Cena:<s>$<?=$product['list_price'];?></s></p>
          <p class="price">Akcijska Cena:$<?=$product['price'];?></p>
         <?php else:?>
          <p class="price">Cena:$<?=$product['price'];?></p>
         <?php endif; ?>
         <button tipe="button" class="btn btn-sm btn-secondary" onclick="detailsmodal(<?=$product['id'];?>)">
           Detaljnije</button>
       </div>

     <?php endwhile; ?>

     </div>
   </div>
<?php

include 'includes/rightbar.php';
include 'includes/footer.php';
 ?>
