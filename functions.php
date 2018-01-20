<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

function construct_display_title($result, $include_link)
{
	$display = "";

	if ($include_link)
	{
		$display .= '<a href="view_message.php?id=' . $result["id"] . '">';
	}

	$display .= $result["title"];

	if ($include_link)
	{
		$display .= "</a>";
	}

	$display .= ' - ';


	if ($result["email"] != '')
	{
		$display .= '<a href="mailto:' . $result["email"] . '">';
	}
	$display .= $result["name"];
	if ($result["email"] != '')
	{
		$display .= '</a>';
	}
	$display .= ' - ' . $result["date"];

	return $display;
}

function print_thread($db, $board, $parent)
{
	$query = 'SELECT id, title, name, email, date FROM Messages WHERE board = :board AND parent';
	if ($parent === NULL)
	{
		$query .= ' IS NULL';
		$statement = $db->prepare($query);
	}
	else
	{
		$query .= ' = :parent';
		$statement = $db->prepare($query);
		$statement->bindValue(':parent', $parent);
	}
	$statement->bindValue(':board', $board);
	
	echo '
		<ul>';
	$results = $statement->execute();
	while (($result = $results->fetchArray(SQLITE3_ASSOC)))
	{
		echo '
			<li>' .
				construct_display_title($result, TRUE) . '
			</li>';	
		print_thread($db, $board, $result["id"]);
		if ($parent === NULL)
		{
			echo '<br>';
		}
	}
	$results->finalize();

	echo '</ul>';
}

function get_message_result($db, $id)
{
	$statement = $db->prepare('SELECT board, title, name, email, date, body FROM Messages WHERE id = :id');
	$statement->bindValue(':id', $id);


	$results = $statement->execute();
	return $results->fetchArray(SQLITE3_ASSOC);
}

function print_message_from_result($result)
{
	echo '
		<div id="title">' .
			construct_display_title($result, FALSE) . '
		</div>';
	echo '
		<div id="message">' . 
			$result["body"] . '
		</div>';
}
?>
