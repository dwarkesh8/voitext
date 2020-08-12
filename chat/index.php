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
	<meta name="author" content="Dwarkesh Purohit">
	<meta name="keywords" content="online chat application, voice to text chat app, web based chat application, javascript chat application, speech recognition chat application, speech recognition api, login to voitext, voitext chat application, voice to text chat application">
	<meta name="description" content="Web based chat application developed with speech recognition javascript api, in which user can send messages by typing and also by speaking sentence.">
	<meta property="og:image" itemprop="image primaryImageOfPage" content="https://charotaritsolutions.com/assets/img/CIS-logo.png" />
	<link rel="shortcut icon" href="https://charotaritsolutions.com/assets/img/CIS-logo.png">
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
						<!-- <th>Chat</th> -->
						<th><button class="btn btn-info"><?php echo isset($_SESSION['login_username']) ? $_SESSION['login_username'] : '';?></button></th>
						<th><button class="btn btn-danger" id="logout">Logout</button></th>
					</tr>
					<tr>
						<td width="20%">
							<div id="contacts">
								<div class="loading" class="mx-auto"></div>
							</div>
						</td>
						<td width="30%" colspan="2">
							<div id="chat">
								<div class="loading" class="mx-auto"></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div id="input">
				<form class="form-inline">
					<input type="hidden" id="currentSelectedId" value="0">
					<input type="text" class="form-control" id="result" name="message" placeholder="Enter your message here...">
					&nbsp
					<button type="button" class="btn btn-outline-primary" onclick="startConversion();"><i class="fa fa-microphone"></i> </button>
					&nbsp
					<button type="button" class="btn btn-outline-primary" id="send">Send <i class="fa fa-paper-plane"></i> </button>
				</form>
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
		function setCookie(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+ d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}
		function getCookie(cname) {
			var name = cname + "=";
			var decodedCookie = decodeURIComponent(document.cookie);
			var ca = decodedCookie.split(';');
			for(var i = 0; i <ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
		setInterval(function(){ updateChat(); }, 5000);
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
		updateChat();
		function updateChat() {
			var sid = getCookie('currentSelectedId');
			if (sid.length == 0) {
				sid = 0;
			}
			$.post('../server/main.php', {cmd:'get_chats', sid:sid}, function(response){
				var obj = JSON.parse(response);
				if (obj.length == 0) {
					var html = '<div class="alert alert-secondary" role="alert"> Please select any contact </div>';
					$("div#chat").html(html);
				}
				else {
					if (obj['type'] == 'error' || obj['type'] == 'warning') {
						swal(obj['msg'],'',obj['type']).then((value)=>{window.location = '../'});
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
						var existing_html = $("div#chat").html();
						if(existing_html !== html) {
							
							$("div#chat").html(html);
							audio = new Audio('../assets/bell-ring.mp3');
							audio.play();
						}
					}
				}
			});
		}
		$("div#contacts").on("click", "button.btn-contact", function(){
			var sid = $(this).attr('sid');
			if (sid > 0) {
				setCookie('currentSelectedId', sid, 1);
				$.post('../server/main.php', {cmd:'get_chats', sid:sid}, function(response){
					var obj = JSON.parse(response);
					if (obj.length == 0) {
						var html = '<div class="alert alert-secondary" role="alert"> No messages found! </div>';
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
		$("button#send").on("click", function(){
			var rid = getCookie('currentSelectedId');
			var msg = $("input#result").val();
			if (msg.length > 0 && rid > 0) {
				$.post('../server/main.php', {cmd:'send_msg', rid:rid, msg:msg}, function(response){
					console.log(response);
					var obj = JSON.parse(response);
					if (obj['type'] == 'error' || obj['type'] == 'warning') {
						swal(obj['msg'],'',obj['type']);
					}
					else {
						$("input#result").val('');
						updateChat();
					}
				});
			}
			else {
				$("input#result").focus();
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
<script type="text/javascript">

	function startConversion() {

		if ('webkitSpeechRecognition' in window) {
			var speechRecognizer = new webkitSpeechRecognition();
			speechRecognizer.continuous = true;
			speechRecognizer.interimResults = true;
			speechRecognizer.lang = 'en-IN';
			speechRecognizer.start();

			var finalTranscripts = '';

			speechRecognizer.onresult = function(event) {
				var interimTranscripts = '';

				for(var i = event.resultIndex; i < event.results.length; i++) {
					var transcript = event.results[i][0].transcript;
					transcript.replace("\n", "<br>");
					if (event.results[i].isFinal) {
						finalTranscripts += transcript;
					}
					else {
						interimTranscripts += transcript;
					}
				}
				$("input#result").val(finalTranscripts);
			}
			speechRecognizer.onerror = function(event) {
				swal('No Internet!', 'Please check your internet connection!', 'warning');
			}
		}
		else {
			swal('Use Google Chrome','Your browser does not support speech API, kindly upgrate it or try another browser!','error');
		}
	}
</script>
</html>