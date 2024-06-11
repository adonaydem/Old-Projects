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
</head>
<body>
<h3>Update Auto-payments</h3>
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
<h3>Payment</h3>
<form method="post">
<select name="people[]" multiple>
<?php
$employ->selectAll();
while($row=mysqli_fetch_assoc($employ->result)){
 echo "<option>".$row['fname']."</option>";
}
?>
</select><br>
<input type="number" name="basic" class="form-control"><br>
Special Price<br>
<input type="number" name="price" class="form-control"><br>
Lost Of Punishment<br>
<input type="number" name="punish" class="form-control"><br>
<button type="submit" name="pay" class="btn btn-success">Pay</button>
</form>
Withdrawal Allowance<br>
<?php
$employ->selectOne("access","acc");
if(mysqli_num_rows($employ->result)==0){
 echo "Accountants are not set!";
}else{
echo '
<form method="post">
<select name="people[]" multiple>';
 while($row=mysqli_fetch_assoc($employ->result)){
  echo '<option>'.$row['fname'].'</option>';
 }
 echo '
</select>
<textarea name="reason"></textarea>
 <input type="number" name="amount"><br>
 <input type="text" name="period"><br>
 <button name="withdraw" type="submit">Done</button>
</form>';
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
 $people=$_POST['people'];
 $reason=$_POST['reason'];
 $amount=$_POST['amount'];
 $period=$_POST['period'];
 $payroll->allow($people,$reason,$amount,$period);
}
?>
 