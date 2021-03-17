<!-- top nav bar -->
<?php
// $time=time();
// setcookie("test", "value", time()+86400);
// $varname = $_COOKIE["test"];
$sql = "SELECT * FROM categories WHERE parent= 0";
$parentquery = $db->query($sql);
// var_dump($parentquery);
// var_dump(mysqli_fetch_assoc($parentquery));
 ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
   <a href="index.php" class="navbar-brand">
    <img src="/andjelismrdenoge/images/headerlogo/logo2.png" width="50" height="50" class="d-inline-block align-middle" alt="">
    Andjelkine umotvorine
   </a>
   <ul class="nav navbar-nav">

     <?php while ($parent = mysqli_fetch_assoc($parentquery)):?>
       <?#= var_dump($parent); ?>
       <?php
        $parent_id = $parent['id'];
        $sql2 = "SELECT * FROM categories WHERE parent ='$parent_id'";
        $childquery= $db->query($sql2);
        ?>
       <!-- menu items -->
       <li class="nav-item dropdown">
         <button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown"><?php echo $parent['category'] ;?></button>
         <ul class="dropdown-menu">
      <!-- alternativna za dropdown meni -->
        <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">Muskarci<span class="caret"></span></a> -->
          <!-- <ul class="dropdown-menu"> role="menu"> -->
          <?php while($child = mysqli_fetch_assoc($childquery)): ?>
           <li><a  class="dropdown-item" href="category.php?cat=<?=$child['id'];?>"><?php echo $child['category'] ;?></a></li>
          <?php endwhile; ?>
         </ul>
      </li>
     <?php endwhile ; ?>
     <li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span> Moja kolica</a></li>
     <li><a href="admin/index.php"><span class="glyphicon glyphicon-qrcode"></span> Chad only</a></li>
   </ul>
 </div>
</nav>
