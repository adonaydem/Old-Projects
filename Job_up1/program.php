<?php
include 'class.php';
$employ=new employee; 
$program=new program; 
$message=new message; 
$plan=new plan; 
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
width:50%;
background:white;

}
button{
float:right;
}
</style>
</head>
<body>
<?php
if(isset($_GET['one'])){
 if($_GET['type']=="Voting"){
  echo '<div class="jumbotron">
  <form>
  Title:
  <input type="text" name="title" class="form-control" placeholder="Title" required>
  Nominees Grouped:
  <select name="G_nominee[]" multiple class="form-control">
  <option value="0">General Office</option>
  <option value="1">Marketing and Sales</option>
  <option value="2">Production</option>
  <option value="3">Accounting amd Finance</option>
  <option value="4">Human Resource</option>
  <option value="5">Personnel</option>
  </select>
  Nominees Single:
  <select name="nominee[]" multiple class="form-control">';
  $employ->selectAll();
  while($row=mysqli_fetch_assoc($employ->result)){
   echo '<option>'.$row['fname'].'</option>';
  }
  echo '</select>
  Voters:
  <select name="voter[]" multiple class="form-control" required>
  <option value="Admin">Administrator</option>
  <option value="Attend">Attendance</option>
  <option value="Acc">Head Accountant</option>';
  $employ->selectAll();
  while($row=mysqli_fetch_assoc($employ->result)){
  echo '<option>'.$row['fname'].'</option>';
  }
  echo '</select>
  Date:
  <input type="date"  name="date" class="form-control" required>
  <button type="submit" name="insert_voting" class="btn btn-primary">Done</button>
  </form>
  </div>';
 }
}else{
 echo '<div class="jumbotron">
 <form>
 <select name="type" class="form-control">
 <option>Voting</option>
 <option>Group Chat</option>
 <option>Post</option>
 <option>Table</option>
 </select>
 <button type="submit" name="one" class="btn btn-primary">Next</button>
 </form>
 </div>';
}
?>
</body>
</html>
<?php
if(isset($_GET['insert_voting'])){
 $fname=$_COOKIE['fname'];
 $date=$_GET['date'];
 $title=$_GET['title'];
 $nominee=implode(",",$_GET['nominee']).",".implode(",",$_GET['G_nominee']);
 $voter=implode(",",$_GET['voter']);
 if($_GET['nominee']=="None" && $_GET['G_nominee']){
  echo "You have to select at least two nominees";
 }else{
 foreach($_GET['nominee'] as $man){
  $message->send($fname,$man,$title,2);
 }
 foreach($_GET['G_nominee'] as $group){
  $employ->selectOne("edu",$group);
  if(mysqli_num_rows($employ->result)){
   while(mysqli_fetch_assoc($employ->result)){
    $message->send($fname,$row['fname'],$title,2);
   }
  }
 }
 $number=array();
 foreach($_GET['voter'] as $man){
  $plan->insert($date,"06:30","Voting",$man,$title,"Concerned",1);
  array_push($number, 0);
 }
 $number=implode(",",$number);
 $program->insert_voting($fname,$date,$title,$nominee,$voter,$number);
 }
 header("Location: myday.php");
}
?>