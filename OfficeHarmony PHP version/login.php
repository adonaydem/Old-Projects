<?php
include "class.php";
$employ=new employee;
$message=new message;
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src = "js/bootstrap.min.js"></script>
      <script src="include/jquery-3.4.1.js"></script>
      <style type="text/css">
      .header{
      width:100%;
      height:200px;
      color:white;
      background:url("logo.jpeg");
      text-align:center;
      }
      .header h1{
      color:#3d9970;
      }
      .navbar .bottom{
      background:#eee;
      border-top:10px gray solid;
      height:20px;
      }
      th{
      background:#0074d9;
      color:white;
      }
      .view-adm, .view-emp, .view-att, .view-acc{
       display:none;
      }
      </style>
      <script type="text/javascript">
      $(function() {
       var admin=$(".view-adm").text();
       if(admin!=0){
        alert(admin);
       }
       var emp=$(".view-emp").text();
       if(emp!=0){
       alert(emp);
       }
       var att=$(".view-att").text();
       if(att!=0){
       alert(att);
       }
       var acc=$(".view-acc").text();
       if(acc!=0){
       alert(acc);
       }
      });
      </script>
</head>
<body>
<div class="jumbotron header">
</div>
<h2 style="text-align:center">Login</h2><hr>
<table class="table table-bordered table-striped">
<tr>
<th>Admin</th><th>Employeee</th>
</tr>
<tr>
<td>
<form method="post">
<input type="text" class="form-control" name="Afname" placeholder="Username" required><br>
<input type="Password" class="form-control" name="Apsw" placeholder="Password" required><br>
<p id="adm"></p>
<button type="submit" class="btn btn-success" name="admin">Done</button>
</form>
</td>
<td>
<form method="post">
<input type="text" class="form-control" name="fname" placeholder="Username" required><br>
<input type="password" class="form-control" name="Epsw" placeholder="Password" required><br>
<p id="emp"></p>
<button type="submit" class="btn btn-success" name="employ">Done</button>
</form>
</td>
</tr>
<tr><th>Attendance</th><th>Accountant</th></tr>
<tr>
<td>
<form method="post">
  <input type="text" class="form-control" name="Atfname" placeholder="Username" required><br>
<input type="Password" class="form-control" name="Atpsw" placeholder="Password" required><br>
<p id="att"></p>
<button type="submit" class="btn btn-success" name="attend">Done</button>
</form>
</td>
<td>
<form method="post">
<input type="text" class="form-control" name="Acfname" placeholder="Username" required><br>
<input type="Password" class="form-control" name="Acpsw" placeholder="Password" required><br>
<p id="acc"></p>
<button type="submit" class="btn btn-success" name="acc">Done</button>
</form></td>
</tr>
</table>
<nav class="navbar bottom">
 <table>
  <tr>
   <td>
   <h3>Report A Problem</h3><hr>
   <form>
   <?php
   $employ->selectAll();
   if(mysqli_num_rows($employ->result)==0){
    echo "No Employees Registered";
   }else{
   echo '
   From:<select name="from" class="form-control">';
   echo "<option>Admin</option><option>Attendant</option>";
   while($row=mysqli_fetch_assoc($employ->result)){
    echo "<option>".$row['fname']."</option>";
   }
   echo '</select><br>
   To:<select name="to" class="form-control">
    <option value="Attend">Attendance</option>
   </select><br>
   Type:<Select name="content" class="form-control">
    <option>Name</option>
    <option>Password</option>
   </select><br>
   <button type="submit" name="send" class="btn btn-info">Send</button>';
   }
   ?>
   </form>
   </td>
  </tr>
 </table>
</nav>
</body>
</html>
<?php
if(isset($_POST['admin'])){
 $admin=new assist;
 $admin->selectOne("role","Admin");
 $fname=$_POST['Afname'];
 $psw=$_POST['Apsw'];
 if(mysqli_num_rows($admin->result)>0){
  if($row=mysqli_fetch_assoc($admin->result)){
   if($row['fname']!=$fname){
     echo "<p class='view-adm'>Name Not Found!</p>"; 
   }else{
   if($row['psw']==$psw){
    header("Location: homes/admin.php?role=admin");
    setcookie( "id", "Admin", time()+8640000, "/","", 0);
    setcookie( "fname", "Admin", time()+8640000, "/","", 0);
   }else{
    echo "<p class='view-adm'>Incorrect Password!</p>";
   }
 }
  }
 }else{
  echo "<p class='view-adm'>An Admin Is Not Set Yet!</p>";
 }
}else if(isset($_POST['employ'])){
 $fname=$_POST['fname'];
 $psw=md5($_POST['Epsw']);
 $employ=new employee;
 $employ->selectOne("fname",$fname);
 if(mysqli_num_rows($employ->result)==0){
  echo "<p class='view-emp'>Name Not Found!</p>";
 }else{
  if($row=mysqli_fetch_assoc($employ->result)){
   if($row['psw']!=$psw){
    echo "<p class='view-emp'>Incorrect Password!</p>";
   }else{
    header("Location: homes/employee.php?role=employ");
    setcookie( "fname", $row['fname'], time()+8640000, "/","", 0);
    setcookie( "id", $row['id'], time()+8640000, "/","", 0);
   }
  }
 }
}else if(isset($_POST['attend'])){
$psw=$_POST['Atpsw'];
$fname=$_POST['Atfname'];
$attend=new assist;
$attend->selectOne("role","Attendance");
 if(mysqli_num_rows($attend->result)==0){
  echo "<p class='view-att'>Attendant Not Set Yet!</p>";
 }else{
  if($row=mysqli_fetch_assoc($attend->result)){
  if ($row['fname']!=$fname) {
    echo "<p class='view-att'>Name Not Found!</p>";
  } else {
   if($row['psw']=$psw){
   setcookie( "fname", "Attend", time()+8640000, "/","", 0);
    header("Location: homes/attend.php?role=attend");
    
   }else{
    echo "<p class='view-att'>Incorrect Password!</p>";
   }
  }
  }
 }
}else if(isset($_POST['acc'])){
 $psw=$_POST['Acpsw'];
 $fname=$_POST['Acfname'];
 $attend=new assist;
 $attend->selectOne("role","Head Accountant");
 if(mysqli_num_rows($attend->result)==0){
  echo "<p class='view-acc'>Accountant is not set Yet!</p>";
 }else{
  if($row=mysqli_fetch_assoc($attend->result)){
   if ($row['fname']!=$fname) {
     echo "<p class='view-acc'>Name Not Found!</p>";
   } else {
   if($psw!=$row['psw']){
    echo "<p class='view-acc'>Incorrect Password</p>";
   }else{
    setcookie( "fname", "Acc", time()+8640000, "/","", 0);
    header("Location: homes/acc.php");
   }
   }
 }
}
}else if(isset($_GET['send'])){
 $to=$_GET['to'];
 $from=$_GET['from'];
 $content=$_GET['content'];
 $message->five($from,$to);
 if(mysqli_num_rows($message->result)>0){
  echo "You Can No Longer Send To This Reciever.";
 }else{
 $message->send($from,$to,$content,5);
 header("Location: login.php");
 }
}
?>