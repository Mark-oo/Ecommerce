<!--TRENUTNO NE RADI:

FIXED :ako pokusam login bez Mejla izlazi error undefined index
kontam da je zbog toga sto nema sa cim da uporedi password
 koji unosim ili jos nisam uneo

-->
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php';
include 'includes/head.php';
$email=((isset($_POST['email']))?sanitize($_POST['email']):'');
$email=trim($email);
$password=((isset($_POST['password']))? sanitize($_POST['password']) : '');
$password=trim($password);
$passwordVerify=isset($password)? $password : '';
$errors=array();
?>

<style >
  body{
    background-image: url(/andjelismrdenoge/images/headerlogo/hortenzija.jpg);
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
      if(empty($_POST['email']) || empty($_POST['password'])) {
        $errors[] = 'Djes poso bez emejla i sifre';
      }

      // VALIDATE EMAIL
      if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $errors[]='Moras uneti validan mejl';
      }

      // PASSWORD <6
      if(strlen($password) <6 ){
        $errors[]='Lozinka mora bit veca od 6 karaktera';
      }
      // ovo gleda jel unet mejl da ne bi dole iskakao error
      if(!empty($_POST['email'])){
        // DB EMAIL CHEQUE
        // sortiranje
        $query=$db->query("SELECT * FROM users WHERE email = '$email'");
        $user=mysqli_fetch_assoc($query);
        $userCount=mysqli_num_rows($query);
        // provera
        if($userCount < 1) {
          $errors[]='Taj mejl ne postoji u bazi poataka';
        }
        // dal valja sifra
        if (!password_verify($passwordVerify,$user['password'])) {
          $errors[]='Ne valja sifra';
        }
      }
      // CHEQUE ERRORS
      if (!empty($errors)) {
        // var_dump($password);
        // var_dump($passwordVerify);
        // echo $user['password'];
        echo display_errors($errors);
      }else{
        // LOG IN
        $user_id=$user['id'];
        login($user_id);
      }
    }
  ?>

  </div>

  <hr><h2 class="text-center">Login</h2><hr>
  <!-- Form for login -->
  <form action="login.php" method="post">
    <!-- EMAIL -->
    <div class="form-group">
      <label for="email">Elektronska posta:</label>
      <input type="text" name="email"  id="email" class="form-control" value="<?=$email;?>">
    </div>
    <!-- PASSWORD -->
    <div class="form-group">
      <label for="password">Lozinka:</label>
      <input type="password" name="password"  id="password" class="form-control" value="<?=$password;?>">
    </div>
    <!-- SUBMIT -->
    <div class="form-group">
      <input type="submit" class="btn btn-primary" value="Login">
    </div>
  </form>
  <!-- LINK TO FRONT PAGE -->
  <p class="text-right"><a href="/andjelismrdenoge/index.php" alt="home">Visit Site</a></p>
</div>

<?php include 'includes/footer.php'; ?>
