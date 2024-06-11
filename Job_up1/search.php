<?php
 include 'class.php';
 $list=new employee;
 
if(isset($_POST['search'])){
$list->selectAll();
 $newname=$_POST['search'];
 if(!empty($newname)){
 while($row=mysqli_fetch_assoc($list->result)){
  $name=$row['fname'];
  if(strpos($name, $newname)!==false){
   echo "<tr>
   <td>".$row['id']."</td>
   <td>".$row['fname']."</td>
   <td>".$row['residence']."</td>
   <td>".$row['p_no']."</td>";
   if(!isset($_POST['att'])){
   echo "<td><form>
   <input type='hidden' name='name' value='".$row['fname']."'>
   <button type='submit' name='up' class='btn btn-warning'>Up</button>
   <button type='submit' name='down' class='btn btn-danger'>Down</button>
   </form></td>";
   }
   echo "<td><a href='user.php?id=".$row['id']."&&visit=".$_COOKIE['fname']."'>Detail</a></td>
   </tr>";
  }
 }
 }
}
 
 ?>