<?php
require_once 'core/initialization.php';
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerpartial.php';
include 'includes/leftbar.php';

if (isset($_GET['cat'])) {
  $cat_id=sanitize($_GET['cat']);
}else {
  $cat_id='';
}

$sql = "SELECT * FROM products WHERE categories = '$cat_id'";
$productQ = $db->query($sql);
$category = get_category($cat_id);#var_dump($category);
?>



  <!-- main content -->
   <div class="col-md-8">main

     <div class="row">
       <!-- proizvodi -->
       <div class="col-md-12">
         <h2 class="text-center"><?=$category['parent'].'/'.$category['child']; ?></h2>
       </div>
     </div>
     <!-- ne znam dal treba da ovde ili ispord ovog row pisem while
     zato sto searazlikuje njegov od mog koda   -->
     <div class="row">
      <!-- probacu prvo ispod -->

       <!-- dwotacka je da ne mora sve unutar petlje da se stampa u php u
       tj bolje je nego {  } i onda samo enwhile; -->
       <?php while($product = mysqli_fetch_assoc($productQ)) : ?>
         <!-- proverava da li je proizvod "obrisan" iz baze -->
         <?php if($product['deleted']==0): ?>
           <!-- prvi -->
           <div class="col-md-3">
             <h4><?= $product['title']; ?></h4>
             <?php $photos=explode(',',$product['image']); ?>
             <img src="<?=$photos[0];?>" alt="<?=$product['title']; ?>" class="img-thumbnail"/>
             <!-- zavisno od toga dal ima LIST_PRICE radi jedno od -->
             <?php if($product['list_price']!=0):?>
               <p class="list-price text-danger">Cena:<s>$<?= $product['list_price'];?></s></p>
               <p class="price">Akcijska cena:$<?=$product['price'];?></p>
             <?php else: ?>
               <p class="price">Cena:$<?=$product['price'];?></p>
             <?php endif; ?>
             <button tipe="button" class="btn btn-sm btn-secondary" onclick="detailsmodal(<?=$product['id'];  ?>)">
               Detaljnije</button>
           </div>
         <?php endif; ?>
       <?php endwhile; ?>
     </div>
   </div>
<?php
include 'includes/rightbar.php';
include 'includes/footer.php';
 ?>
