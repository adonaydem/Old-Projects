<?php
include 'class.php';
$list=new employee;
$plan=new plan;
$rating=new rating;
$task=new task; 
$message=new message; 
$month=new month; 
$conn=mysqli_connect("localhost","root","","job");
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
<?php
if(isset($_GET['type'])){
 if($_GET['type']=="head"){
  if(isset($_GET['type']) && isset($_GET['pro'])){
   echo '<div class="jumbotron" style="width:50%;background-color:white;border:1px solid lightgray"><form><select class="form-control" name="fname">';
   $list->selectOne("edu",$_GET['pro']);
   while($row=mysqli_fetch_assoc($list->result)){
    echo '<option>'.$row['fname'].'</option>';
   }
   echo '</select>
   <input type="hidden" name="edu" value="'.$_GET['pro'].'">
   <button type="submit" name="update_head" class="btn btn-primary">Update</button></form></div>';
  }else{
  echo '<div class="jumbotron" style="width:50%;background-color:white;border:1px solid lightgray"><form>
    General:
    <select name="0" class="form-control">';
     $list->selectOne("edu",0);
     while($row=mysqli_fetch_assoc($list->result)){
      echo '<option>'.$row['fname'].'</option>';
     }
    echo '</select>
    Marketing and Sales:
    <select name="1" class="form-control">';
    $list->selectOne("edu",1);
    while($row=mysqli_fetch_assoc($list->result)){
    echo '<option>'.$row['fname'].'</option>';
    }
    echo '</select>
    Production:
    <select name="2" class="form-control">';
    $list->selectOne("edu",2);
    while($row=mysqli_fetch_assoc($list->result)){
    echo '<option>'.$row['fname'].'</option>';
    }
    echo '</select>
    Accounting and Finance:
    <select name="3" class="form-control">';
    $list->selectOne("edu",3);
    while($row=mysqli_fetch_assoc($list->result)){
    echo '<option>'.$row['fname'].'</option>';
    }
    echo '</select>
    Human Resource:
    <select name="4" class="form-control">';
    $list->selectOne("edu",4);
    while($row=mysqli_fetch_assoc($list->result)){
    echo '<option>'.$row['fname'].'</option>';
    }
    echo '</select>
    Personnel:
    <select name="5" class="form-control">';
    $list->selectOne("edu",5);
    while($row=mysqli_fetch_assoc($list->result)){
    echo '<option>'.$row['fname'].'</option>';
    }
    echo '</select><button type="submit" name="insert_head" class="btn btn-primary">Insert</button>
  </form></div>';
  }
 }else if($_GET['type']=="month"){
  echo "<div class='jumbotron' id='modalEdit'>
  <form>
  <input type='hidden' name='id' class='form-control' value='".$_GET['id']."' required><br>
  <input type='text' name='sub' class='form-control' value='".$_GET['sub']."' placeholder='Subject' required><br>
  <input type='text' name='content' class='form-control' value='".$_GET['content']."' placeholder='Content' required><br>
  <input type='text' name='concern' class='form-control' value='".$_GET['concern']."' placeholder='Concerns' required><br>
  <input type='number' name='progress' class='form-control' value='".$_GET['progress']."' placeholder='Progress out 100' required><br>
  <button type='submit' name='edit' class='btn btn-primary'>Update</button>
  </form>
  </div>";
 }
}
?>
</body>
</html>
<?php
if(isset($_GET['update_head'])){
 $list->selectOne("access",$_GET['edu']);
 if(mysqli_num_rows($list->result)>0){
  $row=mysqli_fetch_assoc($list->result);
  $list->update("access","","fname",$row['fname']);
  if($_GET['fname']!=$row['fname']){
  $message->send("Admin",$row['fname'],"You have been out from your place of heads",0);
  }
 }
 $list->update("access",$_GET['edu'],"fname",$_GET['fname']);
 $message->send("Admin",$_GET['fname'],"You have been instated as a head <small>Reply for additional info.</small>",1);
 header("Location: list.php");
}else if(isset($_GET['insert_head'])){
 $list->update("access",0,"fname",$_GET[0]);
 $list->update("access",1,"fname",$_GET[1]);
 $list->update("access",2,"fname",$_GET[2]);
 $list->update("access",3,"fname",$_GET[3]);
 $list->update("access",4,"fname",$_GET[4]);
 $list->update("access",5,"fname",$_GET[5]);
 header("Location: list.php");
}else if(isset($_GET['edit'])){
 $id=$_GET['id'];
 $sub=$_GET['sub'];
 $content=$_GET['content'];
 $concern=$_GET['concern'];
 $progress=$_GET['progress'];
 $month->update($id,$sub,$content,$concern,$progress);
 header("Location:myday.php");
}
?>