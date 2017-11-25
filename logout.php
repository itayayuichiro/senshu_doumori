<?php 
    require_once "./db_connect.php";
	$registration = false;
    $login = false;
    setcookie("login", 0);
	setcookie("name", null);
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>簡易掲示板</title>
	<link rel="stylesheet" href="">
	<link rel="stylesheet" href="./bootstrap.css">

</head>
<body>
<div class="container">
	<p>ログアウトしました</p>
	<a href="./login.php" title="">ログイン画面に戻る</a>	
</div>
</body>
</html>
