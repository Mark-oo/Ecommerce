<?php
  require_once '../core/initialization.php';
  // provera dal si ulogovan
  if(!is_logged_in()){
    login_error_redirect();
  }
  // provera dal si admin akko ne onda te sibne na idex.php
  if(!has_permission('bog')){
    permission_error_redirect('index.php');
  }
  include 'includes/head.php';
  include 'includes/navigation.php';
  // brisanje korisnika iz baze
  if (isset($_GET['delete'])) {
    $delete_id=sanitize($_GET['delete']);
    $db->query("DELETE FROM users WHERE id='$delete_id'");
    $_SESSION['success_flash']='Korisnik obrisan';
    header('Location: users.php');
  }
  // dodavanje korisnika u bazu
  if (isset($_GET['add'])) {
    $name=((isset($_POST['name']))?sanitize($_POST['name']):'');
    $email=((isset($_POST['email']))?sanitize($_POST['email']):'');
    $password=((isset($_POST['password']))?sanitize($_POST['password']):'');
    $confirm=((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
    $permissions=((isset($_POST['permissions']))?sanitize($_POST['permissions']):'');
    $errors=array();

    if ($_POST) {
      $emailQuery=$db->query("SELECT * FROM users WHERE email='$email'");
      $emailCount=mysqli_num_rows($emailQuery);
      // var_dump($emailCount);
      // uzima iz baze mejlove koji se poklapaju sa $email
      // stavlamo u $emailCount
      // proveravamo ako je to $emailCount !=0
      // baca gresku
      if ($emailCount != 0) {
        $errors[]='Ta E.posta vec postoji u nasoj bazi poataka';
      }

      $required=array('name','email','password','confirm','permissions');
      // dal su sva polja popunjena
      foreach ($required as $f ) {
        if (empty($_POST[$f])) {
          $errors[]='Moras popuniti sva polja';
          break;
        }
      }
      // dal je sifra <6
      if (strlen($password) < 6) {
        $errors[]='Sifra mora biti veca od 6 karaktera';
      }
      // dal je password i confirm isto
      if ($password != $confirm) {
        $errors[]='Lozinka i Potvrdi lozinku se ne poklapaju';
      }
      //dal je mejl validan
      if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors[]='Elektronska posta nije validna';
      }
      echo $name;
      echo $password;
      echo $email;
      echo $permissions;
      // evde izlecu greske
      if (!empty($errors)) {
        echo display_errors($errors);
      }else {
        // ubaci korisnika u db
        $hashed=password_hash($password,PASSWORD_DEFAULT);
        $insertSql="INSERT INTO  users (`full_name`,`email`,`password`,`permissions`)
        VALUES ('$name','$email','$hashed','$permissions')";
        $db->query($insertSql);
        $_SESSION['success_flash']='Korisnik je unet u bazu podataka';
        header('Location: users.php');
      }
    }
    ?>
    <h2 class="text-center">Dodaj novog korisnika </h2><hr>
    <form class="" action="users.php?add=1" method="post">
      <!-- IME I PREZIME -->
      <div class="form-group col-md-6">
        <label for="name">Ime i Prezime:</label>
        <input class="form-control" type="text" name="name" id="name" value="<?=$name;?>">
      </div>
      <!-- ELEKTRONSKA POSTA -->
      <div class="form-group col-md-6">
        <label for="email">Elektronska posta:</label>
        <input class="form-control" type="text" name="email" id="email" value="<?=$email;?>">
      </div>
      <!-- LOZINKA -->
      <div class="form-group col-md-6">
        <label for="password">Lozinka</label>
        <input class="form-control" type="password" name="password" id="password" value="<?=$password;?>">
      </div>
      <!-- ELEKTRONSKA POSTA -->
      <div class="form-group col-md-6">
        <label for="confirm">Potvrdi lozinku:</label>
        <input class="form-control" type="password" name="confirm" id="confirm" value="<?=$confirm;?>">
      </div>
      <!-- PERMISSIONS -->
      <div class="form-group col-md-6">
        <label for="email">Ovlascenja:</label>
        <select class="form-control" name="permissions">
          <option value=""<?=(($permissions == '')?'selected':'');?>></option>
          <option value="batina"<?=(($permissions == 'batina')?'selected':'');?>>Batina</option>
          <option value="bog,batina"<?=(($permissions == 'bog,batina')?'selected':'');?>>Bog</option>
        </select>
      </div>
      <!-- DUGMICI -->
      <div class="form-group col-md-6 text-right" id="bio-red-da-probam" >
        <a href="users.php" class="btn btn-default">Cancel</a>
        <input type="submit" class=" btn btn-secondary" value="Dodaj korisnika">
      </div>
    </form>
    <?php
  }else{
  $userQuery=$db->query("SELECT * FROM users ORDER BY full_name");

   ?>
  <h2 class="text-center">Korisnici</h2>
  <a href="users.php?add=1" class="btn btn-dark pull-right" id="add-product-btn">Dodaj novog korisnika</a>
  <hr>
  <table class="table table-sm table-striped table-bordered">
   <thead class="thead-dark">
    <th></th><th>Ime i Prezime</th><th>Elekrtonska posta</th><th>Pocetak koriscenja</th><th>Poslednje koriscenje</th><th>Ovlascenja</th>
   </thead>
   <tbody>
     <?php while($user=mysqli_fetch_assoc($userQuery)): ?>
      <tr>
        <td>
          <?php if($user['id']!=$user_data['id']): ?>
            <a href="users.php?delete=<?=$user['id'];?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove-sign"></span></a>
          <?php endif; ?>
        </td>
        <td><?=$user['full_name'];?></td>
        <td><?=$user['email'];?></td>
        <td><?=pretty_date($user['join_date']);?></td>
        <td><?=((!isset($user['last_login']) == 'NULL')?'Nikad se nije ulogovao': pretty_date($user['last_login']));?></td>
        <td><?=$user['permissions'];?></td>
      </tr>
     <?php endwhile; ?>
   </tbody>
  </table>
 <?php } include 'includes/footer.php'; ?>
