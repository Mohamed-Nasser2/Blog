<?php

require_once '../inc/conn.php';

if(isset($_POST['submit'])){
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));

    $errors=[];
    if(empty($email)){
        $errors[] = "email is required";
    }elseif(!filter_var($email , FILTER_VALIDATE_EMAIL)){
        $errors[] = "email must be email @";
    }

    if(empty($password)){
        $errors[] = "password is required";
    }elseif(strlen($password) < 6){
        $errors[] = "password must be more than 6 char";
    }

    if(empty($errors)){

        $query = "select * from users where email='$email'";
        $result = mysqli_query($conn , $query);
        if(mysqli_num_rows($result)==1){
            $user = mysqli_fetch_assoc($result);
            $old_password = $user['password'];
            $is_verified = password_verify($password , $old_password);
            if($is_verified){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['success'] = 'users login successfully';
                header("location:../index.php");
            }else{
                $_SESSION['errors'] = ["email or password not correct"];
                header("location:../login.php");
            }
        }else{
            $_SESSION['errors'] = ["email or password not correct"];
            header("location:../login.php");
        }

    }else{
        $_SESSION['email'] = $email;

        $_SESSION['errors'] = $errors;
        header("location:../login.php");
    }

}else{
    header('location:../errors/404.php');
}