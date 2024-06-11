<?php
include "class.php";
$employ=new employee;
$assist=new assist;
$rating=new rating; 
$message=new message; 
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
      <script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
      <script src = "js/bootstrap.min.js"></script>
      <script src="include/jquery-3.4.1.js"></script>
      <style type="text/css">
      .modal {
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
       .modal-content {
       background-color: #fefefe;
       margin: auto;
       padding: 20px;
       border: 1px solid #888;
       width: 80%;
       height:350px;
       }
       
       /* The Close Button */
       .close {
       color: #aaaaaa;
       float: right;
       font-size: 28px;
       font-weight: bold; 
       }
       
       .close:hover,
       .close:focus {
       color: #000;
       text-decoration: none;
       cursor: pointer;
       
       }
       .input-error{
         margin-top:0;
         font-style: italic;
       }
       .submit-error,.del-error{
        color:white;
       }
      </style>
      <script type="text/javascript">
        $(function() {
          $("#fname").keyup(function() {
            $("#errorName").load("validator.php",{
              fname:$("#fname").val()
            });
          });

        $("#email").keyup(function() {
            $("#errorEmail").load("validator.php",{
              email:$("#email").val()
            });
        });
        var errorS=$(".submit-error").text();
        if(errorS!=""){
         alert(errorS);
        }
        var errorD=$(".del-error").text();
        if(errorD!=""){
        alert(errorD);
        }
        });
      </script>
     
</head>
<body>
<div class="jumbotron" style='width:50%'>
<h3>Register</h3><hr>
<form method="post">
<input type='text' name='fname' required class='form-control' id='fname' placeholder="First Name"><br>
<p id="errorName" class="input-error text-danger"></p>
<input type='text' name='residence' required class="form-control" placeholder="Residence"><br>
<input type='number' name='p_no' required class="form-control" placeholder="Phone Number"><br>
<input type='number' name='e_no' required class='form-control' placeholder="Emergency Contact Number"><br>
<select name='edu' required class="form-control">
<option value="0">General</option>
<option value="1">Marketing and Sales</option>
<option value="2">Production</option>
<option value="3">Accounting amd Finance</option>
<option value="4">Human Resource</option>
<option value="5">Personnel</option>
</select><br>
<input type='text' name='email' required class="form-control" id="email" placeholder="E-mail"><br>
<p id="errorEmail" class="input-error text-danger"></p>
<input type='number' name='bank' required class="form-control" placeholder="Bank Number"><br>
<input type='checkbox' name='rating' checked>Enter to rating<br>
<button type="submit" id='add-btn' name="regi" class="btn btn-primary">Register</button>
</form>
</div>
<hr>
<div class="jumbotron" style='width:50%'>
<h3>Remove</h3><hr>
<form method="post">
<input type='number' name='id' required class='form-control' placeholder="Identification No"><br>
<input type='text' name='fname' required class='form-control' placeholder="First Name"><br>
<input type='password' name='psw' required class='form-control' placeholder="Admin Password"><br>
<button type="submit" name="del" class="btn btn-danger">Remove</button><br><br>
</form>
<button id="multi" class="btn btn-primary">Multiple Selection</button>
</div>
<div class="modal" id="modal">
<div class="modal-content">
<span class="close">&times;</span>
<?php
$employ->selectAll();
if(mysqli_num_rows($employ->result)==0){
 echo "No Employees!";
}else{
 echo "<form method='post'>Select People:<br><select name='people[]' multiple class='form-control' required>";
 while($row=mysqli_fetch_assoc($employ->result)){
  echo "<option>".$row['fname']."</option>";
 }
 echo "</select>";
 echo "Admin Password:<br><input type='password' name='psw' required class='form-control' placeholder='Admin Password'><br><button type='submit' name='multi' class='btn btn-danger'>Remove</button>";
 echo "</form>";
}
?>
</div>
</div>
<script type="text/javascript">
var btn = document.getElementById("multi");
var modal = document.getElementById("modal");
var span = document.getElementsByClassName("close")[0];
btn.onclick=function(){
 modal.style.display="block";
}
span.onclick=function(){
 modal.style.display="none";
}
</script>
</body>
</html>
<?php
$conn=mysqli_connect("localhost","root","","job");
if(isset($_POST['regi'])){
$residence=str_replace(" ","",$_POST['residence']);
$fname=$_POST['fname'];
$email=$_POST['email'];
$p_no=str_replace(" ","",$_POST['p_no']);
$e_no=str_replace(" ","",$_POST['e_no']);
$bank=str_replace(" ","",$_POST['bank']);
$edu=str_replace(" ","",$_POST['edu']);
$rand=str_shuffle("abcd1234567890");
$subrand=substr($rand,0,6);
$psw=md5($subrand);
 if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
  echo "<p class='submit-error'>Incorrect Email!</p>";
 }else{
  $employ->selectOne("fname",$fname);
  if(mysqli_num_rows($employ->result)>0){
   echo "<p class='submit-error'>Username already taken!</p>";
   exit();
  }else /*if(!mail($email,"Welcome to Auto-Office System","Your password is ".$psw,"From: Auto-Office <ad@gmail.com>")){
    echo "There has been an error sending an email";
   }else*/{
   $sql="insert into `employee`(`fname`,`residence`,`email`,`p_no`,`e_no`,`bank`,`edu`,`psw`) values('$fname','$residence','$email','$p_no','$e_no','$bank','$edu','$psw')";
   mysqli_query($conn,$sql);
   if(isset($_POST['rating'])){
    $rating->insert($fname);
   }
  }
 }
}else if(isset($_POST['del'])){
 $fname=$_POST['fname'];
 $id=$_POST['id'];
 $psw=$_POST['psw'];
 $employ->selectOne("id",$id);
 if(mysqli_num_rows($employ->result)==0){
  echo "<p class='del-error'>Incorrect Identification!</p>";
 }else{
  $row=mysqli_fetch_assoc($employ->result);
  if($row['fname']!=$fname){
   echo "<p class='del-error'>Name and ID do not match! </p>";
  }else{
   $assist->selectOne("role","Attendance");
   $rowi=mysqli_fetch_assoc($assist->result);
   if($rowi['psw']!=$psw){
    echo "<p class='del-error'>Incorrect Password!</p>";
   }else{
    $employ->delete("id",$id);
   }
  }
 }
}else if(isset($_POST['multi'])){
 $people=$_POST['people'];
 $psw=$_POST['psw'];
 $assist->selectOne("role","Admin");
 $row=mysqli_fetch_assoc($assist->result);
 if($row['psw']!=$psw){
  echo "<p class='del-error'>Incorrect Password!</p>";
 }else{
  foreach($people as $man){
   $employ->delete("fname",$man);
  }
 }
}
?>