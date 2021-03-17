<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php';
if(!is_logged_in()){
   login_error_redirect();
}
include 'includes/head.php';

$hashed=$user_data['password'];
$old_password=((isset($_POST['old_password']))?sanitize($_POST['old_password']):'');
$old_password=trim($old_password);

$password=((isset($_POST['password']))?sanitize($_POST['password']):'');
$password=trim($password);

$confirm=((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
$confirm=trim($confirm);
$newhashed=password_hash($password,PASSWORD_DEFAULT);
$user_id=$user_data['id'];

$errors=array();
?>

<style >
  body{
    background-image: url(/andjelismrdenoge/images/headerlogo/catto1.jpg);
     background-size: 100vw 100vh;
     background-attachment: fixed;
  }
</style>

<div id="login-form">
  <!-- ovde proverava i greske izlaze -->
  <div>

  <?
    if ($_POST) {
      // FORM VALIDATION
      if(empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])) {
        $errors[] = 'Sva polja moraju biti popunjena';
      }
      // PASSWORD <6
      if(strlen($password) <6 ){
        $errors[]='Lozinka mora bit veca od 6 karaktera';
      }
      // if new matches CONFIRM
      if ($password !=$confirm) {
        $errors[]='Nova lozinka i Potvrdi nova lozinka se ne poklapaju';
      }
      // Proveda dal walja sifra
      if (!password_verify($old_password,$hashed)) {
        $errors[]='Ne valja stara lozinka';
      }

      // CHEQUE ERRORS
      if (!empty($errors)) {
        echo display_errors($errors);
      }else{
        // change password
        $db->query("UPDATE users SET password ='$newhashed' WHERE id='$user_id'");
        $_SESSION['success_flash']='Vasa lozinka je promenjena';
        header('Location: index.php');
      }
    }
  ?>

  </div>

  <hr><h2 class="text-center">Promeni lozinku</h2><hr>
  <!-- Form for login -->
  <form action="change_password.php" method="post">
    <!-- OLD PASSWORD -->
    <div class="form-group">
      <label for="old_password">Stara lozinka:</label>
      <input type="password" name="old_password"  id="old_password" class="form-control" value="<?=$old_password;?>">
    </div>
    <!--NEW PASSWORD -->
    <div class="form-group">
      <label for="password">Nova lozinka:</label>
      <input type="password" name="password"  id="password" class="form-control" value="<?=$password;?>">
    </div>
    <!-- CONFIRM NEW PASSWORD -->
    <div class="form-group">
      <label for="confirm">Potvrdi novu lozinku:</label>
      <input type="password" name="confirm"  id="confirm" class="form-control" value="<?=$confirm;?>">
    </div>
    <!-- SUBMIT -->
    <div class="form-group">
      <a href="index.php" class="btn btn-default">Cancel</a>
      <input type="submit" class="btn btn-primary" value="Change">
    </div>
  </form>
  <!-- LINK TO FRONT PAGE -->
  <p class="text-right"><a href="/andjelismrdenoge/index.php" alt="home">Visit Site</a></p>
</div>

<?php include 'includes/footer.php'; ?>
