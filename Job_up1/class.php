<?php
class dbh{
 protected function connect(){
  $conn=mysqli_connect("localhost","root","","job");
  return $conn;
 }
}

class employee extends dbh{
 private $sql;
 public $result;
 public function selectAll(){
  $this->sql="select * from `employee`";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function selectOne($type,$value){
  $this->sql="select * from `employee` where `".$type."`='$value'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($type,$set,$where,$id){
  $this->sql="update `employee` SET `".$type."`='$set' where `".$where."`='$id'";
  mysqli_query($this->connect(),$this->sql);
 }
 public function delete($type,$value){
  $this->sql="delete from `employee` where `".$type."`='$value'";
  mysqli_query($this->connect(),$this->sql);
 }
}

class assist extends dbh{
 private $sql;
 public $result;
 public function selectAll(){
 $this->sql="select * from `assist`";
 $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function selectOne($type,$value){
 $this->sql="select * from `assist` where `".$type."`='$value'";
 $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($type,$set){
 $this->sql="update `employee` SET `".$type."`='$set'";
 mysqli_query($this->connect(),$this->sql);
 }
}

class attend extends dbh{
 private $sql;
 public $result;
 public function insert($absent){
  $date=date("d/m/y",time());
  $this->sql="insert into `attend`(`date`,`absent`) values('$date','$absent')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function selectOne($type,$value){
  $this->sql="select * from `attend` where `".$type."`='$value'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($set,$value){
  $date=date("d/m/y",time());
  $this->sql="update `attend` SET `".$set."`='$value' where `date`='$date'";
  mysqli_query($this->connect(),$this->sql);
 }
 public function selectAll(){
  $this->sql="select * from `attend`";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
}

class plan extends dbh{
 private $sql;
 public $result;
 public function insert($date,$time,$sub,$call,$content,$concern,$access){
  $this->sql="insert into `plan`(`date`,`time`,`sub`,`call`,`content`,`concern`,`access`) values('$date','$time','$sub','$call','$content','$concern','$access')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function selectAll($limit){
  $this->sql="select * from `plan` order by `date` desc limit ".$limit.";";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function select($type,$value){
  $this->sql="select * from `plan` where `".$type."`='$value' ORDER BY `time`";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function delete($type,$value){
  $date=date("d/m/y",time());
  $this->sql="delete from `plan` where `".$type."`='$value'";
  mysqli_query($this->connect(),$this->sql);
 }
 public function allow($type,$value){
  $date=date("d/m/y",time());
  $this->sql="update `plan` SET `access`='1' where `".$type."`='$value' AND `date`='$date' AND `call`='All'";
  mysqli_query($this->connect(),$this->sql);
 }
}

class message extends dbh{
 private $sql;
 public $result;
 public $i=0; 
 public function send($from,$to,$content,$reply){
  $this->sql="insert into `message`(`from`,`to`,`content`,`reply`) values('$from','$to','$content','$reply')";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function view($value){
  $this->sql="select * from `message` where `to`='$value' order by `time` desc";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($type,$value,$where,$value2){
  $this->sql="update `message` SET `".$type."`='$value' where `".$where."`='$value2'";
  mysqli_query($this->connect(),$this->sql);
 }
 public function delete($type,$value){
  $this->sql="delete from `message` where `".$type."`='$value'";
  mysqli_query($this->connect(),$this->sql);
 }
 public function five($value1,$value2){
  $this->sql="select * from `message` where `from`='$value1' AND `to`='$value2' AND `reply`=5";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function recent($fname){
  $this->sql="select * from `message` where `to`='$fname'";
  $this->result=mysqli_query($this->connect(),$this->sql);
  while($row=mysqli_fetch_assoc($this->result)){
   $time=explode(" ",$row['time']);
   if(date("20y-m-d",time())==$time[0]){
    $this->i++;
   }
  }
 }
}

class month extends dbh{
 private $sql;
 public $result;
 public function insert($sub,$content,$concern){
  $date=date("m",time());
  $this->sql="insert into `month`(`sub`,`content`,`concern`,`month`) values('$sub','$content','$concern','$date')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function view(){
  $date=date("m",time());
  $this->sql="select * from `month` where `month`='$date'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($id,$sub,$content,$concern,$progress){
  $date=date("m",time());
  $this->sql="update `month` SET `sub`='$sub',`content`='$content',`concern`='$concern',`progress`='$progress' where `month`='$date' AND `id`='$id'";
  mysqli_query($this->connect(),$this->sql);
 }
 public function delete($presub){
  $this->sql="delete from `month` where `sub`='$presub'";
  mysqli_query($this->connect(),$this->sql);
 }
}

class rating extends dbh{
 private $sql;
 public $result;
 public function insert($value){
  $this->sql="insert `rating`(`name`) values('$value')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function select($type,$value){
  $this->sql="select * from `rating` where `".$type."`='$value'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function selectAll(){
  $this->sql="select * from `rating` order by `number` desc";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($type,$value,$where,$value2){
  $this->sql="update `rating` SET `".$type."`='$value' where `".$where."`='".$value2."'";
  mysqli_query($this->connect(),$this->sql);
 }
}

class task extends dbh{
 private $sql;
 public $result;
 public function insert($assigner,$assigned,$time,$content,$status){
 $date=date("d/m",time());
  $this->sql="insert into `task`(`assigner`,`assigned`,`duration`,`content`,`status`,`date`) values('$assigner','$assigned','$time','$content','$status','$date')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function selectAll(){
  $this->sql="select * from `task` order by `id` desc";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function select($type,$value){
  $this->sql="select * from `task` where `".$type."`='$value'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($type,$set,$where,$value){
  $this->sql="update `task` SET `".$type."`='$set' where `".$where."`='$value'";
  mysqli_query($this->connect(),$this->sql);
 }
 public function delete($type, $value){
  $this->sql="delete from `task` where `".$type."`='$value'";
  mysqli_query($this->connect(),$this->sql);
 }
}

class payroll extends dbh{
 private $sql;
 public $result;
 public function selectAll($value){
  $this->sql="select * from `".$value."`";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function select($id){
  $this->sql="select * from `payroll` where `id`='$id'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function select_allow($id){
  $this->sql="select * from `acc_allow` where `id`='$id'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function pay($people,$basic,$price,$punish){
  $date=date("d/m/y",time());
  $this->select(1);
  $row=mysqli_fetch_assoc($this->result);
  $home=$row['home'];
  $bonus=$row['bonus'];
  $comm=$row['comm'];
  $gross=$basic+$price+$home+$bonus;
  $net=$gross-$comm-$punish;
  $this->sql="insert into `payroll`(`people`,`date`,`basic`,`home`,`price`,`bonus`,`gross`,`comm`,`punish`,`net`) values( '$people','$date','$basic','$home','$price','$bonus','$gross','$comm','$punish','$net')";
  mysqli_query($this->connect(),$this->sql);
  $people=explode(",",$people);
  foreach($people as $person){
   $this->buss("withdraw",$person,$net,"Payroll");
  }
 }
 public function auto($home,$bonus,$comm){
  $this->sql="update `payroll` set `home`='$home',`bonus`='$bonus',`comm`='$comm' Where `id`=1";
  mysqli_query($this->connect(),$this->sql);
 }
 public function allow($people,$reason,$amount,$period){
  $date=date("d/m/y",time());
  $this->sql="insert into `acc_allow`(`date`,`people`,`amount`,`reason`,`period`) values('$date','$people','$amount','$reason','$period')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function all_acc(){
  $this->sql="select * from `acc_allow`";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function last($table,$limit){
  $this->sql="select * from `".$table."` order by `id` desc limit ".$limit."";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function buss($type,$fname,$amount,$reason){
  $date=date("d/m/y",time());
  $this->last("capital",1);
  $row=mysqli_fetch_assoc($this->result);
  if($type=="deposit"){
   $net=$row['total']+$amount;
   $this->sql="insert into `capital`(`fname`,`date`,`withdraw`,`deposit`,`reason`,`total`) values('$fname','$date',0,'$amount','$reason','$net')";
   mysqli_query($this->connect(),$this->sql);
  }else if($type=="withdraw"){
   $net=$row['total']-$amount;
   $this->sql="insert into `capital`(`fname`,`date`,`withdraw`,`deposit`,`reason`,`total`) values('$fname','$date','$amount',0,'$reason','$net')";
   mysqli_query($this->connect(),$this->sql);
  }
 }
 public function update_allow($value,$id){
  $this->sql="update `acc_allow` set `people`='$value' where `id`='$id'";
  mysqli_query($this->connect(),$this->sql);
 }
}
class done extends dbh{
 private $sql; 
 public $result;
 public function insert($date,$id,$fname,$status){
  $this->sql="delete from `done` where `task_no`='$id' AND `fname`='$fname'";
  mysqli_query($this->connect(),$this->sql);
  $this->sql="insert into `done` values('$date','$id','$fname','$status')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function select($type,$value){
  $this->sql="select * from `done` where `".$type."`='$value'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update($type,$set,$where,$value){
  $this->sql="update `done` set `".$type."`='$set' where `".$where."`='$value'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
}
class program extends dbh{
 private $sql; 
 public $result;
 public function insert_voting($fname,$date,$title,$nominee,$voter,$number){
  $this->sql="insert into `voting`(`fname`,`date`,`title`,`nominee`,`voter`,`number`) values('$fname','$date','$title','$nominee','$voter','$number')";
  mysqli_query($this->connect(),$this->sql);
 }
 public function select_voting($type,$value){
  $this->sql="select * from `voting` where `".$type."`='$value'";
  $this->result=mysqli_query($this->connect(),$this->sql);
 }
 public function update_voting($type, $set, $where, $value){
  $this->sql="update `voting` set `".$type."`='$set' where `".$where."`='$value'";
  mysqli_query($this->connect(),$this->sql);
 }
}
?>