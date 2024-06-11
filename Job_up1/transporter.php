<?php
if(isset($_GET['emeet'])){
setcookie("emeet",$_GET['emeet'],time()+8640000, "/","", 0);
session_start();
$_SESSION['emeet']=$_GET['emeet'];
header("Location: emeet.php");
}
?>