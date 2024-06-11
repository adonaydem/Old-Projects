<?php
include 'class.php';
$employ=new employee;

if(isset($_POST['fname'])){
	$fname=$_POST['fname'];
 if(!empty($fname)){
 	$employ->selectOne("fname",$fname);
    if(mysqli_num_rows($employ->result)>0){
    	echo "Username already taken!";
    }
    
 }
}
if(isset($_POST['email'])){
	$email=$_POST['email'];
	if(!empty($email)){
		if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
			echo "Invalid E-mail!";
		}
	}
} 