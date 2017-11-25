<?php 
    require_once "./db_connect.php";
    session_start();
    if (!empty($_POST['login_id']) && !empty($_POST['login_pass'])) {
    	$sql = "select * from users where id = '{$_POST['login_id']}' and password = '{$_POST['login_pass']}' and flag = 1";
	    $result = mysql_query($sql);
	    $row = mysql_fetch_row($result);
	    if (!empty($row)) {
	    	$_SESSION['login'] = 1;
	    	$_SESSION['name'] = $row[2];
    		$login = true;
    		$name = $row[2];
	    }
    }
    if (!empty($login) || $_SESSION['login'] == 1) {
		$edit_flag = false;
		$name = $_SESSION['name'];    
	  if (!empty($_POST['comment']) && empty($_POST['edit_num']) ) {
	    $d = date('Y/m/d H:i:s');
	    $sql = "INSERT INTO `chat` (`name`, `comment`, `password`, `created_at`) VALUES (\"{$_SESSION['name']}\",\"{$_POST['comment']}\",\"{$_POST['password']}\", \"{$d}\");";
	    //print_r($_FILES);
	    if (!empty($_FILES["file"])) {
	    	//echo "ファイル添付されてる";
		    $fp = fopen($_FILES["file"]["tmp_name"], "rb");
			$imgdat = fread($fp, filesize($_FILES["file"]["tmp_name"]));
			fclose($fp);
			$imgdat = base64_encode($imgdat);
			$imagetype = $_FILES['file']['type'];
			$array = array('image/gif','image/jpeg','image/png');
		    	
			if (in_array($imagetype,$array,true)) {
			    $sql = "INSERT INTO `chat` (`name`, `comment`, `password`,`image_file`, `created_at`) VALUES (\"{$_SESSION['name']}\",\"{$_POST['comment']}\",\"{$_POST['password']}\",\"{$imgdat}\", \"{$d}\");";	
	        }else if(in_array($imagetype, array('video/mp4', 'video/webm'), true)){
			    $sql = "INSERT INTO `chat` (`name`, `comment`, `password`,`video_file`, `created_at`) VALUES (\"{$_SESSION['name']}\",\"{$_POST['comment']}\",\"{$_POST['password']}\",\"{$imgdat}\", \"{$d}\");";	
	        }
	    }
	    $result = mysql_query($sql);
	  }else if(!empty($_POST['comment']) && !empty($_POST['edit_num'])){
	    $sql = "select * from chat where id = {$_POST['edit_num']}";
	    $result = mysql_query($sql);
	    $content = mysql_fetch_row($result);
	    if ($_POST['password']  == $content[3]) {
	        $sql = "update chat set name = \"{$_SESSION['name']}\",comment = \"{$_POST['comment']}\" where id = {$_POST['edit_num']}";
	        echo "更新しました<br>";
	        $result = mysql_query($sql);
	    }else{
	        echo "パスワードが違うため、編集に失敗しました。<br>";
	    }
	  }else if (empty($_POST['comment']) && !empty($_POST['del_number'])) {

	    $sql = "delete from chat where id = {$_POST['del_number']}";
	    $result = mysql_query($sql);
	  }else if(empty($_POST['comment']) && empty($_POST['del_number']) && !empty($_POST['edit_id'])){
	    $sql = "select * from chat where id = {$_POST['edit_id']}";
	    $result = mysql_query($sql);
	    $row = mysql_fetch_row($result);
	    $edit_id = $row[0];
		$name_text = $row[1];
		$comment_text = $row[2];
		$password_text = $row[3];
		$edit_flag =true;
	  }
    }		
	if ($_SESSION['login'] != 1) {
		$url = 'http://ity-y.sakura.ne.jp/senshu_doumori/login.php';
		header('Location: ' . $url, true , 301);
	}


 

 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>入力フォーム</title>
	<link rel="stylesheet" href="./bootstrap.css">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="./custom.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</head>
<body>

	<nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse" style="background-color: red">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <form action="./logout.php" method="post" accept-charset="utf-8">
      	<label>
			<p>ログアウト</p>
			<input type="submit" style="display:none;">
		</label>
		
<!--       	<a class="navbar-brand" href="#">Navbar</a>
      	<input type="submit" name="" value="ログアウト">
 -->      </form>
    </nav>
<div class="chat_block" style="
    margin-top: 80px;
">
  <div class="container bootstrap snippet">
      <div class="row">
          <!--=========================================================-->
          <!-- selected chat -->
        <div class="bg-white ">
              <div class="chat-box bg-white">
		          <div class="chat-message">
		                <ul class="chat">
		                    <div v-for="context in contexts">
							<?php 
						        $sql = "select * from chat";
						        $result = mysql_query($sql);
						        while ($content = mysql_fetch_row($result)) {
						        	// echo $content[0]." 名前 : ".$content[1]." ".$content[4]."<br>";
						        	// echo $content[2]."<br>";
						        	// if (!empty($content[5])) {
							        // 	echo "<img src='data:image/png;base64,".$content[5]."'>";
						        	// }else if(!empty($content[6])){
						        	// 	echo "<video controls><source type='video/mp4' src='data:video/mp4;base64,".$content[6]."'></video>";
						        	// }
						    ?>
								<?php
								if ($content[1]==$_SESSION['name']) {
									$houkou = 'right';
								}else{
									$houkou = 'left';
								}
								 ?>
		                      <li class="clearfix <?php echo $houkou; ?>" id="chat_<?php echo $content[0]; ?>">
		                        <div class="chat-body clearfix">
			                        <div class="{{messageClass}}">
			                          
			                        </div>
		                          <div class="header">
		                            <strong class="primary-font"><?php echo $content[1] ?></strong>
		                          </div>
		                          <p><?php echo $content[2] ?></p>
		                            <br>
		                            <?php 
		                            if (!empty($content[5])) {
						        			echo "<img src='data:image/png;base64,".$content[5]."'>";
							        	}else if(!empty($content[6])){
							        		echo "<video controls><source type='video/mp4' src='data:video/mp4;base64,".$content[6]."'></video>";
							        	}				
	        						?>
	        						<br>
	        						<div class="min_date">
	        							<?php echo $content[4]; ?>
	        						</div>
		                          </p>
		                        </div>
		                        	<?php if ($houkou=='right') {?>
    		                        <div class="options">
							            <form action="./board.php" method="post" accept-charset="utf-8" id="del_form_<?php echo $content[0] ?>">
							                <input type="hidden" name="del_number" value="<?php echo $content[0] ?>" placeholder="">
							                [<input type="button" name="" value="削除" onclick="kakunin('<?php echo $content[0] ?>')">]
							            </form>
 	                		            <form action="./board.php" method="post" accept-charset="utf-8" >
							                <input type="hidden" name="edit_id" value="<?php echo $content[0]; ?>">
							                [<input type="submit" name="" value="編集">]
							            </form>
 								</div>
									<?php } ?>
		                      </li>                 
		                        <?php
									}
		                         ?>
		                    </div>
		                </ul>
		            </div>
<!--                 <div class="input-group">
        		    <form action="./board.php" method="post" accept-charset="utf-8" enctype="multipart/form-data">
		                <input class="form-control border no-shadow no-rounded" name="comment" id="input_text">
		                <div id="input_file">	                	
			                <label for="file_photo">
								<img src="./file_icon.png" alt="">
								<input type="file" id="file_photo" name="file" style="display:none;">
							</label>
		                </div>
						<input type="password" name="password" id="input_pass" value="" placeholder="">
	                    <input type="submit" class="btn btn-primary" id="submit_btn" name="" value="送信">
				    </form>
              </div>            
 -->      </div>        
    </div>
  </div>
</div>




<div class="container">
	     <?php 
	     if (!empty($login) || $_SESSION["login"] == 1) {
	     	?>

		     <br>
		    入力フォーム<br>
		    <?php if (!$edit_flag): ?>
			<form action="./board.php" method="post" accept-charset="utf-8" enctype="multipart/form-data">
			  <div class="form-group">
			    <input type="text" name="comment" class="form-control" value="" placeholder="コメント">
			  </div>
<!-- 			  <div class="form-group">
			    <input type="password" name="password" class="form-control" value="" placeholder="パスワード（オプション）">
			  </div>
 -->			  <div class="input-group">
				    <label class="input-group-btn">
				        <span class="btn btn-primary">
				            Choose File<input type="file" name="file" style="display:none">
				        </span>
				    </label>
				    <input type="text" class="form-control" readonly="">
				</div>
				<button type="submit" class="btn btn-primary">送信</button>
			</form>
		    <?php elseif ($edit_flag): ?>
			<form action="./board.php" method="post" accept-charset="utf-8" enctype="multipart/form-data">
			  <div class="form-group">
			    <input type="text" name="comment" class="form-control" value="<?php echo $comment_text;  ?>" placeholder="コメント">
			  </div>
<!-- 			  <div class="form-group">
			    <input type="password" name="password" class="form-control" value="" placeholder="パスワード（オプション）">
			  </div>
 -->			  <input type="hidden" name="edit_num" value="<?php echo $edit_id;  ?>">
			  <div class="input-group">
				    <label class="input-group-btn">
				        <span class="btn btn-primary">
				            Choose File<input type="file" name="file" style="display:none">
				        </span>
				    </label>
				    <input type="text" class="form-control" readonly="">
				</div>
				<button type="submit" class="btn btn-primary">編集</button>
			</form>
		    



		    <?php endif ?>
		    <script>
		        function kakunin(id){
		            if(window.confirm('本当にいいんですね？')){
		                document.getElementById("del_form_"+id).submit();
		            }else{

		            }
		        }

		    </script>
		<?php     
			}
	      ?>
  </div>
</body>
<script>
	function edit(n){
//		alert('#chat'+n+" div p");
		text = $('#chat_'+n+" div p").text();
		$('#chat_'+n+" div p").append('<textarea name="" id="area_'+n+'"></textarea>');
		//$('#area_'+n).val(text);
		$('#input_text').val(text);
//		alert(text);
//		document.getElementById('chat_'+n).
	}
	$(document).on('change', ':file', function() {
	    var input = $(this),
	    numFiles = input.get(0).files ? input.get(0).files.length : 1,
	    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	    input.parent().parent().next(':text').val(label);
	});

</script>
<style>

</style>
</html>