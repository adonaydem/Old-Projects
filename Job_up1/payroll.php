<?php
include "class.php";
$employ=new employee;
$payroll=new payroll;
$message=new message;
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<link href = "css/bootstrap.min.css" rel = "stylesheet">
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src = "js/bootstrap.min.js"></script>
</head>
<body>
<div class="row">
<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 jumbotron">
<h3>Update Auto-payments</h3><hr>
<?php
$payroll->select(1);
$row=mysqli_fetch_assoc($payroll->result);
?>
<form method="post">
Home Rent<br>
<input type="number" name="home" class="form-control" value="<?php echo $row['home'];?>"><br>
Bonus<br>
<input type="number" name="bonus" class="form-control" value="<?php echo $row['bonus'];?>"><br>
Lost For Community<br>
<input type="number" name="comm" class="form-control" value="<?php echo $row['comm'];?>"><br>
<button type="submit" name="update" class="btn btn-warning">Update</button>
</form>
</div>
</div>
<div class="row">
<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 jumbotron">
<h3>Payment</h3><hr>
<form method="post">
Select Employees<br>
<select name="people[]" multiple class="form-control">
<?php
$employ->selectAll();
while($row=mysqli_fetch_assoc($employ->result)){
 echo "<option>".$row['fname']."</option>";
}
?>
</select><br>
Basic Pay<br>
<input type="number" name="basic" class="form-control"><br>
Special Price<br>
<input type="number" name="price" class="form-control"><br>
Lost Of Punishment<br>
<input type="number" name="punish" class="form-control"><br>
<button type="submit" name="pay" class="btn btn-success">Pay</button>
</form>
</div></div>

<div class="row">
<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 jumbotron">
<h3>Withdrawal Allowance</h3><hr>
<?php
$employ->selectOne("access",3);
if(mysqli_num_rows($employ->result)==0){
 echo "Accountants are not set!";
}else{
echo '
<form method="post">
Select Employees<br>
<select name="people[]" multiple class="form-control">';
 while($row=mysqli_fetch_assoc($employ->result)){
  echo '<option>'.$row['fname'].'</option>';
 }
 echo '
</select><br>
Reason<br>
<textarea name="reason" class="form-control"></textarea><br>
Amount<br>
 <input type="number" name="amount" class="form-control"><br>
 Period<br>
 <input type="text" name="period" class="form-control"><br>
 <button name="withdraw" type="submit" class="btn">Done</button>
</form></div></div>';
}
?>
</body>
</html>
<?php
if(isset($_POST['pay'])){
 $people=implode(",",$_POST['people']);
 $basic=$_POST['basic'];
 $price=$_POST['price'];
 $punish=$_POST['punish'];
 $payroll->pay($people,$basic,$price,$punish);
 foreach($_POST['people'] as $man){
 $message->send("Head Accountant",$man,"There Has Been Payment To Your Account!",6);
 }
 header("Location: payroll.php");
}else if(isset($_POST['update'])){
 $home=$_POST['home'];
 $comm=$_POST['comm'];
 $bonus=$_POST['bonus'];
 $payroll->auto($home,$bonus,$comm);
 header("Location: payroll.php");
}else if(isset($_POST['withdraw'])){
 $people=implode(",",$_POST['people']);
 $reason=$_POST['reason'];
 $amount=$_POST['amount'];
 $period=$_POST['period'];
 $payroll->allow($people,$reason,$amount,$period);
 header("Location: payroll.php");
}
?>
 