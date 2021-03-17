<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/andjelismrdenoge/core/initialization.php';
unset($_SESSION['SBUser']);
header('Location:login.php');

?>
