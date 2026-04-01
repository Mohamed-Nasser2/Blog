<?php

require_once "../inc/conn.php";

if(isset($_POST['submit'])){

    trim(htmlspecialchars(extract($_POST)));

    $errors = [];
    if(empty($title)){
        $errors[] = "title is required";
    }elseif(is_numeric($title)){
        $errors[] = "title must be string";
    }

    if(empty($body)){
        $errors[] = "body is required";
    }elseif(is_numeric($body)){
        $errors[] = "body must be string";
    }

    $image = $_FILES['image'];
    $image_name = $image['name'];
    $tmp_name = $image['tmp_name'];
    $image_error = $image['error'];
    $size = $image['size']/(1024*1024);
    $image_ext = strtolower(pathinfo($image_name,PATHINFO_EXTENSION));

    $ext = ["png" , "jpeg" , "jpg"];

    if($image_error != 0){
        $errors[] = "image not correct";
    }elseif($size > 1){        
        $errors[] = "image size must be less than 1 mb";
    }elseif(!in_array($image_ext , $ext)){
        $errors[] = "image type must be in (png , jpeg , jpg)";
    }
    $new_name = uniqid().".$image_ext";
    if(empty($errors)){

        $query = "insert into posts (`title` , `body` , `image`, `user_id`) values ('$title' , '$body' , '$new_name' ,'1')";
        $result = mysqli_query($conn , $query);
        if($result){
            move_uploaded_file($tmp_name , "../uploads/$new_name");
            $_SESSION['success'] = "post add successfully";
            header("location:../index.php");
        }else{
            $_SESSION['errors'] = ["error while add post"];
            header("location:../addPost.php");
        }

    }else{
        $_SESSION['title'] = $title;
        $_SESSION['body'] = $body;
        $_SESSION['errors'] = $errors;
        header("location:../addPost.php");
    }

}else{  
    header("location:../errors/404.php");
}