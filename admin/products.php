<!--
OVDE NE RADI:
FIXED :kad unosim korisnika zahteva sva polja da budu popunjena ili nece ubaciti u DB

FIXED :ne mogu da da ubacim slikq u required izlazi error undefined index
FIXED: ne mogu da editujem stvari


 -->
 <?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php';
  if(!is_logged_in()){
    login_error_redirect();
  }
  include 'includes/head.php';
  include 'includes/navigation.php';


  // DELETE PRODUCT
  if(isset($_GET['delete'])){
    $id=sanitize($_GET['delete']);
    $db->query("UPDATE products SET deleted =1 WHERE id='$id' ");
    header('Location:products.php');
  }

  $dbpath='';
  // ADD OR EDIT PRODUCT
  if(isset($_GET['add']) || isset($_GET['edit'])) {
  $brandQuery=$db->query("SELECT * FROM brand ORDER BY brand ");
  $parentQuery=$db->query("SELECT * FROM categories WHERE parent=0 ORDER BY category ");
  $title=((isset($_POST['title']) && $_POST['title']!='')?sanitize($_POST['title']):'');
  $brand=((isset($_POST['brand']) && !empty($_POST['brand']))?sanitize($_POST['brand']):'');
  $parent=((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent']):'');
  $category=((isset($_POST['child']) && !empty($_POST['child']))?sanitize($_POST['child']):'');
  $price=((isset($_POST['price']) && $_POST['price']!='')?sanitize($_POST['price']):'');

  $list_price=((isset($_POST['list_price']) && $_POST['list_price'] !='')?sanitize($_POST['list_price']):'');

  $description=((isset($_POST['description']) && $_POST['description']!='')?sanitize($_POST['description']):'');
  $sizes=((isset($_POST['sizes']) && $_POST['sizes']!='')?sanitize($_POST['sizes']):'');
  $sizes=rtrim($sizes,',');
  // $imageRequired=((isset($_POST['image']) && $_POST['image']!='')?$_POST['image']:'');
  $saved_image='';
   // EDIT PRODUCT
   if(isset($_GET['edit'])){
     $edit_id=(int)$_GET['edit'];
     $productResults=$db->query("SELECT * FROM products WHERE id ='$edit_id'");
     $product=mysqli_fetch_assoc($productResults);
    // obrisi sliku
     if(isset($_GET['delete_image'])){
       $imgi=(int)$_GET['imgi']-1;
       $images=explode(',',$product['image']);
       $image_url=$_SERVER['DOCUMENT_ROOT'].$images[$imgi];
       unlink($image_url);
       unset($images[$imgi]);
       $imageString=implode(',',$images);
       $db->query("UPDATE products SET image='{$imageString}' WHERE id='$edit_id'");
       header('Location:products.php?edit='.$edit_id);
     }
     $category=((isset($_POST['child']) && $_POST['child']!='')?sanitize($_POST['child']):$product['categories']);
     $title=((isset($_POST['title']) && $_POST['title']!='')?sanitize($_POST['title']):$product['title']);
     $brand=((isset($_POST['brand']) && $_POST['brand']!='')?sanitize($_POST['brand']):$product['brand']);
     $parentQ=$db->query("SELECT * FROM categories WHERE id=$category");
     $parentResult=mysqli_fetch_assoc($parentQ);
     $parent=((isset($_POST['parent']) && $_POST['parent']!='')?sanitize($_POST['parent']):$parentResult['parent']);
     $price=((isset($_POST['price']) && $_POST['price']!='')?sanitize($_POST['price']):$product['price']);
     // ova dva mora da smem da ostavim prazna
     $list_price=(isset($_POST['list_price'])?sanitize($_POST['list_price']):$product['list_price']);
     $description=(isset($_POST['description'])?sanitize($_POST['description']):$product['description']);

     $sizes=((isset($_POST['sizes']) && $_POST['sizes']!='')?sanitize($_POST['sizes']):$product['sizes']);
     $sizes=rtrim($sizes,',');
     $saved_image=(($product['image']!='')?$product['image']:'');
     $dbpath=$saved_image;

    }
   if(!empty($sizes)) {
     $sizeString=sanitize($sizes);
     $sizeString=rtrim($sizeString,',');
     $sizesArray= explode(',',$sizeString);
     $sArray=array();
     $qArray=array();
     $tArray=array();
     foreach($sizesArray as $ss){
       $s=explode(':',$ss);
       $sArray[]=$s[0];
       $qArray[]=$s[1];
       $tArray[]=$s[2];
     }
   }else {$sizesArray=array();}

  if ($_POST) {
    // $list_price=sanitize($_POST['list_price']);
    // $description=sanitize($_POST['description']);
    // var_dump($list_price,$description);
    $errors=array();

    $required=array('title', 'brand', 'price', 'parent', 'child', 'sizes');
    $allowed=array('png','jpeg','jpg','gif');
    // $photoName=array();
    $tempLoc=array();
    $uploadpath=array();
    foreach ($required as $field) {
      // cheque if *filelds are field
      if($_POST[$field]==''){
        $errors[]='Polja sa zvezdicom su obavezna';
        break;
      }
    }
    var_dump($_FILES['photo']);
    $photoCount=count($_FILES['photo']['name']);
    if($photoCount >0){
      for($i=0;$i<$photoCount; $i++){
        $name=$_FILES['photo']['name'][$i];
        $nameArray=explode('.',$name);
        $fileName=$nameArray[0];
        $fileExt=((isset($nameArray[1]))? $nameArray[1] : null);
        $mime=explode('/',$_FILES['photo']['type'][$i]);
        $mimeType=$mime[0];#var_dump($mimeType);
        $mimeExt=((isset($meme[1]))? $meme[1] : null);
        $tmpLoc[]=$_FILES['photo']['tmp_name'][$i];
        $fileSize=$_FILES['photo']['size'][$i];
        $uploadName=md5(microtime().$i).'.'.$fileExt;
        $uploadPath[]=BASEURL.'images/products/'.$uploadName;
        if($i!=0){
          $dbpath.=',';
        }
        $dbpath.='/andjelismrdenoge/images/products/'.$uploadName;
         // var_dump($uploadPath);
         // var_dump($tmpLoc);
         // akko nije ubacena slika da ne lupa ove errore
         if(empty($_FILES)){
            // cheque if file is img
            if ($mimeType != 'image' ) {
              $errors[]='Fajl mora da bude kujsli.';
            }
            // file tipe cheque
            if (!in_array($fileExt,$allowed)) {
              $errors[]='Slika mora da bude png,jpg,jpeg ili gif';
            }
          }
        // file size cheque
        if($fileSize > 15000000){
          $errors[]='Fajl mora biti manj od 15MB';
        }
        // cheque if file isnt altered
        if ($fileExt !=$mimeExt && ($mimeExt=='jpeg' && $fileExt !='jpg')) {
          $errors[]='Format fajla se ne poklapa sa tipom fajla';
        }
      }
    }
    // sleep(120);
    if(!empty($errors)) {
      // move_uploaded_file($tmpLoc,$uploadPath);
      // sleep(120);
       echo display_errors($errors);
    }else{
      // upload filoe and insert to DB
      if ($photoCount>0) {
        for($i=0;$i<$photoCount;$i++){
          move_uploaded_file($tmpLoc[$i],$uploadPath[$i]);
        }
      }
      $insertSql="INSERT INTO products  (`title`,`price`,`list_price`,`brand`,`categories`,`sizes`,`image`,`description`)
      VALUES ('$title', '$price', '$list_price', '$brand', '$category', '$sizes', '$dbpath', '$description')";
      if(isset($_GET['edit'])){
        $insertSql="UPDATE products SET title='$title',price='$price',list_price='$list_price',
        brand='$brand',categories='$category',sizes='$sizes',image='$dbpath',description='$description'
        WHERE id='$edit_id'";
      }

      $db->query($insertSql);
      header('Location: products.php');
    }
  }
  ?>
  <!-- FORM -->
    <hr><h2 class="text-center" ><?=((isset($_GET['edit']))?'Izmeni':'Dodaj nov'); ?> proizvod</h2><hr>
     <form  action="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="post" enctype="multipart/form-data">
       <!-- TITLE -->
       <div class="form-group col-md-3">
         <label for="title">Title*:</label>
         <input type="text" name="title" class="form-control" id="title" value="<?=$title?>">
       </div>
       <!-- BRAND -->
       <div class="form-group col-md-3">
         <label for="brand">Marka*:</label>
         <select class="form-control" id="brand" name="brand">
           <option value=""<?=(($brand == '')?' selected':''); ?>></option>
           <?php while ($b=mysqli_fetch_assoc($brandQuery)):?>
             <option value="<?=$b['id'];?>"<?=(($brand == $b['id'])?' selected':'');?>><?=$b['brand'];?></option>
           <?php endwhile;?>
         </select>
       </div>
       <!-- PARENT -->
       <div class="form-group col-md-3">
         <label for="parent">Parent Category*:</label>
         <select class="form-control" id="parent" name="parent">
           <option value=""<?=(($parent == '')?' selected':''); ?>></option>
           <?php while($p=mysqli_fetch_assoc($parentQuery)) : ?>
               <option value="<?=$p['id'];?>"<?=(($parent == $p['id'])?' selected':''); ?>><?=$p['category'];?></option>
           <?php endwhile; ?>
         </select>
       </div>
       <!--CHILD -->
       <div class="form-group col-md-3">
         <label for="child">Child Category*:</label>
         <select class="form-control" id="child" name="child"></select>
       </div>
       <!-- PRICE -->
       <div class="form-group col-md-3">
         <label for="price">Price*:</label>
         <input type="text" class="form-control" name="price" id="price" value="<?=$price ;?>">
       </div>
       <!-- LIST PRICE -->
       <div class="form-group col-md-3">
         <label for="list_price">List Price:</label>
         <input type="text" class="form-control" name="list_price" id="list_price" value="<?=$list_price; ?>">
       </div>
       <!-- BUTTON -->
       <div class="form-group col-md-3">
         <label>Quantity & Sizes*:</label>
         <button  class="btn btn-outline-secondary form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>
       </div>
       <!--  SIZES & QUANTITY PREVIEW-->
       <div class="form-group col-md-3">
         <label for="sizes">Sizes & Quantity Preview</label>
         <input type="text" name="sizes" class="form-control" id="sizes" value="<?=$sizes; ?>" readonly>
       </div>
       <!-- IMAGE -->
       <div class="form-grouo col-md-6">
         <?php if($saved_image!=''): ?>
           <?php
           $imgi=1;
           $images=explode(',',$saved_image);?>
           <?php foreach($images as $image): ?>
           <div class="saved-image col-md-4">
             <img src="<?=$image;?>" alt="saved image"><br>
             <a href="products.php?delete_image=1&edit=<?=$edit_id;?>&imgi=<?=$imgi;?>" class="text-danger">Delete image</a>
           </div>
         <?php
         $imgi++;
         endforeach; ?>
         <?php else : ?>
         <label for="photo">Product Photo:</label>
         <input type="file" name="photo[]" id="photo" class="form-control" multiple >
       <?php endif; ?>
       </div>
      <!-- DESCRIPTION -->
       <div class="form-group col-md-6">
         <label for="description">Description:</label>
         <textarea id="description" name="description" class="form-control" rows="6"><?=$description; ?></textarea>
       </div>
       <!-- BUTTONS -->
       <div class="form-group pull-right">
         <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
         <input type="submit" class=" btn btn-secondary " value="<?=((isset($_GET['edit']))?'Izmeni':"Dodaj nov");?> Proizvod">
       </div><div class="clearfix"></div>
     </form>
     <!-- MODAL -->
     <div class="modal" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-lg">
         <div class="modal-content">
           <div class="modal-header">
             <h4 class="modal-title" id="sizesModalLabel">Size & Quantity</h4>
             <button type="button" class="close" onclick="closeModal();" data-dissmis="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
             </button>
           </div>
           <div class="modal-body">
              <?php for($i=1;$i<=12;$i++): ?>
                <div class="from-group col-md-2">
                  <label for="size<?=$i;?>">Size:</label>
                  <input class="form-control" type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:'');?>">
                </div>
                <div class="from-group col-md-2">
                  <label for="quantity<?=$i;?>">Quantity:</label>
                  <input class="form-control" type="number" name="quantity<?=$i;?>" id="quantity<?=$i;?>" value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:'');?>" min="0">
                </div>
                <div class="from-group col-md-2">
                  <label for="threshold<?=$i;?>">???:</label>
                  <input class="form-control" type="number" name="threshold<?=$i;?>" id="threshold<?=$i;?>" value="<?=((!empty($tArray[$i-1]))?$tArray[$i-1]:'');?>" min="0">
                </div>
              <?php endfor; ?>
           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-light" onclick="closeModal();" data-dissmis="modal">Close</button>
             <button type="button" class="btn btn-secondary" onclick="updateSizes();jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
           </div>
         </div>
       </div>
     </div>
  <?php }else {

  $sql="SELECT * FROM products WHERE deleted = 0";
  $presults=$db->query($sql);
  if(isset($_GET['featured'])){
    $id= (int)$_GET['id'];
    $featured= (int)$_GET['featured'];
    $featured_sql="UPDATE products SET featured= '$featured' WHERE id= '$id'";
    $db->query($featured_sql);
    header('Location: products.php');
  }
  ?>
  <h2 class="text-center">Products</h2>
  <a href="products.php?add=1" class="btn btn-dark pull-right" id="add-product-btn">Dodaj Proizvod</a><div class="clearfix"></div>
  <hr>
  <table class="table table-bordered table-condensed table-striped">
    <thead class="thead-dark">
     <th></th>
     <th>Product</th>
     <th>Price</th>
     <th>Category</th>
     <th>Featured</th>
     <th>Sold</th>
    </thead>
    <tbody>
     <?php while($product=mysqli_fetch_assoc($presults)) :
        $childID= $product['categories'];
        $cat_sql="SELECT * FROM categories WHERE id = '$childID'";
        $result=$db->query($cat_sql);
        $child=mysqli_fetch_assoc($result);
        $parentID=$child['parent'];
        $parent_sql="SELECT * FROM categories WHERE id = '$parentID'";
        $p_result=$db->query($parent_sql);
        $parent = mysqli_fetch_assoc($p_result);
        $category= $parent['category'].'~'.$child['category'];
       ?>
      <tr>
       <td>

        <a href="products.php?edit=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
        <a href="products.php?delete=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>

       </td>
       <td><?=$product['title'];?></td>
       <td><?= money($product['price']);?></td>
       <td><?=$category ;?></td>
       <td><a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?=$product['id'];?> " class="btn btn-xs btn-default">
          <span class="glyphicon glyphicon-<?=(($product['featured'] == 1)?'minus':'plus');?>"></span>
           </a> &nbsp <?=(($product['featured'] == 1)?'Featured Product':'');?></td>
       <td>0</td>
      </tr>
     <?php endwhile; ?>
    </tbody>
  </table>


  <?php } include 'includes/footer.php'; ?>
  <script>
    jQuery('document').ready(function(){
      get_child_options('<?=$category;?>');
    });
  </script>
