<?php
include "class.php";
$payroll=new payroll;
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
<div class="jumbotron">
<h3>Capital</h3><hr>
<?php
$payroll->last("capital",1);
$row=mysqli_fetch_assoc($payroll->result);
echo "<h3 style='text-align:center;font-size:50px'>Net Amount: ".$row['total']." Birr</h3>";
?>
<h6>Last Transactions</h6>
<table class="table">
<tr>
<th>Name</th><th>Date</th><th>Withdrawal</th><th>Deposit</th><th>Reason</th><th>Total</th>
</tr>
<?php
$payroll->last("capital",10);
while($row=mysqli_fetch_assoc($payroll->result)){
 echo "<tr>
 <td>".$row['fname']."</td>
 <td>".$row['date']."</td>
 <td>".$row['withdraw']."</td>
 <td>".$row['deposit']."</td>
 <td>".$row['reason']."</td>
 <td>".$row['total']."</td>
 </tr>";
}

?>
</table>
</div>
<div class="jumbotron">
<h3>Payroll</h3><hr>
<table class="table">
<tr>
<th>ID</th><th>Date</th><th>People</th><th>Basic</th><th>Gross</th><th>Net</th>
</tr>
<?php
$payroll->last("payroll",10);
while($row=mysqli_fetch_assoc($payroll->result)){
 echo "<tr>
 <td>".$row['id']."</td>
 <td>".$row['date']."</td>
 <td>".$row['people']."</td>
 <td>".$row['basic']."</td>
 <td>".$row['gross']."</td>
 <td>".$row['net']."</td>
 </td>";
}
?>
</table>
</div>
</body>
</html>