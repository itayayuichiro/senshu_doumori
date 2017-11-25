<?php 
    require_once "./db_connect.php";
    $registration = false;
    phpinfo();
    if (!empty($_GET['id'])) {
		$sql = "select * from users where id = '{$_GET['id']}'";
	    $result = mysql_query($sql);
	    $row = mysql_fetch_row($result);
	    if (!empty($row)) {
	        $sql = "update users set flag = 1 where id = '{$_GET['id']}'";
	        echo $sql;
	        echo "本登録が完了しました<br>";
	        $result = mysql_query($sql);
		    $registration = true;
		    $login = true;
    	    setcookie("login", 1);
	    	setcookie("name", $row[2]);
	    }
	 }else if (!empty($_POST['name']) && !empty($_POST['password'])) {
	    $id = uniqid("ID");
	    $sql  = "INSERT INTO users(id,password,name) VALUES ('".$id."','".$_POST['password']."','".$_POST['name']."');";
	    $result = mysql_query($sql);
	    $login = true;
		mb_language("japanese");
		mb_internal_encoding("UTF-8");
		//日本語メール送信
		$to = $_POST['email'];
		$subject = "掲示板仮登録";
		$body = "仮登録が完了しました、URLをクリックした本登録を完了させてください\nhttp://co-778.it.99sv-coco.com/mission_3-9.php?id=".$id;
		$from = "keiji@example.com";
		//ちゃんと日本語メールが送信できます
		mb_send_mail($to,$subject,$body,"From:".$from);
    }else{
	    $login = false;
    }
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>入力フォーム</title>
	<link rel="stylesheet" href="">
    <link rel="stylesheet" href="./bootstrap.css">
</head>
<body>
    <div class="container">         
         <?php 
         	if ($login == false) {
        ?>
        <h1>簡易掲示板</h1>
            新規登録フォーム<br>
            <form action="./new.php" method="post" accept-charset="utf-8">
            	mail：<input type="email" class="form-control" name="email" value="" placeholder=""><br>
                名前：<input type="text" class="form-control"  name="name" value="" placeholder=""><br>
                パスワード：<input type="password" name="password" class="form-control" value="" placeholder=""><br>
                <button type="submit" class="btn btn-primary">会員登録</button>
            </form>
            <a href="./login.php" title="">ログイン</a>
        <?php
         	}else if($registration == false){
         		$sql  = "select * from users where id ='".$id."'";
         		 $result = mysql_query($sql);
        	    $row = mysql_fetch_row($result);
        	    #print_r($row);
         ?>
            登録情報<br>
            ID:<?php echo $row[0]; ?><br>
            name:<?php echo $row[1]; ?><br>
            パスワード:<?php echo $row[2]; ?><br>
            入力したメールアドレス宛てにメールを送りました。<br>
            メールを確認して、本登録を完了させてください。
        <?php
         		
         	}else{
        ?>
            本登録が完了しました。
            <a href="./board.php">掲示板へ移動</a>
        <?php

         	}
         ?>
     </div>

</body>
</html>
