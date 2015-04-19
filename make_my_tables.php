<?php
include_once("php_includes/db_con.php");

$tbl_users= "CREATE TABLE IF NOT EXISTS users (
             id INT(11) NOT NULL AUTO_INCREMENT,
             username VARCHAR(16) NOT NULL,
             email VARCHAR(255) NOT NULL,
             password VARCHAR(255) NOT NULL,
             gender ENUM('m','f') NOT NULL,
             website VARCHAR(255) NULL,
             country VARCHAR(255) NULL,
             userlevel ENUM('a','b','c','d') NOT NULL DEFAULT 'a',
             avatar VARCHAR(255) NULL,
             ip VARCHAR(255) NOT NULL,
             signup DATETIME NOT NULL,
             lastlogin DATETIME NOT NULL,
             notescheck DATETIME NOT NULL,
             activated ENUM('0','1') NOT NULL DEFAULT '0',
             PRIMARY KEY (id),
             UNIQUE KEY username (username,email)
             )";

$query=mysqli_query($db_con,$tbl_users);

if($query === TRUE){
    echo "user table:)";
}else{
echo "user table:(";
}
////////////////////////////////////////
$tbl_useroptions="CREATE TABLE IF NOT EXISTS useroptions (
             id INT(11) NOT NULL,
             username VARCHAR(16) NOT NULL,
             background VARCHAR(255) NULL,
             question VARCHAR(255) NULL,
             answer VARCHAR(255) NULL,
             email VARCHAR(255) NOT NULL,
             UNIQUE KEY username(username)
             )";

$query=mysqli_query($db_con,$tbl_useroptions);

if($query === TRUE){
    echo "tbl_usroptions table:)";
}else{
echo "tbl_usroptions table:(";
}
////////////////////////////////////////
$tbl_friends="CREATE TABLE IF NOT EXISTS friends (
             id INT(11) NOT NULL AUTO_INCREMENT,
             user1 VARCHAR(16) NOT NULL,
             user2 VARCHAR(16) NOT NULL,
             datetime DATETIME NOT NULL,
             accepted ENUM('0','1') NOT NULL DEFAULT '0',
             PRIMARY KEY (id)
             )";

$query=mysqli_query($db_con,$tbl_friends);

if($query === TRUE){
    echo "tbl_friends table:)";
}else{
echo "tbl_friends table:(";
}

////////////////////////////////////////
$tbl_blockedusers="CREATE TABLE IF NOT EXISTS blockedusers (
             id INT(11) NOT NULL AUTO_INCREMENT,
             blocker VARCHAR(16) NOT NULL,
             blockee VARCHAR(16) NOT NULL,
             blockdate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";

$query=mysqli_query($db_con,$tbl_blockedusers);

if($query === TRUE){
    echo "tbl_blockedusers table:)";
}else{
echo "tbl_blockedusers table:(";
}
////////////////////////////////////////

$tbl_status="CREATE TABLE IF NOT EXISTS status (
             id INT(11) NOT NULL AUTO_INCREMENT,
             osid INT(11) NOT NULL,
             account_name VARCHAR(16) NOT NULL,
             author VARCHAR(16) NOT NULL,
             type ENUM('a','b','c') NOT NULL,
             data TEXT NOT NULL,
             postdate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";

$query=mysqli_query($db_con,$tbl_status);

if($query === TRUE){
    echo "tbl_status table:)";
}else{
echo "tbl_status table:(";
}

////////////////////////////////////////
$tbl_photos="CREATE TABLE IF NOT EXISTS photos (
             id INT(11) NOT NULL AUTO_INCREMENT,
             user VARCHAR(16) NOT NULL,
             gallery VARCHAR(16) NOT NULL,
             filename VARCHAR(255) NOT NULL,
             description VARCHAR(255) NULL,
             uploaddate DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";

$query=mysqli_query($db_con,$tbl_photos);

if($query === TRUE){
    echo "tbl_photos table:)";
}else{
echo "tbl_photos table:(";
}
////////////////////////////////////////
$tbl_notifications="CREATE TABLE IF NOT EXISTS notifications (
             id INT(11) NOT NULL AUTO_INCREMENT,
             username VARCHAR(16) NOT NULL,
             initiator VARCHAR(16) NOT NULL,
             app VARCHAR(255) NOT NULL,
             note VARCHAR(255) NOT NULL,
             did_read ENUM('0','1') NOT NULL DEFAULT '0',
             date_time DATETIME NOT NULL,
             PRIMARY KEY (id)
             )";

$query=mysqli_query($db_con,$tbl_notifications);

if($query === TRUE){
    echo "tbl_notifications table:)";
}else{
echo "tbl_notifications table:(";
}
?>
