<?php

require_once "../inc/conn.php";

if(isset($_POST['submit']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $query = "select * from posts where id=$id";
    $result = mysqli_query($conn , $query);
    if(mysqli_num_rows($result) == 1){
        $post = mysqli_fetch_assoc($result);
        $old_image = $post['image'];
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
        if(!empty($_FILES['image']['name'])){
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
        }else{
            $new_name = $old_image;
        }
        if(empty($errors)){

            $query = "update posts set title='$title' , body='$body' , image='$new_name' where id = $id";
            $result = mysqli_query($conn , $query);
            if($result){
            
                if(!empty($_FILES['image']['name'])){
                    unlink("../uploads/$old_image");
                    move_uploaded_file($tmp_name , "../uploads/$new_name");
                }
                
                $_SESSION['success'] = "post updated successfully";
                header("location:../viewPost.php?id=$id");
            }else{
                $_SESSION['errors'] = ["error while updating post"];
                header("location:../editPost.php?id=$id");
            }

        }else{
            $_SESSION['title'] = $title;
            $_SESSION['body'] = $body;
            $_SESSION['errors'] = $errors;
            header("location:../editPost.php?id=$id");
        }

    }else{
        header("location:../errors/404.php");
    }

}else{  
    header("location:../errors/404.php");
}