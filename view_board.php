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
		<?php
			include("functions.php");
			
			$db = new SQLite3("babble.db", SQLITE3_OPEN_READWRITE);
			
			$statement = $db->prepare("SELECT name, header FROM Boards where id = :id");
			$statement->bindValue(":id", $_GET["board"]);
			$results = $statement->execute();

			if (($results === FALSE) || !($result = $results->fetchArray(SQLITE3_ASSOC)))
			{
				echo "Sorry, could not find your board!";
			}
			else
			{
				echo "
					<center>
						<h1>" . $result["name"] . "</h1>
						<div id='header-content'>" .
							$result["header"] . "
						</div>
					</center>";
			}
		?>
	</div>

	<div id="threads">
		<?php

			if (!isset($_GET["board"]))
			{
				echo "Could not find that board!";
				goto error;
			}

			$db = new SQLite3('babble.db', SQLITE3_OPEN_READWRITE);
			
			print_thread($db, $_GET["board"], NULL);

			$db->close();
error:
		?>
	</div>

	<div id="new-thread">
		<h3>Submit a new thread:</h3>
		<form action="submit_message.php" method="post">
			<label>Name: <input type="text" id="name" name="name"></label><br>
			<label>Email: <input type="text" id="email" name="email"></label><br>
			<label>Title: <input type="text" id="title" name="title"></label><br>
			<label>Message: <textarea id="message" name="message" rows="25" cols="150"></textarea></label><br>
			<input type="hidden" id="parent" name="parent" value="">
			<input type="hidden" id="board" name="board" value="<?php echo $_GET["board"] ?>">
			<input type="submit" value="Submit">
		</form>
	</div>
	
</body>
</html>
