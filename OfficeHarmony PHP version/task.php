<?php
include "class.php";
$task=new task;
$message=new message;
$payroll=new payroll;
$done=new done; 
$list=new employee; 
$plan=new plan; 
$program=new program; 
$conn=mysqli_connect("localhost","root","","job");
?>
<!DOCTYPE html>
<html>
<head>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src = "js/bootstrap.min.js"></script>
<title></title>
</head>
<body>
<div class="jumbotron" style="background:white">
<?php
$list->selectOne("fname",$_COOKIE['fname']);
if(mysqli_num_rows($list->result)>0){
 $rowi=mysqli_fetch_assoc($list->result);
}
$fname=$_COOKIE['fname'];
if($_COOKIE['fname']=="Admin" || $rowi['access']!=""){
 echo '<div class="jumbotron col-sm-4 col-lg-4">
 <h2>Assign Task</h2>
 <form>
 <input type="hidden" name="assigner" value="'.$fname.'">
 <select name="people[]" multiple class="form-control" required>';
 $list->selectAll();
 while($row=mysqli_fetch_assoc($list->result)){
  if($fname=="Admin"){
   echo "<option>".$row['fname']."</option>";
  }else{
   $list->selectOne("edu",$rowi['access']);
   if(mysqli_num_rows($list->result)>0){
    while($row3=mysqli_fetch_assoc($list->result)){
     if($row3['fname']!=$fname){
      echo "<option>".$row3['fname']."</option>";
     }
    }
   }
  }
 }
 echo '</select>
 <input type="time" name="time" class="form-control" placeholder="Reporting time" required><br>
 <input type="text" name="content" class="form-control" placeholder="Content" required><br>
 <button type="submit" name="assign" class="btn btn-warning">Assign</button>
 </form>
 </div>
 
 <div class="jumbotron col-sm-4 col-lg-4">
 <h2>Call for E-Meeting</h2>
 <form method="post">
 <input type="hidden" name="assigner" value="'.$fname.'">
 <select name="people[]" multiple class="form-control" required>';
 $list->selectAll();
 while($row=mysqli_fetch_assoc($list->result)){
 if($fname=="Admin"){
 echo "<option>".$row['fname']."</option>";
 }else{
 $list->selectOne("edu",$rowi['access']);
 if(mysqli_num_rows($list->result)>0){
 while($row3=mysqli_fetch_assoc($list->result)){
 if($row3['fname']!=$fname){
 echo "<option>".$row3['fname']."</option>";
 }
 }
 }
 }
 }
echo ' </select>
 <input type="time" name="time" class="form-control" placeholder="Time" required><br>
 <input type="text" name="sub" class="form-control" placeholder="Subject" required><br>
 <button type="submit" name="e-meet" class="btn btn-warning">Assign</button>
 </form>
 </div>';
 $fname=$_COOKIE['fname'];
 $sql="select * from `task` where `assigner`='$fname' order by `time` desc limit 10";
 $result=mysqli_query($conn, $sql);
 if(mysqli_num_rows($result)>0){
 echo "<h4>Assigned List</h4><hr><table class='table table-bordered'><tr><th>Id</th><th>Assigned</th><th>Time</th><th>Type</th></tr>";
 while($row=mysqli_fetch_assoc($result)){
 echo "<tr>
 <td>".$row['id']."</td>
 <td>".$row['assigned']."</td>
 <td>".$row['time']."</td>";
 if($row['status']==1){
 echo "<td>Task</td>";
 }else if($row['status']==2){
 echo '<td>E-meeting</td><td><a class="btn btn-info" href="transporter.php?emeet='.$row['id'].'" target="_blank">Attend</a></td>';
 }
 echo "<td><form method='post'><input type='hidden' name='id' value='".$row['id']."'><form><button type='submit' name='del_task' class='btn btn-link'>Remove</button></form></td></tr>";
 }
 echo "</table>";
 }
}
$task->selectAll();
while($row=mysqli_fetch_assoc($task->result)){
$id=$row['id'];
 $people=explode(",",$row['assigned']);
 foreach($people as $man){
  if($man==$_COOKIE['fname']){
   echo '<div class="jumbotron" style="width:50%;box-shadow:0 0 3px gray"><p><small>';
   if($row['status']==1){
    echo "Report to: ";
   }else if($row['status']==2){
    echo "E-Meeting: ";
   }
   echo '<b>'.$row['assigner'].'</b></p>
   <p>Task Number: '.$row['id'].'</p></small>
   <p>'.$row['content'].'<p>
   <small><p>Reporting Time: '.$row['duration'].'</p>
   <p>Posted Time: '.$row['time'].'<p></small>
   <form method="post" enctype="multipart/form-data">
   <input type="hidden" name="id" value="'.$row['id'].','.$man.'">
   <input type="hidden" name="assigner" value="'.$row['assigner'].'">';
   if($row['status']==1){
   echo '<input type="file" name="file" class="form-control" required><br><button type="submit" name="report" class="btn btn-default">Report</button>';
   }else if($row['status']==2){
   echo '<a class="btn btn-info" href="transporter.php?emeet='.$row['id'].'" target="_blank">Attend</a>';
   }
   echo '</form>
   </div>';
 }
}
}
$program->select_voting("date",date("Y-m-d"));
while($row=mysqli_fetch_assoc($program->result)){
 $voter=explode(",",$row['voter']);
 foreach($voter as $man){
  if($man == $_COOKIE['fname']){
   echo '
   <div class="jumbotron" style="width:50%;box-shadow:0 0 3px gray">
   <p><b>Voting Program</b></p>
   <p>BY <b>'.$row['fname'].'</b></p>
   <p>Title: '.$row['title'].'</p>
   <form method="post">
   <input type="hidden" name="id" value="'.$row['id'].'">
   Nominees:
   <select name="voted" class="form-control" required>';
   $nominee=explode(",",$row['nominee']);
   foreach($nominee as $nom){
    echo "<option>".$nom."</option>";
   }
   echo ' 
   <input type="password" name="psw" class="form-control" placeholder="Password" required>
   <button type="submit" name="vote" class="btn btn-success">Vote</button>
   </select>
   </form>
   </div>
   ';
  }
 }
 
}

$done->select("fname",$_COOKIE['fname']);
if(mysqli_num_rows($done->result)>0){
echo "<div class='jumbotron'><h4>Task's Done</h4>
<table class='table'><tr><th>Date</th><th>Task Number</th><th>Full Name</th><th>Status</th></tr>";
while($row=mysqli_fetch_assoc($done->result)){
 echo "<tr>
 <td>".$row['date']."</td>
 <td>".$row['task_no']."</td>
 <td>".$row['fname']."</td>";
 if($row['status']==""){
  echo "<td>Waiting</td>";
 }else if($row['status']==0){
  echo "<td>Failed</td>";
 }else if($row['status']==1){
  echo "<td>Approved</td>";
 }
 echo "</tr>";
}
echo "</table></div>";
}
$payroll->all_acc();
 while($row=mysqli_fetch_assoc($payroll->result)){
  $people=explode(",",$row['people']);
  foreach($people as $man){
   if($_COOKIE['fname']==$man){
    echo "<div class='jumbotron'>
    <p><b>Withdrawal Allowance</b></p>
    <p><small>".$row['date']."</small></p>
    <p>Amount: ".$row['amount']."</p>
    <p>Reason: ".$row['reason']."</p>
    <form>
    <input type='hidden' name='fname' value='".$man."'>
    <input type='hidden' name='id' value='".$row['id']."'>
    <button name='get' class='btn' type='submit'>Get</button>
    </form>
    </div>";
   }
  }
 }


?>

</body>
</html>
<?php
if(isset($_GET['assign'])){
 $people=implode(",",$_GET['people']);
 $content=$_GET['content'];
 $assigner=$_GET['assigner'];
 $time=$_GET['time'];
 $task=new task;
 $task->insert($assigner,$people,$time,$content,1);
 header("Location: task.php");
}else if(isset($_POST['e-meet'])){
 $people=implode(",",$_POST['people']);
 $sub=$_POST['sub'];
 $time=$_POST['time'];
 $assigner=$_POST['assigner'];
 $task=new task;
 foreach($_POST['people'] as $per){
  $plan->insert($time,"E-meeting",$per,$sub,$people,1);
 }
 $task->insert($assigner,$people,$time,$sub,2);
 header("Location: task.php");
}else if(isset($_POST['del_task'])){
  $id=$_POST['id'];
  $task->delete("id",$id);
  header("Location: task.php");
 }else if(isset($_POST['report'])){
$id=$_POST['id'];
$assigner=$_POST['assigner'];
$file=$_FILES['file'];
$name=$_FILES['file']['name'];
$tmp=$_FILES['file']['tmp_name'];
$size=$_FILES['file']['size'];

$ext=explode('.',$name);
$actual=strtolower(end($ext));

$allowed=array('doc','docx','xls','ppt');
if(in_array($actual,$allowed)){
 $newname=$id."_".$_COOKIE['id']."."."doc"; 
 $destination='Reports/'.$newname; 
 move_uploaded_file($tmp, $destination); 
 $message->delete("content",$newname);
 $message->send($_COOKIE['fname'],$assigner,$newname,"3");
 $done->insert(date("d/m/y",time()),$id, $_COOKIE['fname'],"");
 header("Location: task.php");
}else{
 echo "Incorrect File";
}
}else if(isset($_GET['get'])){
 $fname=$_GET['fname'];
 $id=$_GET['id'];
 $message->send($fname,"Acc",$id,7);
 header("Location: task.php");
}else if(isset($_POST['vote'])){
 $id=$_POST['id'];
 $voted=$_POST['voted'];
 $psw=md5($_POST['psw']);
 $list->selectOne("fname",$_COOKIE['fname']);
 if(mysqli_num_rows($list->result)>0){
 $row=mysqli_fetch_assoc($list->result);
 if($row['psw']==$psw){
  $program->select_voting("id",$id);
  $rowi=mysqli_fetch_assoc($program->result);
  $number=explode(",",$rowi['number']);
  $nominee=explode(",",$rowi['nominee']);
  $voter=explode(",",$rowi['voter']);
  if(($key=array_search($voted,$nominee))!==false){
   $number[$key]=$number[$key]+1;
   $new=implode(",",$number);
   $program->update_voting("number",$new,"id",$id);
   $key2=array_search($_COOKIE['fname'],$voter);
   unset($voter[$key2]);
   $voter=implode(",",$voter);
   $program->update_voting("voter",$voter,"id",$id);
   header("Location: task.php");
  }
  
 }else{
  echo "Incorrect Password";
 }
 }
}
?>