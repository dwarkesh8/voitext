<?php 
session_start();
if (isset($_SESSION['login_id'])) {
	header('location:chat/');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login | Voitext Chat</title>
	<link rel="stylesheet" type="text/css" href="assets/bootstrap-4.4.1-dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/fontawesome-free-5.13.0-web/css/all.min.css">

	<style type="text/css">
		body {
			font-family: rockwell;
		}
		div#login {
			width: auto;
			border: 2px solid black;
			box-shadow: 2px 3px 4px 9px grey;
			padding: 25px;
			box-sizing: border-box;
		}
		label {
			font-weight: bold;
			color: black;
		}
		input[type=text] {
			font-weight: bold;
			color: #543;
		}
		button {
			font-weight: bold !important;
		}

	</style>
</head>
<body>
	
	<div class="row">
		<div class="container col-md-12 mt-5" align="center">
			<h3>Voitext Login <i class="fa fa-lock"></i></h3>
		</div>
		<div class="container col-md-5" id="login">
			<div class="form-group">
				<label>Username</label>
				<input type="text" name="username" class="form-control" placeholder="Enter your username" autocomplete="off" required>
			</div>
			<div class="form-group">
				<label>Password</label>
				<input type="password" name="password" class="form-control" placeholder="Enter your password" autocomplete="off" required>
			</div>
			<div class="form-group">
				<button class="btn btn-outline-dark">Login</button>
			</div>
		</div>
	</div>



	<script type="text/javascript" src="assets/bootstrap-4.4.1-dist/js/jquery.min.js"></script>
	<script type="text/javascript" src="assets/bootstrap-4.4.1-dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/bootstrap-4.4.1-dist/js/sweetalert.min.js"></script>
	<script type="text/javascript" src="assets/fontawesome-free-5.13.0-web/js/all.min.js"></script>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		$("button").on("click", function(){
			var username = $("input[name=username]").val();
			if (username.length == 0) {
				$("input[name=username]").focus();
				return false;
			}
			var password = $("input[name=password]").val();
			if (password.length == 0) {
				$("input[name=password]").focus();
				return false;
			}
			if (username.length > 0 && password.length > 0) {
				$.post('server/main.php', {cmd:'login', username: username, password: password}, function(response){
			console.log(response);
					var obj = JSON.parse(response);
					if (obj['type'] == 'error' || obj['type'] == 'warning') {
						swal(obj['msg'],'',obj['type']);
					}
					else {
						swal('Success', 'Welcome to Voitext Chat', 'success').then((value)=>{
							window.location = 'chat/';
						});
					}
				});
			}
			else {
				swal('Enter value', 'Please fill the login credentials carefully!', 'error');
			}
		});
	});
</script>
</html>