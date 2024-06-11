<?php
include "class.php";
$assist=new assist;
$employ=new employee;
$message=new message;
$plan=new plan;
$payroll=new payroll;
$done=new done; 
$task=new task; 
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src = "js/bootstrap.min.js"></script>
<style type="text/css">
.jumbotron{
 padding:1%;
 border-radius:0;
}
#modal{
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
     /* Sit on top */
    padding-top: 80px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    height:150px;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}
</style>
</head>
<body>
<?php
if($_COOKIE['fname']=="Admin"){
echo '
<div class="container jumbotron">
<h6>Informing Message</h6><hr>
<form method="post">
<input type="text" name="content" class="form-control" placeholder="Content" required><br>
<select name="to[]" class="form-control" multiple required>';
$employ->selectAll();
while($row=mysqli_fetch_assoc($employ->result)){
 echo "<option>".$row['fname']."</option>";
}
echo '</select>
<div class = "checkbox">
   <label>
      <input type="checkbox" name="reply">Allow Replying
   </label>
</div>
<button type="submit" name="inform" class="btn btn-success">Send</button>
</form>'; 
}else{
echo '
<div class="container jumbotron">
<h6>Contact The Admin</h6><hr>
<form method="post">
<input type="text" name="content" class="form-control" placeholder="Content" required><br>
<button type="submit" name="contact" class="btn btn-success">Send</button>
</form>
';
}
?>
<br><h6>Free File Share</h6><hr>
<form method="post" enctype="multipart/form-data">
Attach file:<br>
<input type="file" name="file" class="form-control" required><br>
<select name="to" class="form-control" required>
<?php
 $employ->selectAll();
 while($row=mysqli_fetch_assoc($employ->result)){
  if($row['fname']!=$_COOKIE['fname']){
   echo "<option>".$row['fname']."</option>";
  }
 }
?>
</select>
<button type="submit" name="send_file" class="btn btn-default">Send</button>
</form>
</div>
<div class="row">
<div class="col-sm-6">
<?php
 $fname=$_COOKIE['fname'];
$message->view($fname);
if(mysqli_num_rows($message->result)==0){
 echo "No Notifications Yet!";
}else{
echo "<h5>Notifications</h5><hr>";
 while($row=mysqli_fetch_assoc($message->result)){
 echo "<div class='jumbotron'><p><b>".$row['from']."</b><form>
 <input type='hidden' name='id' value='".$row['id']."'>
 <button type='submit' name='del_m' class='btn btn-link text-danger' style='float:right'>&times;</button>
 </form></p>";
    if($row['reply']==9){
     echo "<p>".$row['from']." sent you a file.</p>";
     echo "<a href='Shared/".$row['content']."' class='btn btn-defualt'>Open</a>";
    }else if($row['reply']==8){
      echo "<p>".$row['from']." complianed about rating. <small>Press see to look at it.</p>";
      echo "<a href='list.php' class='btn btn-defualt'>See</a>";
    }else if($row['reply']==7){
     echo "<p>".$row['from']." asked for withdrawal alowance ID:".$row['content']."</p>";
     $payroll->select_allow($row['content']);
     $rowi=mysqli_fetch_assoc($payroll->result);
     $people=explode(",",$rowi['people']);
     if(($key=array_search($row['from'],$people))!==false){
     echo "<form><input type='hidden' name='fname' value='".$row['from']."'><input type='hidden' name='id' value='".$row['content']."'><button type='submit' name='acc_allow' class='btn'>Allow</button></form>";
     }
    }else if($row['reply']==6){
     echo "<p>Your payment account has been updated.</p>";
     echo "<a href='user.php#acc' target='_blank' class='btn btn-warning'>Show</a>";
    }else if($row['reply']==5){
     echo "<p>".$row['from']." Is having problem with ".$row['content']."</p>";
      if($row['to']=="Attend"){
       if($row['from']!="Admin" || $row['from']!="Attendant"){
        $employ->selectOne("fname",$row['from']);
        if(mysqli_num_rows($employ->result)>0){
         $rowi=mysqli_fetch_assoc($employ->result);
         echo "<a href='user.php?id=".$rowi['id']."&&visit=Attend' class='btn btn-primary'>Troubleshoot</a>";
        }
       }else{
       echo "<a href='list.php' class='btn btn-primary'>Troubleshoot</a>";
       }
      }
      
    }else if($row['reply']==3){
     $id=explode(",",$row['content']);
     echo "<p>".$row['from']." sent a report for task number ".$id[0].".";
     echo "<a class='btn btn-info' href='Reports/".$row['content']."'>Download Here</a><form></p><input type='hidden' name='id' value='".$id[0]."'><input type='hidden' name='fname' value='".$row['from']."'><input type='hidden' name='m_id' value='".$row['id']."'><button type='submit' name='approve' class='btn btn-success'>Approve</button><button type='submit' name='cancel' class='btn btn-danger'>Cancel</button></form>";
    }else if($row['reply']==2){
     echo "<p>You have been nominated in the voting program of Subject: ".$row['content']."</p>";
     echo "<p>Wait,we'll get in touch.</p>";
    }else if($row['content']=="editInfo" && $row['to']=="Admin"){
     echo "<p>".$row['from']." Is requesting for information Change.</p>";
     if($row['reply']==1){
      echo '<form method="post">
      <input type="hidden" name="from" value="'.$row['to'].'">
      <input type="hidden" name="to" value="'.$row['from'].'">
      <input type="text" name="content" class="form-control" placeholder="Content"><br>
      <button type="submit" name="reply" class="btn btn-primary">Reply</button>
      </form>';
     }
  }else{
    echo "<p>".$row['content']."</p>";
    if($row['reply']==1){
    echo '<form method="post">
    <input type="hidden" name="from" value="'.$row['to'].'">
    <input type="hidden" name="to" value="'.$row['from'].'">
    <input type="text" name="content" class="form-control" placeholder="Content"><br>
    <button type="submit" name="reply" class="btn btn-success">Reply</button>
    </form>';
    }
  }
    echo "
    </div>";
 }
}
?>
</div></div>

</body>
</html>
<?php
if(isset($_POST['inform'])){
 $content=$_POST['content'];
 $people=$_POST['to'];
 if(!isset($_POST['reply'])){
  $reply=0;
 }else{
  $reply=1;
 }
 foreach($people as $to){
  $message->send("Admin",$to,$content,$reply); 
 }
 header("Location: contact.php");
}else if(isset($_POST['contact'])){
 $content=$_POST['content'];
 $from=$_COOKIE['fname'];
 $message->send($from,"Admin",$content,1);
}else if(isset($_POST['reply'])){
 $content=$_POST['content'];
 $from=$_POST['from'];
 $to=$_POST['to'];
 $message->send($from,$to,$content,1);
 header("Location: contact.php");
}else if(isset($_GET['acc_allow'])){
 $fname=$_GET['fname'];
 $id=$_GET['id'];
 $payroll->select_allow($id);
 $row=mysqli_fetch_assoc($payroll->result);
 $people=explode(",",$row['people']);
 if(($key=array_search($fname,$people))!==false){
  $payroll->buss("withdraw",$fname,$row['amount'],$row['reason']);
  unset($people[$key]);
  $new=implode(",",$people);
  $payroll->update_allow($new,$id);
 }
 header("Location: contact.php");
}else if(isset($_POST['send_file'])){
$from=$_COOKIE['fname'];
$to=$_POST['to'];
$file=$_FILES['file'];
$name=$_FILES['file']['name'];
$tmp=$_FILES['file']['tmp_name'];
$size=$_FILES['file']['size'];
$ext=explode(".",$name);
$actual=strtolower(end($ext));
$rand=rand(100000,999999);
$newname=$rand.".".$actual; 
$destination='Shared/'.$newname; 
 move_uploaded_file($tmp, $destination);
$message->send($from,$to,$newname,9);
header("Location: contact.php");
}else if(isset($_GET['approve']) || isset($_GET['cancel'])){
$id=$_GET['id'];
$fname=$_GET['fname'];
$m_id=$_GET['m_id'];
if(isset($_GET['approve'])){
$done->update("status",1,"task_no",$id);
$message->send("Admin",$fname,"Your report for Task Number ".$id." has been approved. thank you!",0);
$message->delete("id",$m_id);
$task->select("id",$id);
$row=mysqli_fetch_assoc($task->result);
$people=explode(",",$row['assigned']);
if(($key=array_search($fname,$people))!==false){
 unset($people[$key]);
 $new=implode(",",$people);
 $task->update("assigned",$new,"id",$id);
}
}else if(isset($_GET['cancel'])){
$done->update("status",0,"task_no",$id);
$message->send("Admin",$fname,"Your report for Task Number ".$id." has been denied. last resubmission date will be after 10 days",1 );
$message->delete("id",$m_id);
}
header("Location: contact.php");
}else if(isset($_GET['del_m'])){
 $id=$_GET['id'];
 $message->delete("id",$id);
 header("Location: contact.php");
}
?>