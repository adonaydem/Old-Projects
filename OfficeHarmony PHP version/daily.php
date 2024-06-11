<?php
include "class.php";
 $employ=new employee;
 $assist=new assist;
 $attend=new attend;
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src = "js/bootstrap.min.js"></script>
      <style type="text/css">
       body{
       padding:0;
       margin:0;
       }
       .jumbotron{
       border:1px #199970 solid;
       margin:15px;
       }
       .col-sm-6{
       width:50%;
       }
      </style>
</head>
<body>
<div class="jumbotron col-sm-4">
<form method="post">
<div class="container"><h3>Sign here!</h3></div>
Specific ID:
<input type="text" name="id" placeholder="Identification" class="form-control">
<br>
Password:
<input type="passowrd" name="psw" placeholder="Password" class="form-control">
<button type="submit" name="sign" class="btn btn-info" style="float:right">Done</button>
</form>
</div>
<div class="jumbotron col-sm-4">
<form method="post">
<div class="container"><h3>Permit For Short Period  here!</h3></div>
Specific ID:
<input type="text" name="id" placeholder="Identification" class="form-control">
<br>
Reason:
<select class="form-control" name="reason">
<option value="sick">Sick</option>
<option value="warranty">Permission</option>
<option value="accident">Accident</option>
</select>
<br>
Password:
<input type="passowrd" name="psw" placeholder="Permiter Password" class="form-control" required>
<button type="submit" name="permit" class="btn btn-info" style="float:right">Done</button>
</form>
</div>
<h1>Report</h1><hr>
<?php
 $date=date("d/m/y",time());
 $attend->selectOne("date",$date);
  if(mysqli_num_rows($attend->result)==0){
   echo "<form method='post'><button type='submit' name='submit' class='btn btn-primary'>Report For Today</button></form>";
  }else{
   echo "You Have Already Submitted For Today!";
  }
?>
<table class="table table-bordered">
<tr></tr>
<tr><th>Day</th><th>No Reason</th><th>Sick</th><th>Permission</th><th>Accident</th></tr>
<?php
$now=date("m",time());
$attend->selectAll();
while($row=mysqli_fetch_assoc($attend->result)){
 $date=explode("/",$row['date']);
 $month=$date[1];
 if($now==$month){
  echo "<tr>
  <td>".$row['date']."</td>
  <td>".$row['absent']."</td>
  <td>".$row['sick']."</td>
  <td>".$row['permit']."</td>
  <td>".$row['accident']."</td>
  </tr>";
 }
}
?>
</table>
</body>
</html>
<?php
if(isset($_POST['sign'])){
 $id=$_POST['id'];
 $psw=$_POST['psw'];
 $employ->selectOne("id",$id);
 if(mysqli_num_rows($employ->result)==0){
  echo "Failed Indentifying";
 }else{
  $row=mysqli_fetch_assoc($employ->result);
  if($row['psw']!=$psw){
   echo "Failed Indentifying";
  }else{
   setcookie( $id, "present", time()+36000, "/","", 0);
  }
 }
}else if(isset($_POST['permit'])){
 $id=$_POST['id'];
 $psw=$_POST['psw'];
 $reason=$_POST['reason'];
 $employ->selectOne("id",$id);
 if(mysqli_num_rows($employ->result)==0){
  echo "Failed Indentifying";
 }else{
  $assist->selectOne("role","attendance");
  if(mysqli_num_rows($assist->result)==0){
   echo "Attendant Not Set!";
  }else{
   $row=mysqli_fetch_assoc($assist->result);
   if($row['psw']!=$psw){
    echo "Incorrect Password!";
   }else{
    setcookie( $id, $reason, time()+36000, "/","", 0);
  }
 }
}
}else if(isset($_POST['submit'])){
 $date=date("d/m/y",time());
 $attend->selectOne("date",$date);
 if(mysqli_num_rows($attend->result)==0){
 $attend->insert("");
 }
 $employ->selectAll();
 $absent=array();
 $sick=array();
 $warranty=array();
 $accident=array();
 while($row=mysqli_fetch_assoc($employ->result)){
  $id=$row['id'];
  if(!isset($_COOKIE[$id])){
   array_push($absent,$id);
  }else if($_COOKIE[$id]=="sick"){
   array_push($sick,$id);
  }else if($_COOKIE[$id]=="warranty"){
  array_push($warranty,$id);
  }else if($_COOKIE[$id]=="accident"){
  array_push($accident,$id);
  }
 }
 $absent=implode(",",$absent);
 $sick=implode(",",$sick);
 $warranty=implode(",",$warranty);
 $accident=implode(",",$accident);
 $attend->update("absent",$absent);
 $attend->update("sick",$sick);
 $attend->update("permit",$warranty);
 $attend->update("accident",$accident);
 header("Loaction: daily.php");
}
?>