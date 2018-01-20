<html>
<head>
	<title>Music Babble</title>
	<!--
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	-->
	<link rel="stylesheet" href="babble.css">

</head>
<body>
 	<div id="header">
		<center>
			<h1 id="title">Music Babble</h1>
			<div id="header-content">
				<?php
					
				?>
			</div>
		</center>
	</div>

	<div id="message">
		<?php
			include("functions.php");

			if (!isset($_GET["id"]))
			{
				echo "No message can be viewed here!";
				goto error;
			}

			$db = new SQLite3('babble.db', SQLITE3_OPEN_READWRITE);

			$result = get_message_result($db, $_GET["id"]);

			print_message_from_result($result);

			print_thread($db, $result["board"], $_GET["id"]);

			$db->close();
error:
		?>
	</div>

	<div id="new-thread">
		<h3>Respond:</h3>
		<form action="submit_message.php" method="post">
			<label>Name: <input type="text" id="name" name="name"></label><br>
			<label>Email: <input type="text" id="email" name="email"></label><br>
			<label>Title: <input type="text" id="title" name="title"></label><br>
			<label>Message: <textarea id="message" name="message"></textarea></label><br>
			<input type="hidden" id="parent" name="parent" value="<?php echo $_GET["id"] ?>">
			<input type="hidden" id="board" name="board" value="<?php echo $result["board"] ?>">
			<input type="submit" value="Submit">
		</form>
	</div>
	
</body>
</html>
