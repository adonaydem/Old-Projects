<?php
include 'class.php';
$list=new employee;
$plan=new plan;
$rating=new rating;
$task=new task; 
$conn=mysqli_connect("localhost","root","","job");
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src = "js/bootstrap.min.js"></script>
      <script src="include/jquery-3.4.1.js"></script>
<script type="text/javascript">
  $(function() {
    $("#input-search").keyup(function() {
      $("#search-result").load("search.php",{
        search:$("#input-search").val()
      })
    });
    var rate=$(".rate-error").text();
    if(rate!=""){
     alert(rate);
    }
  });
</script>
</head>
<body>
  <div class="jumbotron" style='padding:10px'>
    <input type="text" id="input-search" class="form-control" placeholder="Search Here">
    <table id="search-result" class="table table-bordered table-striped"></table>
  </div>
 <h4>Employee</h4><hr>
<table class="table table-bordered table-striped">
<tr>
<th>Id</th><th>Full Name</th><th>Residence</th><th>Phone Number</th><th colspan="2">Rating</th>
</tr>
<?php
$list->selectAll();
while($row=mysqli_fetch_assoc($list->result)){
 echo "<tr>
 <td>".$row['id']."</td>
 <td>".$row['fname']."</td>
 <td>".$row['residence']."</td>
 <td>".$row['p_no']."</td>";
 $rating->select("name",$row['fname']);
 if(mysqli_num_rows($rating->result)==0){
  echo "<td colspan='2'>Not Registered 
  <form method='post'><input type='hidden' name='fname' value='".$row['fname']."'><button type='submit' name='insert_rating' class='btn btn-link'>Register</button></from></td>";
 }else{
 $rowi=mysqli_fetch_assoc($rating->result);
 echo "<td>".$rowi['number']."</td><td><form method='post'>
 <input type='hidden' name='name' value='".$row['fname']."'>
 <button type='submit' name='up' class='btn btn-warning'>Up</button>
 <button type='submit' name='down' class='btn btn-danger'>Down</button>
 </form></td>";
 }
 echo "<td><a href='user.php?id=".$row['id']."&&visit=".$_COOKIE['fname']."'>Detail</a></td>
 </tr>";
 }
 echo "</table>";
 echo "<h4>My Heads</h4>";
 $sql="select * from `employee` where `access`!=''";
 $result=mysqli_query($conn, $sql);
 echo "<table class='table table-bordered'>
 <tr><th>ID</th><th>Full Name</th><th>Department</th><th>Phone Number</th><th>Email</th></tr>";
 if(mysqli_num_rows($result)==0){
  echo "<tr><td colspan='5'><p style='text-align:center;font-style:bold'><b>Heads of departments are not set! </b><a href='update.php?type=head' class='btn btn-link'>Click here to add</a></p></td></tr>";
 }else{
  while($row=mysqli_fetch_assoc($result)){
   echo "<tr>
   <td>".$row['id']."</td>
   <td>".$row['fname']."</td>
   <td>";
   switch($row['access']){
   case 0:
   echo "General Office";
   break; 
   case 1:
   echo "Marketing and Sales";
   break; 
   case 2:
   echo "Production";
   break; 
   case 3:
   echo "Accounting and Finance";
   break; 
   case 4:
   echo "Human Resource";
   break; 
   case 5:
   echo "Personnel";
   }
   echo "</td>
   <td>".$row['p_no']."</td>
   <td>".$row['email']."</td>
   <td><a href='update.php?type=head&pro=".$row['edu']."'>Change</a></td>
   </tr>";
  }
 }
 echo "</table>";
?>
</body>
</html>
<?php
if(isset($_POST['up'])||isset($_POST['down'])){
  $name=$_POST['name'];
  $rating->select("name",$name);
  $row=mysqli_fetch_assoc($rating->result);
  if(isset($_POST['up'])){
   if($row['number']==5){
    echo "<p class='rate-error' style='color:white'>Rating At Maximum</p>";
   }else{
    $rate=$row['number']+1;
    $rating->update("number",$rate,"name",$name);
    header("Location: list.php");
   }
  }else if(isset($_POST['down'])){
  if($row['number']==0){
   echo "<p class='rate-error' style='color:white'>Rating At Minimum</p>";
  }else{
   $rate=$row['number']-1;
   $rating->update("number",$rate,"name",$name);
   header("Location: list.php");
  }
  }
 }else if(isset($_POST['insert_rating'])){
  $fname=$_POST['fname'];
  $rating->insert($fname);
  header("Location: list.php");
 }
?>