<?php
include "../class.php";
$employ=new employee;
$attend=new attend;
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "../css/bootstrap.min.css" rel = "stylesheet">
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src = "../js/bootstrap.min.js"></script>
    <link href="include/home.css" rel="stylesheet">
</head>
<body>
<nav class="navbar fixed-top container">
  <h1>Attendance</h1>
</nav>
<iframe name="att" src="../daily.php"></iframe>
<nav class="navbar fixed-bottom">
<a class="navbar-brand" href="../daily.php"  target="att">Daily</a>
<a class="navbar-brand" href="../attdb.php"  target="att">Database</a>
<a class="navbar-brand" href="../regi.php"  target="att">Registrar</a>
<a class="navbar-brand" href="../client.php"  target="att">Client</a>
<a class="navbar-brand" href="../contact.php" target="att">Contacts</a>
<form><button class="navbar-brand btn-warning" name="logout">Logout</button></form>
</nav>
</body>
</html>
<?php
if(isset($_GET['logout'])){
 $fname=$_COOKIE['fname'];
 setcookie("fname","",time()-1);
 header("Location: ../login.php");
}
?>