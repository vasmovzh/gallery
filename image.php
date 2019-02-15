<?php
/**
 * Created by PhpStorm.
 * User: vasmovzh
 * Date: 2019-02-07
 * Time: 12:23
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$id = 0;
$url = $_SERVER["REQUEST_URI"];
$urlPartsArray = explode("/", $url);
$key = array_search("image", $urlPartsArray);
if ($key) {
    $id = $urlPartsArray[$key + 1];
    array_splice($urlPartsArray, $key);
    $path = implode("/", $urlPartsArray)."/";
}

include "include/const.php";
if ($linkDB) echo "OK";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Image #<?php echo $id;?></title>
</head>
<body>
<h1>Image description and it's description</h1>
<img src="<?php echo "{$path}img/full/{$id}";?>.jpg" alt="here shows image <?php echo $id;?>.jpg">
<p>
    <?php
    $pathToFile = "img/description/{$id}_desc.txt";
    if (file_exists($pathToFile)) {
        $description = file_get_contents($pathToFile);
        if ($description == "") echo "No description!";
        else echo $description;
    }

    $linkDB = mysqli_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);
    mysqli_select_db($linkDB, MYSQL_DB);

    $query = "UPDATE img_info SET popularity=popularity+1 WHERE id = '{$id}'";
    mysqli_query($linkDB, $query);

    ?>
</p>

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

<p><a href="<?php echo $path;?>index.php"><<< Go Back</a></p>
</body>
</html>
