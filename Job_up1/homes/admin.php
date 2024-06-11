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
  <h1>Adminstrator</h1>
</nav>
<iframe name="adu" src="../myday.php"></iframe>
<nav class="navbar fixed-bottom">
<a class="navbar-brand" Href="../myday.php" target="adu">Daily</a>
<a class="navbar-brand" Href="../list.php" target="adu">Employees</a>
<a class="navbar-brand" Href="../task.php" target="adu">Task Manager</a>
<a class="navbar-brand" Href="../contact.php" target="adu">Contacts</a>
<form><button class="navbar-brand btn-warning" name="logout">Logout</button></form>
</nav>
</body>
</html>
<?php
if(isset($_GET['logout'])){
 $fname=$_COOKIE['fname'];
 setcookie( "fname", "Admin", time()-8640000, "/","", 0);
 header("Location: ../login.php");
}
?>