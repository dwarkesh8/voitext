<?php 
session_start();
if (!isset($_SESSION['login_id'])) {
	header('location:../');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Welcome | Voitext Chat</title>
	<link rel="stylesheet" type="text/css" href="../assets/bootstrap-4.4.1-dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../assets/fontawesome-free-5.13.0-web/css/all.min.css">
	<style type="text/css">
		div#chat {
			height: 300px;
			max-height: 500px
			width: auto;
			border: 1px solid black;
			overflow-y: auto;
			box-shadow: 2px 2px 2px 2px grey;
		}
		div#contacts {
			height: 300px;
			max-height: 500px
			width: auto;
			border: 1px solid black;
			overflow-y: auto;
			padding: 20px;
			box-shadow: 2px 2px 2px 2px grey;
		}
		th {
			font-size: 20px;
			background-color: grey;
			color: white;
		}
		span.bold {
			font-weight: bold;
		}
		.loading {
			border: 6px solid #f3f3f3;
			border-top: 6px solid #3498db;
			border-radius: 50%;
			width: 50px;
			height: 50px;
			animation: spin 2s linear infinite;
		}
		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
		button {
			font-weight: bold !important;
		}
	</style>
</head>
<body>
	<div class="row">
		<div class="container mt-5">
			<div class="table table-responsive">
				<table class="table">
					<tr>
						<th>Contacts</th>
						<th>Chat</th>
						<th><button class="btn btn-info"><?php echo isset($_SESSION['login_username']) ? $_SESSION['login_username'] : '';?></button></th>
						<th><button class="btn btn-danger" id="logout">Logout</button></th>
					</tr>
					<tr>
						<td width="20%">
							<div id="contacts">
								<div class="loading" class="mx-auto"></div>
							</div>
						</td>
						<td width="50%" colspan="3">
							<div id="chat">
								<div class="loading" class="mx-auto"></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="">
			</div>
		</div>
	</div>

	<script type="text/javascript" src="../assets/bootstrap-4.4.1-dist/js/jquery.min.js"></script>
	<script type="text/javascript" src="../assets/bootstrap-4.4.1-dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../assets/bootstrap-4.4.1-dist/js/sweetalert.min.js"></script>
	<script type="text/javascript" src="../assets/fontawesome-free-5.13.0-web/js/all.min.js"></script>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		$.post('../server/main.php', {cmd:'get_contacts'}, function(response){
			var obj = JSON.parse(response);
			if (obj['type'] == 'error' || obj['type'] == 'warning') {
				swal(obj['msg'],'',obj['type']);
			}
			else {
				var html = '';
				$.each(obj, function(key,value){
					html += '<p><button class="btn btn-primary btn-contact" sid="'+value.id+'">'+value.username+' <span class="badge badge-light">0</span></button></p>';
				});
				$("div#contacts").html(html); 
			}
		});
		$.post('../server/main.php', {cmd:'get_chats'}, function(response){
			var obj = JSON.parse(response);
			if (obj.length == 0) {
				var html = '<div class="alert alert-secondary" role="alert"> Please select any contact </div>';
				$("div#chat").html(html);
			}
			else {
				if (obj['type'] == 'error' || obj['type'] == 'warning') {
					swal(obj['msg'],'',obj['type']);
				}
				else {
					var html = '';
					$.each(obj, function(key,value){
						var username = "<?php echo $_SESSION['login_username'];?>";
						if (value.username == username) {
							username = 'You';
						}
						else {
							username = value.username;
						}
						html += '<div class="alert alert-info" role="alert"><span class="bold">'+username+':</span> '+value.message+'</div>';
					});
					$("div#chat").html(html); 
				}
			}
		});
		$("div#contacts").on("click", "button.btn-contact", function(){
			var sid = $(this).attr('sid');
			if (sid > 0) {
				$.post('../server/main.php', {cmd:'get_chats', sid:sid}, function(response){
					var obj = JSON.parse(response);
					if (obj.length == 0) {
						var html = '<div class="alert alert-secondary" role="alert"> Please select any contact </div>';
						$("div#chat").html(html);
					}
					else {
						if (obj['type'] == 'error' || obj['type'] == 'warning') {
							swal(obj['msg'],'',obj['type']);
						}
						else {
							var html = '';
							$.each(obj, function(key,value){
								var username = "<?php echo $_SESSION['login_username'];?>";
								if (value.username == username) {
									username = 'You';
								}
								else {
									username = value.username;
								}
								html += '<div class="alert alert-info" role="alert"><span class="bold">'+username+':</span> '+value.message+'</div>';
							});
							$("div#chat").html(html); 
						}
					}
				});
			}
			else {
				swal('Refresh', 'Contact id is missing, please reload the page!', 'warning');
			}
		});
		$("button#logout").on("click", function(){
			$.post('../server/main.php', {cmd:'logout'}, function(response){
				var obj = JSON.parse(response);
				if (obj['type'] == 'error' || obj['type'] == 'warning') {
					swal(obj['msg'],'',obj['type']);
				}
				else {
					swal(obj['msg'],'',obj['type']).then((value)=>{
						window.location = '../';
					}); 
				}
			});
		});
	});
</script>
</html>