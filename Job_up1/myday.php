<?php
include "class.php";
$plan=new plan;
$message=new message;
$rating=new rating;
$month=new month;
$employ=new employee;
$date=date("d/m/y",time());
$payroll=new payroll;
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
$(function(){
 var tat=$("#tat").text();
 if(tat!=""){
  alert(tat);
 }
});
</script>
<style type="text/css">
.plan{
width:100%;
background:#ffdc00;
}
.head{
width:100%;
background:teal;
color:white;
}
.head a{
 text-decoration:none;
 color:white;
}
.plan2{
width:100%;
background:#ff851b;
}
.plan3{
width:100%;
background:#ddd;
}
#modal{
display:none;
}
#modalEdit{
display:none;
background:gray;
}
#tat{
color:white;
}
</style>
</head>
<body>
<?php
  echo '<div class="jumbotron head">
    <table class="table table-striped">
     <tr><td><b>'.$_COOKIE['fname'].'</b></td></tr>';
   if($_COOKIE['fname']!="Admin" && $_COOKIE['fname']!="Acc" && $_COOKIE['fname']!="Attend"){
   echo '<tr><td><a href="user.php#att" target="_blank">Attendance</a></td><td><a href="user.php#acc" target="_blank">Payroll</a></td><td><a href="user.php#rate" target="_blank">My Rating</a></td></tr>';
   }
   echo '<tr style="background:teal"><td><a href="program.php">Create New Program</a></td></tr>';
    $message->recent($_COOKIE['fname']);
    echo '<tr><td colspan="3">';
    if($message->i==1){
     echo 'Only 1 message recieved today.';
    }else if($message->i==0){
     echo 'No messages recieved today.';
    }else{
     echo $message->i.' messages recieved today.';
    }
    echo ' <a href="contact.php" class="btn btn-primary">Go</a></td></tr></table>
  </div>';
?>
<div class="jumbotron plan">
<h1>My Plan</h1><h3 style="color:gray">
<?php
echo date("D M Y",time());
?>
</h3><hr>
<form>
<table class="table table-bordered">
<tr><th>Date</th><th>Time</th><th>Type</th><th>Subject</th><th>Content</th><th>Concerns</th></tr>
<?php
$plan->selectAll(10);
if(mysqli_num_rows($plan->result)==0){
 echo "<tr><td colspan='5' style='text-align:center'><h6>No plans made!</h6></td></tr>";
}else{
$dept=array('0','1','2','3','4','5');
 while($row=mysqli_fetch_assoc($plan->result)){
 if($row['access']==1){
 if($row['call']==$_COOKIE['fname'] || $row['call']=="All"){
  echo "<tr>
  <td>".$row['date']."</td>
  <td>".$row['time']."</td>";
  if($row['call']==$_COOKIE['fname']){
  echo "<td>Personal</td>";
  }else{
    echo "<td>All</td>";
  }
 echo "<td>".$row['sub']."</td>
  <td>".$row['content']."</td>
  <td>".$row['concern']."</td>";
  if($_COOKIE['fname']==$row['call']){
  echo "<td><input type='hidden' name='value' value='".$row['id']."'><button type='submit' name='del' class='btn btn-link'>Remove</td>";
  }else if($_COOKIE['fname']=="Admin" && $row['call']=="All"){
  echo "<td><input type='hidden' name='value' value='".$row['id']."'><button type='submit' name='del' class='btn btn-link'>Remove</td>";
  }
  echo "</tr>";
  }else if(in_array($row['call'],$dept)){
  $employ->selectOne("fname",$_COOKIE['fname']);
   if(mysqli_num_rows($employ->result)>0){
   $rowi=mysqli_fetch_assoc($employ->result);
   if($rowi['edu']==$row['call']){
   echo "<tr>
   <td>".$row['date']."</td>
   <td>".$row['time']."</td>
   <td>Department</td>
   <td>".$row['sub']."</td>
   <td>".$row['content']."</td>
   <td>".$row['concern']."</td>";
    if($rowi['access']==$row['call']){
    echo "<td><input type='hidden' name='value' value='".$row['id']."'><button type='submit' name='del' class='btn btn-link'>Remove</td>";
    }
    echo "</tr>";
    }
    }
  }
  }
 }
}
?>
</form>
<form>
<tr>
<td><input type="date" name="date" class="form-control" required></td>
<td><input type="time" name="time" class="form-control" placeholder="HH-MM-AM/PM" required><input type="hidden" value="<?php echo $_GET['role'];?>" name="role"></td>
<td><select name="call" class="form-control" required>
<option>Personal</option>
<?php
if($_COOKIE['fname']=="Admin"){
echo "<option>All</option>";
}else{
$employ->selectOne("fname",$_COOKIE['fname']);
 if(mysqli_num_rows($employ->result)>0){
  $row=mysqli_fetch_assoc($employ->result);
  if($row['access']!=""){
   echo "<option value='".$row['access']."'>Department</option>";
  }
 }
}
?>
</select></td>
<td><input type="text" name="sub" class="form-control" required></td>
<td><input type="text" name="content" class="form-control" required></td>
<td><input type="text" name="concern" class="form-control" required></td>
</tr>
</table>
<button type="submit" name="save" class="btn btn-success" style="float:right">Save</button>
</form>

<?php
if(!isset($_GET['today'])){
 echo "<button type='submit' name='today' class='btn btn-warning'>Show Todays</button>";
}else{
 $plan->select("date",date("Y-m-d",time()));
 if(mysqli_num_rows($plan->result)==0){
  echo "<p id='tat'>No plans for today!</p>";
 }else{
  echo "<table class='table table-bordered'>";
  $dept=array('0','1','2','3','4','5');
  while($row=mysqli_fetch_assoc($plan->result)){
   if($row['call']==$_COOKIE['fname'] || $row['call']=="All"){
    echo "<tr>
     <td>".$row['date']."</td>
     <td>".$row['time']."</td>";
     if($row['call']=="All"){
      echo "<td>All</td>";
     }else{
      echo "<td>Personal</td>";
     }
     echo "<td>".$row['sub']."</td>
     <td>".$row['content']."</td>
     <td>".$row['concern']."</td>
     </tr>";
    }else if(in_array($row['call'],$dept)){
    $employ->selectOne("fname",$_COOKIE['fname']);
    if(mysqli_num_rows($employ->result)>0){
    $rowi=mysqli_fetch_assoc($employ->result);
    if($rowi['edu']==$row['call']){
    echo "<tr>
    <td>".$row['date']."</td>
    <td>".$row['time']."</td>
    <td>Department</td>
    <td>".$row['sub']."</td>
    <td>".$row['content']."</td>
    <td>".$row['concern']."</td>";
    if($rowi['access']==$row['call']){
    echo "<td><input type='hidden' name='value' value='".$row['id']."'><button type='submit' name='del' class='btn btn-link'>Remove</td>";
    }
    }
    }
    }
   }
   echo "</table>";
 }
}
?>

</div>
<div class="jumbotron plan2">
<h1>Monthly Plan</h1><h3 style="color:gray">
<?php
echo "Month ".date("m/ M",time());
?>
</h3><hr>
<?php
$month->view();
if(mysqli_num_rows($month->result)==0){
 echo '<div class="jumbotron"><h3>No Plans For This Month!</h3></div>';
}else{
 while($row=mysqli_fetch_assoc($month->result)){
  echo '<div class="jumbotron" style="background:lightgray;width:50%"><p><b>'.$row['sub'].'</b></p>
  <p>'.$row['content'].' <small style="color:gray;">Concerns: '.$row['concern'].'</small></p>
  <p>Progress: ';
  echo '<div class="progress">
  <div class="progress-bar" role="progressbar"
  style="width:'.$row['progress'].'%">
  <span class="sr-only">70% Complete</span>
  </div>
  </div>';
  if($_COOKIE['fname']=="Admin"){
  echo "<form method='get' action='update.php'><input type='hidden' name='id' value='".$row['id']."'>
  <input type='hidden' name='type' value='month'>
  <input type='hidden' name='sub' value='".$row['sub']."'>
  <input type='hidden' name='content' value='".$row['content']."'>
  <input type='hidden' name='concern' value='".$row['concern']."'>
  <input type='hidden' name='progress' value='".$row['progress']."'>
  <button type='submit' name='edit_month' class='btn btn-primary'>Edit</button></form>
  ";
  echo "<form><input type='hidden' name='presub' class='form-control' value='".$row['sub']."' required><br> <button type='submit' name='del_month' class='btn btn-danger'>Delete</button></form>";
  }
  echo '</div>';
 }
}
if($_COOKIE['fname']=="Admin"){
 echo "<buttton id='show' class='btn btn-primary'>Plan</button>
 <div class='jumbotron' id='modal'>
 <form>
  <input type='text' name='sub' class='form-control' placeholder='Subject' required><br>
  <input type='text' name='content' class='form-control' placeholder='Content' required><br>
  <input type='text' name='concern' class='form-control' placeholder='Concerns' required><br>
  <button type='submit' name='month' class='btn btn-primary'>Save</button>
 </form>
 </div>
 ";
}
?>
</div>

<div class="jumbotron plan3">
<h1>Rating</h1><h3 style="color:gray">☀️☀️☀️☀️☀️</h3><small>It must be Updated Monthly</small><hr>
<?php
$rating->selectAll();
if(mysqli_num_rows($rating->result)==0){
 echo "<hr><h2>Program Not Started!</h2><hr>";
 if($_COOKIE['fname']=="Admin"){
 echo "<form><button type='submit' name='start' class='btn btn-success btn-lg'>Start</button></form>";
 }
}else{
 echo "<table class='table table-striped'>";
 while($row=mysqli_fetch_assoc($rating->result)){
  echo "<tr>";
  echo "<td>".$row['name']."</td><td>";
  for($i=0;$i<$row['number'];$i++){
   echo "☀️";
  }
  echo "</td>";
  if($_COOKIE['fname']=="Admin"){
  echo "<td><a href='list.php'>Update</a></td>";
  }
  "</tr>";
 }
 echo "</table>";
}

if(isset($_GET['start'])){
 $employ->selectAll();
 while($row=mysqli_fetch_assoc($employ->result)){
  $rating->insert($row['fname']);
 }
}
?>
</div>

<?php
if($_COOKIE['fname']=="Acc"){
$payroll->last("capital",1);
$row=mysqli_fetch_assoc($payroll->result);
echo '<div class="jumbotron plan">
<h1>Transaction</h1>
<h3 style="color:gray">'.$row['total'].'</h3>
<h4 style="color:gray">Last Transaction'.$row['date'].'</h3><hr>';
echo '<div class="col-xs-4 col-sm-4 col-md-6 col-lg-6"><form>
<input type="number" name="amount" class="form-control" placeholder="Amount" required><br>
<input type="radio" name="type" value="withdraw">Withdraw 
<input type="radio" name="type" value="deposit">Deposit</br>
<textarea name="reason" class="form-control" placeholder="Reason" required></textarea><br>
<button type="submit" name="trans" class="btn">Done</button>
</form></div>';
echo '</div>';
}

?>
<script type="text/javascript">
var show=document.getElementById("show");
var modal=document.getElementById("modal");
show.onclick=function(){
 modal.style.display="block";
}
var edit=document.getElementById("edit");
var modalEdit=document.getElementById("modalEdit");
edit.onclick=function(){
 modalEdit.style.display="block";
}
</script>
</body>
</html>
<?php
if(isset($_GET['save'])){
  $fname=$_COOKIE['fname'];
  $date=$_GET['date'];
  $sub=$_GET['sub'];
  $content=$_GET['content'];
  $concern=$_GET['concern'];
  $time=$_GET['time'];
  if($_GET['call']=="Personal"){
   $call=$fname;
  }else{
   $call=$_GET['call'];
  }
  $sql="select * from `plan` where `date`='$date' AND `time`='$time' AND `call`='$call'";
  $result=mysqli_query($conn, $sql);
  if(mysqli_num_rows($result)>0){
   echo "<p id='tat'>The time is occupied</p>";
  }else{
  $plan->insert($date,$time,$sub,$call,$content,$concern,1);
  
  }
}else if(isset($_GET['del'])){
  $id=$_GET['value'];
  $plan->delete("id",$id);
  header("Location:myday.php");
}else if(isset($_GET['month'])){
 $sub=$_GET['sub'];
 $content=$_GET['content'];
 $concern=$_GET['concern'];
 $month->insert($sub,$content,$concern);
 header("Location:myday.php");
}else if(isset($_GET['del_month'])){
 $presub=$_GET['presub'];
 $month->delete($presub);
 header("Location:myday.php");
}else if(isset($_GET['trans'])){
 $amount=$_GET['amount'];
 $type=$_GET['type'];
 $reason=$_GET['reason'];
 $payroll->buss($type,"Acc",$amount,$reason);
 header("Location:myday.php");
}
 
 
 ?>