<?php
require_once 'class/Image.php';
$app = new Image;

if (isset($_POST['upload'])) {
    $image      = $_FILES['image']['name'];
    $image_tmp  = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_ext  = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    // check if file is empty
    if (empty($image)) {
        header("Location: ./?upload&error=Select image to upload.");
        die;
    }

    // valid image extension
    $valid_ext = ['jpeg', 'jpg', 'JPEG', 'JPG', 'png', 'PNG'];

    // check if image has valid extension
    if (in_array($image_ext, $valid_ext)) {
        // check image size
        if ($image_size > 100000) {
            header("Location: ./?upload&error=Image must not be more than 100kb.");
            die;
        }

        // if there is no error, go to upload method in Image.php class
        if ($app->upload($image, $image_tmp, $image_ext)) {
            header("Location: ./");
        }
    } else {
        header("Location: ./?upload&error=Image doesn't have a valid extension.");
    }
}

if (isset($_POST['update'])) {
    $image      = $_FILES['image']['name'];
    $image_tmp  = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_ext  = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $id         = $_POST['id'];

    if (empty($image)) {
        header("Location: ./?upload&error=Select image to upload.");
        die;
    }

    // valid image extension
    $valid_ext = ['jpeg', 'jpg', 'JPEG', 'JPG', 'png', 'PNG'];

    // check if image has valid extension
    if (in_array($image_ext, $valid_ext)) {
        // check image size
        if ($image_size > 100000) {
            header("Location: ./?upload&error=Image must not be more than 100kb.");
            die;
        }

        // if there is no error, go to update method in Image.php class
        if ($app->update($image, $image_tmp, $image_ext, $id)) {
            header("Location: ./");
        }
    } else {
        header("Location: ./?upload&error=Image doesn't have a valid extension.");
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // go to delete method
    if ($app->delete($id)) {
        header("Location: ./");
    }
}