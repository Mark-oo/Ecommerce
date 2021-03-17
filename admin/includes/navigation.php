<nav class="navbar navbar-expand-lg  navbar-light bg-light">
 <div class="container">
   <a href="/andjelismrdenoge/admin/index.php" class="navbar-brand">Andjelkine umotvorine Admin</a>
   <ul class="nav navbar-nav">

       <!-- menu items -->
       <li><a href="index.php">Kontrolna tabla</a></li>
      <li><a href="brands.php">Marke</a></li>
      <li><a href="categories.php">Kategorije</a></li>
      <li><a href="products.php">Proizvodi</a></li>
      <?php if (has_permission('bog')): ?>
          <li><a href="users.php">Korisnici</a></li>
      <?php endif; ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" class="dropdown-toggle" data-toggle="dropdown">Vozdra, <?=$user_data['first'];?>!</a>
        <ul class="dropdown-menu" >
          <li><a class="dropdown-item" href="change_password.php">Promeni Lozinku</a></li>
          <li><a class="dropdown-item" href="logout.php">Odjavi se</a></li>
        </ul>
      </li>
      <li><a href="/andjelismrdenoge/index.php"><span class="glyphicon glyphicon-qrcode"></span> Normie only</a></li>
         <!-- moram vratim ono sve sto sam izbacio (drpodown itd) -->
   </ul>
 </div>
</nav>
<hr>
