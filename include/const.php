<?php
/**
 * Created by PhpStorm.
 * User: vasmovzh
 * Date: 2019-02-11
 * Time: 09:19
 */
define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "root");
define("MYSQL_PASSWORD", "root");
define("MYSQL_DB", "gallery");
define("MYSQL_PORT", "8889");
define("IMG_FOLDER", "img/full/");
define("THUMB_FOLDER", "img/thumb/");
define("DESC_FOLDER", "img/description/");

$linkDB = mysqli_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);
mysqli_select_db($linkDB, MYSQL_DB);

const IMAGE_HANDLERS = [
    IMAGETYPE_BMP => [
        "load" => "imagecreatefrombmp",
        "save" => "imagebmp"
    ],
    IMAGETYPE_JPEG => [
        "load" => "imagecreatefromjpeg",
        "save" => "imagejpeg"
    ],
    IMAGETYPE_PNG => [
        "load" => "imagecreatefrompng",
        "save" => "imagepng"
    ],
    IMAGETYPE_GIF => [
        "load" => "imagecreatefromgif",
        "save" => "imagegif"
    ]
];