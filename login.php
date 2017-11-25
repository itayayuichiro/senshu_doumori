<?php 
    require_once "./db_connect.php";
	require_once("./phpQuery-onefile.php");

    session_start();
    if (!empty($_POST['login_id']) && !empty($_POST['login_pass'])) {
		$base_url = 'http://ity-y.sakura.ne.jp';
		$query = ['id'=>$_POST['login_id'],'pass'=>$_POST['login_pass']];

		$response = file_get_contents(
		                  $base_url.'/senshu/senshu_api.php?' .
		                  http_build_query($query)
		            );
		// 結果はjson形式で返されるので
		if ($response) {
			$phpQueryObj = phpQuery::newDocument($response);
			$h1 = $phpQueryObj['h1'];
			if (pq($h1)->text()=="専修大学ポータル") {
				echo "<script>alert('ログイン認証失敗');</script>";
			}else{
//				echo $_SERVER["HTTP_HOST"];
				$_SESSION['login'] = 1;
				$_SESSION['name'] = $_POST['login_id'];
				$url = "http://ity-y.sakura.ne.jp/senshu_doumori/board.php";
				header('Location: ' . $url, true , 301);
			}			
		}

    }
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>【専修大学限定】どう森掲示板</title>
	<link rel="stylesheet" href="./bootstrap.css">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
<div class="container">
	<h2>【専大限定】<br>どう森掲示板	<img src="./leaf.png" alt="" width="10px"></h2>
		    <form action="./login.php" method="post" accept-charset="utf-8">
		        学籍番号：<input type="text"  class="form-control" name="login_id" value="" placeholder=""><br>
		        パスワード：<input type="password"  class="form-control" name="login_pass" value="" placeholder=""><br>
		        <button type="submit" class="btn btn-primary">ログイン</button>
		    </form>
		   
  </div>
</body>
<style>
	
</style>
</html>