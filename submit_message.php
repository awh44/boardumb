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
	<?php
		error_reporting(E_ALL); ini_set("display_errors", "1");

		function post_is_valid()
		{
			return
				isset($_POST["board"]) &&
				isset($_POST["name"]) &&
				isset($_POST["title"]) &&
				isset($_POST["email"]) &&
				isset($_POST["message"]) &&
				isset($_POST["parent"]);
		}

		function get_query_up_to_parent()
		{
			return "INSERT INTO Messages(board, title, body, name, email, date";
		}

		function get_query_to_parent_value()
		{
			return ") VALUES(:board, :title, :body, :name, :email, :date";
		}

		function get_query_to_end()
		{
			return ")";
		}

		function get_query_no_parent()
		{
			return get_query_up_to_parent() . get_query_to_parent_value() . get_query_to_end();
		}

		function get_statement_no_parent($db, $query, $title, $message, $name, $email)
		{
			$statement = $db->prepare($query);
			$statement->bindValue(':board', $_POST["board"]);
			$statement->bindValue(':title', $title);
			$statement->bindValue(':body', $message);
			$statement->bindValue(':name', $name);
			$statement->bindValue(':email', $email);
			$statement->bindValue(':date', date('Y-m-d H:i:s'));
			return $statement;
		}

		$db = new SQLite3("babble.db", SQLITE3_OPEN_READWRITE);

		if (!post_is_valid())
		{
			echo "There was an error in forming the message posting. Sorry!";
			goto error;
		}

		$name = trim(filter_var($_POST["name"], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW));
		if ($name === "")
		{
			echo "You must include a name in a post.";
			goto error;
		}

		$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
		$title = trim(filter_var($_POST["title"], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW));
		if ($title === "")
		{
			echo "You must include a title in a post.";
			goto error;
		}

		$message = filter_var($_POST["message"], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW);

		if ($_POST["parent"] !== "")
		{
			$query = get_query_up_to_parent() . ', parent' . get_query_to_parent_value() . ', :parent' . get_query_to_end();
			$statement = get_statement_no_parent($db, $query, $title, $message, $name, $email);
			$statement->bindValue(':parent', $_POST["parent"]);
		}
		else
		{
			$query = get_query_no_parent();
			$statement = get_statement_no_parent($db, $query, $title, $message, $name, $email);
		}

		if ($statement->execute() === FALSE)
		{
			echo "There was an error in submitting the post. Sorry!";
error:
			echo "<br>";
		}
		$db->close();
	?>
	<a href="view_board.php?board=<?php echo $_POST["board"]?>">Back to Music Babble</a>
</body>
</html>
