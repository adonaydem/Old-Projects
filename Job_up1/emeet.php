<?php
include 'class.php';
$task=new task;
$message=new message;
?>
<!DOCTYPE html>
<html>
<head>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        
        <script src = "js/bootstrap.min.js"></script>
<title></title>
<style type="text/css">
.fixed-top,.fixed-bottom{
        box-shadow:0 0 40px black;
        font-weight:bolder;
        position:relative;
        }
        .input{
        width:90%;
        font-family:arial;
        font-size:30px;
        }
        .fixed-bottom{
         margin-top: 0px;
        }
        .fixed-bottom form{
        width:100%;
        height:100px;
        position:relative;
        
        }
        .jumbotron{
        max-height:200px;
        width:50%;
        overflow:scroll;
        position:relative;
        }
       .view{
        width:100%;
        height:calc(80vh);
        overflow-y: scroll;
        background:gray;
        
       }
</style>
<script src="include/jquery-3.4.1.js"></script>
<script type="text/javascript">
  $(function() {
   
   $(".view").animate({
    scrollTop:$(".late").offset().top
   });
  });
</script>
</head>
<body>
<?php
echo '<nav class="navbar fixed-top container">';
echo "<p>Meetind ID: <b>".$_COOKIE['emeet']."</b></p>";
$task->select("id",$_COOKIE['emeet']);
$row=mysqli_fetch_assoc($task->result);
if($row['assigner']==$_COOKIE['fname']){
echo '<form><input type="hidden" name="id" value="'.$_COOKIE['emeet'].'"><button type="submit" name="end" class="btn btn-warning">End Meeting</button></form>';
}
echo '</nav><div id="view" class="view">';
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
echo '<div class="late"></div></div>';
echo '<nav class="navbar fixed-bottom ">';
echo '<form>
<input type="hidden" name="sender" value='.$_COOKIE['fname'].'>
<textarea type="text" class="input" name="message" placeholder="Write a message.. " required></textarea>
<button type="submit" class="btn btn-info" name="send">Send</button>
</form></nav>
';
?>
<script type="text/javascript">
var btn=document.getElementById("dropbtn");
var content=document.getElementById("content");
function = btn.onclick(){
 content.style.display="block";
}
</script>
</body>
</html>
<?php
if(isset($_GET['send'])){
$to=$_COOKIE['emeet'];
$from=$_GET['sender'];
$content=$_GET['message'];
$message->send($from,$to,$content,1);
header("Location: emeet.php?emeet='$to'");
}else if(isset($_GET['end'])){
  $message->send("Admin",$_GET['id'],"End_Meet",1);
  $task->delete("id",$_GET['id']);
  setcookie("endmeet",$_GET['emeet'],time()+8640000, "/","", 0);
  header("Location: homes/admin.php");
}
?>