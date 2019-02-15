<?php
/**
 * Created by PhpStorm.
 * User: vasmovzh
 * Date: 2019-02-06
 * Time: 14:04
 */
include "const.php";

function filesCount($path) {
    $count = 0;
    $dir = opendir($path);

    while ($file = readdir($dir)) {
        if ($file == "." or $file == ".." or is_dir($path.$file) or $file == ".DS_Store")
            continue;
        $count++;
    }

    return $count;
}

function filesNames($path) {
    $names = array();
    $dir = opendir($path);

    while ($file = readdir($dir)) {
        if ($file == "." or $file == ".." or is_dir($path.$file) or $file == ".DS_Store")
            continue;
        $names[] = basename($file);
    }

    return $names;
}

function uploadingFiles($file, $folder = IMG_FOLDER) {
    $marker = filesCount($folder) + 1;

    if ($file["name"] == "") {
        echo "Choose a file";
        die();
    }

    $imgInf = pathinfo($file["name"]);
    $ext = $imgInf["extension"];

    if (move_uploaded_file($file["tmp_name"], $folder . $marker . "." . $ext)) {
        $imageName = $marker . "." . $ext;
        $thumbName = $marker . "_thumb." . $ext;
        $descName =  $marker . "_desc.txt";

        $linkDB = mysqli_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);
        mysqli_select_db($linkDB, MYSQL_DB);

        $query = <<<QUERY
        INSERT INTO img_info VALUES (
        '{$marker}', '{$imageName}', '{$thumbName}', '{$descName}', '0'
        )
QUERY;

        mysqli_query($linkDB, $query);

        echo "File uploaded";
    }
    else echo "Error!";

    header("Location: index.php");
}

function makeThumbnail($src, $dest = THUMB_FOLDER) {
    $imageType = exif_imagetype($src);

    if (!$imageType or !IMAGE_HANDLERS[$imageType])
        return null;

    $sourceImage = call_user_func(IMAGE_HANDLERS[$imageType]["load"], $src);

    if (!$sourceImage)
        return null;

    $width = imagesx($sourceImage);
    $height = imagesy($sourceImage);

    $newWidth = 0.3 * $width;
    $newHeight = 0.3 * $height;

    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    imagecopyresampled(
        $newImage,
        $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $width, $height
    );

    call_user_func(IMAGE_HANDLERS[$imageType]["save"], $newImage, $dest);
}

function deletingFiles($key) {
    @unlink("img/description/{$key}_desc.txt");
    @unlink("img/full/{$key}.jpg");
    @unlink("img/thumb/{$key}_thumb.jpg");

    header("Location: index.php");
}

function renameFiles($arrayOfFilenames) {

    for ($i = 0; $i < count($arrayOfFilenames); $i++) {
        $numberInFileName = (int) $arrayOfFilenames[$i];
        rename("img/description/{$numberInFileName}_desc.txt", "img/description/{$i}_desc.txt");
        rename("img/full/{$numberInFileName}.jpg", "img/full/{$i}.jpg");
        rename("img/thumb/{$numberInFileName}_thumb.jpg", "img/thumb/{$i}_thumb.jpg");
    }

    header("Location: index.php");
}

function deleteAndRename($arrayOfFilenames, $key) {
    @unlink(DESC_FOLDER . "{$key}_desc.txt");
    @unlink(IMG_FOLDER . "{$key}.jpg");
    @unlink(THUMB_FOLDER . "{$key}_thumb.jpg");

    $linkDB = mysqli_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);
    mysqli_select_db($linkDB, MYSQL_DB);

    $query = "DELETE FROM img_info WHERE id='{$key}'";
    mysqli_query($linkDB, $query);

    for ($i = 0; $i < count($arrayOfFilenames); $i++) {
        $numberInFileName = (int)substr($arrayOfFilenames[$i], 0);
        $ext = stristr($arrayOfFilenames[$i], ".");

        if ($numberInFileName > $key) {
            $descRename = "{$i}_desc.txt";
            $imageRename = "{$i}{$ext}";
            $thumbRename = "{$i}_thumb{$ext}";

            rename(DESC_FOLDER . "{$numberInFileName}_desc.txt", DESC_FOLDER . $descRename);
            rename(IMG_FOLDER . "{$numberInFileName}{$ext}", IMG_FOLDER . $imageRename);
            rename(THUMB_FOLDER . "{$numberInFileName}_thumb{$ext}", THUMB_FOLDER . $thumbRename);

            $linkDB = mysqli_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);
            mysqli_select_db($linkDB, MYSQL_DB);

            $query = <<<QUERY
            UPDATE img_info SET id = '{$i}', img_name = '{$imageRename}', thumb_name = '{$thumbRename}', desc_name = '{$descRename}'
            WHERE id = '{$numberInFileName}'
QUERY;
            mysqli_query($linkDB, $query);
        }
    }

    header("Location: index.php");
}