<?php
/*
 * @Author: Yaaprogrammer
 * @Date: 2022-05-12 14:17:14
 * @LastEditors: Yaaprogrammer
 * @LastEditTime: 2022-05-14 19:49:08
 * 
 * Copyright (c) 2022 by Yaaprogrammer, All Rights Reserved.
 */
$link = mysqli_connect('mysql_db_container', 'root', 'admin');
if (!$link) {
die('Could not connect: '. mysqli_connect_error());
}
echo 'Connected successfully';
mysqli_close($link);

?>