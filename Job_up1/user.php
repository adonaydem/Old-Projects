<?php
include "class.php";
$assist=new assist;
$employ=new employee;
$attend=new attend;
$message=new message;
$payroll=new payroll;
$rating=new rating;
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src = "js/bootstrap.min.js"></script>
<script src="include/jquery-3.4.1.js"></script>
<script src="include/juser.js"></script>
<script type="text/javascript">
$(function(){
 $(".editerror").css("color","white");
 var editerror=$(".editerror").text();
 if(editerror!=""){
 alert(editerror);
 }
});
</script>
</head>
<body>
<div class="container jumbotron" style="background:white">
<table class="table table-striped">
<?php
if(isset($_GET['id'])){
$employ->selectOne("id",$_GET['id']);
}else{
$employ->selectOne("id",$_COOKIE['id']);
}
$row=mysqli_fetch_assoc($employ->result);
echo "<tr><td colspan='2' style='background:#3d9970'><h2 style='text-align:center;color:white'>".$row['fname']."</h2></td></tr>";
echo "<tr>";
echo "<td>
<p><b>Phone Number:</b> ".$row['p_no']."</p>
<p><b>Emergency Contact: </b>".$row['e_no']."</p>
<p><b>Residence: </b>".$row['residence']."</p>
<p><b>Department: </b>";
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
echo "</p>
<p><b>E-mail: </b>".$row['email']."</p>
<p><b>Bank Acoount No: </b>".$row['bank']."</p>
</td><td>";
if(!isset($_GET['id'])){
 echo "<form><button type='submit' name='request' class='btn btn-primary'>Request For Change</button></form>";
}
echo "</td></tr>";
?>
</table>
</div>
<?php
if(isset($_GET['id'])){
if($_GET['visit']=="Attend"){
echo '
<div class="col-sm-4 jumbotron">
<h2>Edit</h2><hr>
<form method="post">
<select name="category" class="form-control" required>
<option value="residence">Residence</option>
<option value="p_no">Phone Number</option>
<option value="e_no">Emergency Contact</option>
<option value="email">E-mail</option>
<option value="bank">Bank Number</option>
</select>
<input type="hidden" name="id" value="'.$_GET['id'].'"required>
<input type="text" class="form-control" name="new" placeholder="New"required><br>
<input type="password" class="form-control" name="psw" placeholder="Password" required><br>
<button type="submit" name="edit" class="btn btn-primary">Done</button></form>
</form>
<form method="post">
<button type="submit" name="change" class="btn btn-danger">Change User Password</button>
</form>
</div>
';
}
}else{
 echo '<table class="table" id="scroll-att">
 <tr>
<th style="text-align:center;background:lightgray" colspan="2">Attendance Status</th>
 </tr>';
 $attend->selectAll();
 while($row=mysqli_fetch_assoc($attend->result)){
  $absent=explode(",",$row['absent']);
  $sick=explode(",",$row['sick']);
  $permit=explode(",",$row['permit']);
  $accident=explode(",",$row['accident']);
  if(($key=array_search($_COOKIE['id'],$absent))!==false){
   echo "<tr><td>".$row['date']."</td><td>Absent</td></tr>";
  }else if(($key=array_search($_COOKIE['id'],$sick))!==false){
  echo "<tr><td>".$row['date']."</td><td>Sick</td></tr>";
  }else if(($key=array_search($_COOKIE['id'],$permit))!==false){
  echo "<tr><td>".$row['date']."</td><td>Permission</td></tr>";
  }else if(($key=array_search($_COOKIE['id'],$accident))!==false){
  echo "<tr><td>".$row['date']."</td><td>Accident</td></tr>";
  }
 }
 echo '</table>';
 echo '<table class="table" id="scroll-acc">
 <tr>
 <th colspan="4" style="text-align:center;background:lightgray">Payment Status</th>
 </tr><tr><th>Date</th><th>Basic Pay</th><th>Gross Pay</th><th>Net Pay</th></tr>';
 $payroll->selectAll("payroll");
 while($row=mysqli_fetch_assoc($payroll->result)){
  $people=explode(",",$row['people']);
  foreach($people as $man){
  if($man==$_COOKIE['fname']){
  echo "<tr>
  <td>".$row['date']."</td>
  <td>".$row['basic']."</td>
  <td>".$row['gross']."</td>
  <td>".$row['net']."</td>
  </tr>";
  }
  }
 }
 echo '</table>';
 echo '<table class="table" id="scroll-rate" style="width:50%">
 <tr>
 <th colspan="3" style="text-align:center;background:lightgray">Rating Status</th>';
 $rating->select("name",$_COOKIE['fname']);
 if (mysqli_num_rows($rating->result)>0) {
   $row=mysqli_fetch_assoc($rating->result);
   echo '<tr><td>Rating</td><td>'.$row['number'].'</td>';
   if($row['number']==0){
    echo '<td style="background:red;color:white;font-weight:bold">Danger</td></tr>';
   }else if($row['number']==1){
    echo '<td style="background:black;color:white;font-weight:bold">Warning</td></tr>';
   }else if($row['number']==2 && $row['number']==3){
    echo '<td style="background:yellow;color:white;font-weight:bold">Normal</td></tr>';
   }else if($row['number']==4){
    echo '<td style="background:green;color:white;font-weight:bold">Good</td></tr>';
   }else if($row['number']==5){
    echo '<td style="background:teal;color:white;font-weight:bold">KEEP IT UP!</td></tr>';
   }
   echo '<tr><td colspan="3" style="text-align:center"><form><button type="submit" name="complain" class="btn btn-danger">Complain</button></form></td></tr>';
 }else{
   echo '<tr><td colspan="3">You are not Registered</td></tr>';
 }

}
?>
</body>
</html>
<?php
if(isset($_POST['edit'])){
 $category=$_POST['category'];
 $new=$_POST['new'];
 $psw=$_POST['psw'];
 $assist->selectOne("role","Attendance");
 if(mysqli_num_rows($assist->result)==0){
  echo "<p class='editerror'>Attendance Not Set!</p>";
 }else{
 if($row=mysqli_fetch_assoc($assist->result)){
  if($row['psw']!=$psw){
   echo "<p class='editerror'>Incorrect Password!</p>";
  }else{
   $id=$_GET['id'];
   $employ->update($category,$new,$id);
   header("Location: attdb.php");
  }
 }
 }
}else if(isset($_GET['request'])){
 $fname=$_COOKIE['fname'];
 $message->send($fname,"Attend","editInfo",1);
}else if(isset($_POST['change'])){
  $rand=str_shuffle("abcd1234567890");
  $subrand=substr($rand,0,6);
  $psw=md5($subrand);
  $id=$_GET['id'];
  $employ->update("psw",$psw,"id",$id);
  $message->send("John Doe","Attend",$subrand, 0);
  header("Location: attdb.php");
}
else if (isset($_GET['complain'])) {
  $fname=$_COOKIE['fname'];
  $message->send($fname,"Admin","",8);
  header("Location: user.php#rate");
}
?>