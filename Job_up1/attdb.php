<?php
include 'class.php';
$attend=new attend; 
$employ=new employee; 
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
        search:$("#input-search").val(),
        att:""
        })
       });
      });
      
      </script>
</head>
<body>
<div class="jumbotron" style="background:white">
<h3>Employee Database</h3><hr>
    <input type="text" id="input-search" class="form-control" placeholder="Search Here">
    <table id="search-result" class="table table-bordered table-striped"></table>
<table class="table table-bordered">
<tr><th>ID</th><th>Full Name</th><th>Residence</th><th>Phone Number</th><th>Emergency Contact</th><th>Department</th><th>Email</th><th>Bank Number</th></tr>
<?php
if(isset($_GET['filterEdu'])){
 $employ->selectOne("edu",$_GET['edu']);
 while($row=mysqli_fetch_assoc($employ->result)){
 echo "<tr>
 <td>".$row['id']."</td>
 <td>".$row['fname']."</td>
 <td>".$row['residence']."</td>
 <td>".$row['p_no']."</td>
 <td>".$row['e_no']."</td><td>";
 switch($row['edu']){
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
 <td>".$row['email']."</td>
 <td>".$row['bank']."</td>
 <td><a href='user.php?id=".$row['id']."&&visit=".$_COOKIE['fname']."'>Edit</a></td>
 </tr>";
 }
}else{
$sql="select * from `employee` limit 10";
$result=mysqli_query($conn, $sql);
while($row=mysqli_fetch_assoc($result)){
 echo "<tr>
 <td>".$row['id']."</td>
 <td>".$row['fname']."</td>
 <td>".$row['residence']."</td>
 <td>".$row['p_no']."</td>
 <td>".$row['e_no']."</td><td>";
 switch($row['edu']){
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
 <td>".$row['email']."</td>
 <td>".$row['bank']."</td>
 <td><a href='user.php?id=".$row['id']."&&visit=".$_COOKIE['fname']."'>Edit</a></td>
 </tr>";
}
}
?>
<tr><td colspan='8'>Show more</td></tr>
<tr><td colspan="7">
<form><select name="edu" class="form-control" required>
<option value="0">General Office</option>
<option value="1">Marketing and Sales</option>
<option value="2">Production</option>
<option value="3">Accounting and Finance</option>
<option value="4">Human Resource</option>
<option value="5">Personnel</option>
</select></td><td><button type="submit" name="filterEdu" class="btn btn-default">Filter</button></form>
</td></tr>
</table>
<h3>Attendance Database</h3><hr>
<table class="table table-bordered">
<tr><th>Date</th><th>Absentee's ID</th><th>Sick</th><th>Accident</th><th>Permission</th></tr>
<?php
$attend->selectAll();
if(mysqli_num_rows($attend->result)==0){
 echo "<tr>This programme haven't started yet!</tr>";
}else{
 while($row=mysqli_fetch_assoc($attend->result)){
  echo "<tr>
  <td>".$row['date']."</td>
  <td>".$row['absent']."</td>
  <td>".$row['sick']."</td>
  <td>".$row['accident']."</td>
  <td>".$row['permit']."</td>
  </tr>";
 }
}
?>
</table>
</div>
</body>
</html>
<?php

?>