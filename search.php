<?php
require_once 'core/initialization.php';
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerpartial.php';
include 'includes/leftbar.php';

$sql="SELECT * FROM products";
$cat_id=(($_POST['cat']!='')?sanitize($_POST['cat']):'');
if ($cat_id=='') {
 $sql.=" WHERE deleted = 0";
}else{
  $sql.=" WHERE categories ='{$cat_id}' AND deleted = 0";
}
$price_sort=(($_POST['price_sort']!='')?sanitize($_POST['price_sort']):'');
$min_price=(($_POST['min_price']!='')?sanitize($_POST['min_price']):'');
$max_price=(($_POST['max_price']!='')?sanitize($_POST['max_price']):'');
$brand=(($_POST['brand']!='')?sanitize($_POST['brand']):'');
if ($min_price!=''){
  $sql.=" AND price >='{$min_price}'";
}
if ($max_price!=''){
  $sql.=" AND price <='{$max_price}'";
}
if ($brand!=''){
  $sql.=" AND brand ='{$brand}'";
}
if ($price_sort=='low') {
  $sql.=" ORDER BY price";
}
if ($price_sort =='high') {
  $sql.=" ORDER BY price DESC";
}
$productQ = $db->query($sql);
$category = get_category($cat_id);#var_dump($category);
?>



  <!-- main content -->
   <div class="col-md-8">

     <!-- <div class="row"> -->
       <!-- proizvodi -->
       <?php if($cat_id!=''): ?>
       <div class="col-md-12">
         <h2 class="text-center"><?=$category['parent'].'/'.$category['child']; ?></h2>
       </div>
     <?php else: ?>
       <h2 class="text-center">Andjelkine umotvorine</h2>
     <?php endif; ?>
     <!-- </div> -->
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
