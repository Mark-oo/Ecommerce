<?php
require_once '../core/initialization.php';

$id = $_POST['id'];
// var_dump($id);
$id=(int)$id;
$sql="SELECT * FROM products WHERE id = '$id'";
$result=$db->query($sql);
$product=mysqli_fetch_assoc($result);
$brand_id=$product['brand'];
$sql="SELECT brand FROM brand WHERE id='$brand_id'";
$brand_query=$db->query($sql);
$brand = mysqli_fetch_assoc($brand_query);
$sizestring=$product['sizes'];
$sizestring=rtrim($sizestring,',');
$size_array= explode(',',$sizestring); //ova explode f-ja stvari redja na intervalu znaka ili cega ves joj das da trazi
 ?>
<!-- ob start i ob get clean sluze da on uzme sve ovo u trenutku
kad udje ovde i onda baci sve kad stigne do kraja , ovako je efikasnije -->
<?php ob_start(); ?>
<!-- Details Modal -->
<div class="modal detail-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" arial-hidden="true">
 <div class="modal-dialog modal-lg">
   <div class="modal-content">
    <div class="modal-header">
     <h4 class="modal-title text-center"><?=$product['title'];?></h4>
     <button class="close" tipe="button" onclick="closeModal()" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
      <!-- <?php  var_dump($size_array);?>  -->
    </div>
     <div class="modal-body">
      <div class="container-fluid">
       <!-- <div class="row"> -->
         <div class="col-sm-6 fotorama">
            <?php $photos=explode(',',$product['image']);
            foreach($photos as $photo): ?>
                <img src="<?= $photo;?>" alt="<?= $product['image'];?>" class="details img-responsive">
            <?php endforeach; ?>
          </div>
         <!-- </div> -->
         <div class=" col-sm-6">
           <h4>Opis</h4>
           <p><?=nl2br($product['description']);?></p>
           <hr>
           <p>Cena: $<?=$product['price'];?></p>
           <p>Proizvodjac: <?=$brand['brand'];?></p>
           <!-- FORM -->
           <form  action="add_cart.php" method="post" id="add_product_form">
             <input type="hidden" name="product_id" value="<?=$id;?>">
             <input type="hidden" name="available" id="available" value="">
             <div class="form-group col-xs-6">
                 <label for="quantity">Kolicina:</label>
                 <input type="number" min="0" class="form-control" id="quantity" name="quantity">
             </div>
             <div class="form-group col-xs-6">
               <label for="size">Velicina:</label>
               <select class="form-control" id="size" name="size">
                 <option value=""></option>
                 <?php foreach($size_array as $string){
                   $string_array = explode(':',$string);
                   $size = $string_array[0];
                   $available = $string_array[1];
                   if($available>0){
                        echo '<option value="'.$size.'" data-available="'.$available.'">'.$size.' ('.$available.' na lageru)</option>';
                   }
               } ?>
               </select>
             </div>
           </form>
         </div>
         </div>
       </div>
     <!-- </div> -->
   <div class="modal-footer">
     <span id="modal_errors" class="bg-warning"></span>
     <button class="btn-secondary" type="button" onclick="closeModal()">Zatvori</button>
     <button class="btn-secondary" type="button"  onclick="add_to_cart();return false;"><span class="glyphicon glyphicon-shopping-cart"></span> Dodaj u korpu</button>
   </div>
   </div>
  </div>
 </div>
 <script>
 // slusa dal se size promenio
  jQuery('#size').change(function(){
    var available = jQuery('#size option:selected').data("available");
    jQuery('#available').val(available);
  });

  $(function () {
  $('.fotorama').fotorama({'loop':true,'autoplay' : true});
});

  // onclick close modal
   function closeModal(){
     jQuery('#details-modal').modal('hide');
     setTimeout(function(){
       jQuery('#details-modal').remove();
       jQuery('.modal-backdrop').remove();
     },500);
   }
 </script>
 <?php echo ob_get_clean(); ?>
