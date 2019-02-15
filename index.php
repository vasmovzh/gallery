<?php
/**
 * Created by PhpStorm.
 * User: vasmovzh
 * Date: 2019-02-07
 * Time: 12:22
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

header("Content-type: text/html; encoding=utf-8");

include_once "include/funcs.php";
$namesOfPics = filesNames(THUMB_FOLDER);
sort($namesOfPics);

if ($linkDB) echo "OK";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image Galery</title>
</head>
<body>
<div>
    <?php
    var_dump($namesOfPics);
    echo "<br>";

    for ($i = 1; $i <= count($namesOfPics); $i++) {
        $numberOfPic = (int) substr($namesOfPics[$i -1], 0);
        $result = mysqli_query($linkDB,"SELECT popularity FROM img_info WHERE id = '{$numberOfPic}'");
        $pop = mysqli_fetch_assoc($result);

        echo "<div style='margin: 10px; display: inline-block'>";
        echo "<div>";
        echo "<a href=\"image/$numberOfPic/\">";
        echo "<img src=\"img/thumb/{$namesOfPics[$i-1]}\" alt=\"Thumbnail #$numberOfPic\">";
        echo "</a>";
        echo "</div>";
        echo "<form method='post' enctype='multipart/form-data'>";
        echo "<input type='hidden' name='action' value='delete'>";
        echo "<input type='hidden' name='number' value='$numberOfPic'>";
        echo "<input type='submit' value='Delete image'>";
        echo "</form>";
        echo "</div>";
//        echo $numberOfPic;

        echo $pop["popularity"];

        if ($i % 3 == 0) echo "<br>";
    }

    if ( isset($_POST["action"]) and $_POST["action"] == "delete" and isset($_POST["number"]) ) {
        deleteAndRename($namesOfPics, $_POST["number"]);
//        deletingFiles($_POST["number"]);
//        renameFiles($namesOfPics, $_POST["number"]);
    }
    ?>
</div>
<div>
    <h1>Upload Form</h1>
    <?php
    if (count($_POST)) {
        if (isset($_FILES["uploadedFile"])) {
            uploadingFiles($_FILES["uploadedFile"]);

            $marker = filesCount(IMG_FOLDER);

            $image = pathinfo(IMG_FOLDER . $marker . ".jpg");

            makeThumbnail(IMG_FOLDER . $marker . ".jpg", THUMB_FOLDER . $marker . "_thumb.jpg");

            if (isset($_POST["fileDescription"])) {
                $file = fopen(DESC_FOLDER . $marker . "_desc.txt", "w+");
                fwrite($file, $_POST["fileDescription"]);
                fclose($file);
            }
        }
    }
    ?>
    <div>
        <?php
            $result = mysqli_query($linkDB,"SELECT * FROM img_info");
            $arr[] = array();

            while($row = mysqli_fetch_assoc($result))
                $arr[] = $row;

            foreach ($arr as $row) {
                var_dump($row);
                echo "<br>";
            }
        ?>
    </div>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="uploadedFile"><br>
        <input type="text" name="fileDescription" placeholder="Description"><br>
        <input type="submit" value="Upload">
    </form>
</div>
</body>
</html>
