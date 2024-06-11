<?php
include '../class.php';
$task=new task;
$conn=mysqli_connect("localhost","root","","job");

$fname=$_COOKIE['fname'];
$emeet=$_COOKIE['emeet'];
$sql="select * from `message` where `to`='".$emeet."' order by `time`";
$result=mysqli_query($conn,$sql);
if(mysqli_num_rows($result)==0){
 echo "No Conversation Here Yet!";
}else{
 while($row=mysqli_fetch_assoc($result)){
  echo "<div class='jumbotron'";
  if($_COOKIE['fname']==$row['from']){
  echo " style='background:teal;color:white;'";
  }else if($row['content']=="End_Meet"){
  echo " style='background:red;color:white;'";
  }
  echo "><table><tr><td style='width:75;'><p><b>".$row['from']."</b></p>".$row['content']."</td><td style='width:25%;'>".$row['time']."</td></tr></table></div>";
 }
}