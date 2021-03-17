 <?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php' ;
  if(!is_logged_in()){
    login_error_redirect();
  }
  include 'includes/head.php';
  include 'includes/navigation.php';

  $sql="SELECT * FROM categories WHERE parent= 0";
  $result= $db->query($sql);
  $errors = array();
  $category='';
  $post_parent='';

  // edit category
  if (isset($_GET['edit']) && !empty($_GET['edit'])){
    $edit_id=(int)$_GET['edit'];
    $edit_id=sanitize($edit_id);
    $edit_sql="SELECT * FROM categories WHERE id = '$edit_id'";
    $edit_result=$db->query($edit_sql);
    $edit_category=mysqli_fetch_assoc($edit_result);
  }
  // delete category
  if (isset($_GET['delete']) && !empty($_GET['delete'])){
   $delete_id = (int)$_GET['delete'];
   $delete_id = sanitize($delete_id);
   $sql= "SELECT * FROM categories WHERE id='$delete_id'";
   $result= $db->query($sql);
   $category=mysqli_fetch_assoc($result);
   if ($category['parent'] == 0) {
     $sql="DELETE FROM categories WHERE parent='$delete_id'";
     $db->query($sql);
   }
   $dsql= "DELETE FROM categories WHERE id ='$delete_id'";
   $db->query($dsql);
   header('Location: categories.php');
  }

  // process form
  if (isset($_POST) && !empty($_POST)){
    $post_parent= sanitize($_POST['parent']);
    $category= sanitize($_POST['category']);
    $sqlform= " SELECT * FROM categories WHERE category='$category' AND parent='$post_parent'";
    if (isset($_GET['edit'])) {
      $id = $edit_category['id'];
      $sqlform="SELECT * FROM categories WHERE category='$category' AND parent='$post_parent' AND id !='$id' ";
    }
    $fresult=$db->query($sqlform);
    $count= mysqli_num_rows($fresult);
    // if category blank
    if($category ==''){
      $errors[].= 'Ne smes ostaviti prazno polje  kategorija';
    }
    // if exist in DB
    if($count > 0){
      $errors[].=$category.' vec postoji';
    }

     // display errors or update DB
    if (!empty($errors)) {
      // dysplay errors
      $display=display_errors($errors); ?>
      <script>
       jQuery('document').ready(function(){
         jQuery('#errors').html('<?=$display; ?>');
       });
      </script>
      <?
    }else {
      // update DB
      $updatesql=" INSERT INTO categories (category,parent) VALUES ('$category','$post_parent') ";
      if (isset($_GET['edit'])) {
        $updatesql= "UPDATE categories SET category='$category', parent='$post_parent' WHERE id='$edit_id'";
      }
      $db->query($updatesql);
      header('Location:categories.php');
    }

  }
  $category_value='';
  $parent_value=0;
  if (isset($_GET['edit'])) {
    $category_value=$edit_category['category'];
    $parent_value=$edit_category['parent'];
  }else {
    if (isset($_POST)) {
      $category_value=$category;
      $parent_value=$post_parent;
    }
  }
?>
<h2 class="text-center">Kategorije</h2><hr>
<div class="row">

  <!-- form -->
  <div class="col-md-6">
   <form class="form" action="categories.php<?=((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
    <legend><?=((isset($_GET['edit']))?'Izmeni':'Dodaj');?> kategoriju</legend>
    <div id="errors"></div>
    <div class="form-group">
      <label for="parent">Za</label>
      <select class="form-control" name="parent" id="parent">
        <option value="0"<?=(($parent_value == 0)?'selected="selected"':'');?>>Za</option>
        <?php while($parent=mysqli_fetch_assoc($result)) : ?>
          <option value="<?=$parent['id'];?>"<?=(($parent_value == $parent['id'])?'selected="selected"':'');?>><?=$parent['category'];?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="category">Kategorija</label>
      <input type="text" class="form-group" id="category" name="category" value="<?=$category_value;?>" >
    </div>
    <div class="form-group">
      <input type="submit" value="<?=((isset($_GET['edit']))?'Izmeni':'Dodaj');?> Category" class="btn btn-dark">
    </div>
   </form>
  </div>

  <!-- category table -->
  <div class="col-md-6">
    <table class="table table-bordered">
      <thead>
        <th>Kategorija</th><th>Za</th><th></th>
      </thead>
      <tbody>
           <?php
             $sql="SELECT * FROM categories WHERE parent=0";
             $result= $db->query($sql);
             while($parent = mysqli_fetch_assoc($result)) :
             $parent_id=(int)$parent['id'];
             $sql2 = "SELECT * FROM categories WHERE parent ='$parent_id'";
             $childresult=$db->query($sql2);
           ?>
          <tr class="bg-secondary">
           <td><?= $parent['category'] ;?></td>
           <td>za</td>
           <td>
             <a href="categories.php?edit=<?= $parent['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
             <a href="categories.php?delete=<?= $parent['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
           </td>
          </tr>
          <?php while($child=mysqli_fetch_assoc($childresult)) : ?>
            <tr class="bg-default">
             <td><?=$child['category'];?></td>
             <td><?=$parent['category'];?></td>
             <td>
               <a href="categories.php?edit=<?= $child['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
               <a href="categories.php?delete=<?= $child['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>
             </td>
            </tr>
          <?php endwhile; ?>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>