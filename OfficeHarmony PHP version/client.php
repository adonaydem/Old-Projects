<?php
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
      <script>
      $(function(){
      $("#email").keyup(function() {
      $("#errorEmail").load("validator.php",{
      email:$("#email").val()
      });
      });
     });
      </script>
</head>
<body>
<div class="jumbotron" style="background:white">
<div class="jumbotron" style="width:50%">
<h3>Register Client</h3><hr>
<form method="post">
<input type="text" name="fname" class="form-control" placeholder="Full Name"></br>
<input type="text" name="location" class="form-control" placeholder="Location"></br>
<input type="text" name="p_no" class="form-control" placeholder="Phone number"></br>
<input type="text" id="email" name="email" class="form-control" placeholder="E-mail"></br>
<p id="errorEmail" class="input-error text-danger" style="margin-top:0;font-style:italic"></p>
<select name="cate" class="form-control">
<option>A</option>
<option>B</option>
</select>
<button type="submit" name="insert" class="btn btn-default">Insert</button>
</form>
<h3>Register Client's Events</h3><hr>
<form method="post">
<select name="fname" class="form-control">
<?php
 $sql="select * from `client`";
 $result=mysqli_query($conn, $sql);
 if(mysqli_num_rows($result)>0){
  while($row=mysqli_fetch_assoc($result)){
   echo "<option>".$row['fname']."</option>";
  }
 }
?>
</select>
<textarea name="event" class="form-control" placeholder="Event..."></textarea>
<input type="checkbox" name="success">Was it a Success? <br>
<button type="submit" name="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
<div class="jumbotron">
<h3>Database</h3><hr>
<table class="table table-striped table-bordered">
<tr><th>Full Name</th><th>Location</th><th>Phone Number</th><th>Email</th><th>Category</th><th>Rating</th></tr>
<?php
$sql="select * from `client`";
$result=mysqli_query($conn, $sql);
if(mysqli_num_rows($result)>0){
  while($row=mysqli_fetch_assoc($result)){
   echo "<tr>
   <td>".$row['fname']."</td>
   <td>".$row['location']."</td>
   <td>".$row['p_no']."</td>
   <td>".$row['email']."</td>
   <td>".$row['category']."</td>
   <td>".$row['rating']."</td>
   </tr>";
  }
 }
?>
</table>
</div>
</body>
</html>
<?php
if(isset($_POST['insert'])){
 $fname=$_POST['fname'];
 $location=$_POST['location'];
 $p_no=$_POST['p_no'];
 $email=$_POST['email'];
 $cate=$_POST['cate'];
 $sql="insert into `client`(`fname`,`location`,`p_no`,`email`,`category`) values('$fname','$location','$p_no','$email','$cate')";
 mysqli_query($conn,$sql);
 header("Location: client.php");
}else if(isset($_POST['submit'])){
 $fname=$_POST['fname'];
 $event=$_POST['event'];
 $date=date("d/m/y",time());
 if(isset($_POST['success'])){
  $success=1;
 }else{
  $success=0;
 }
 $sql="insert into `c_event` values('$date','$fname','$event','$success')";
 mysqli_query($conn, $sql);
}
?>