<?php
require_once('utilities.php');
// -------------------------------------------------1  connect to DBMS----------------------------------------------------------
require_once('../db/mysql_credentials.php');
$connect = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
if ($connect->connect_error) {
	die("Connection failed: " . $connect->connect_error);
}





// -------------------------------------------------2  Build query----------------------------------------------------------

function search($db_connection)
{
	if (isset($_POST["query"])) {
		$search = sanitize($db_connection, $_POST["query"]);
		$stmt = $db_connection->prepare("SELECT * FROM donation 
	WHERE nickname LIKE ? LIMIT 10");
		$stmt->bind_param('s', $search);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;

		/*$query = "
	SELECT * FROM donation 
	WHERE nickname LIKE '%" . $search . "%' ";
	echo $query;*/
	} else {
		$query = "SELECT * FROM donation ORDER BY timestamp DESC LIMIT 10 ";
		$result = mysqli_query($db_connection, $query);
		return $result;
	}
}

// -------------------------------------------------3  execute query----------------------------------------------------------
// $result = mysqli_query($connect, $query);


// Search on database
$result = search($connect);

// -------------------------------------------------4 format result----------------------------------------------------------

if ($result) {
	$output = "";
	$output .= '<div class="table-responsive">
					<table class="table table bordered">
						<tr>
							<th>Nickname</th>
							<th>Amount</th>
							<th>Message</th>
						</tr>';
	while ($row = mysqli_fetch_array($result)) {
		$output .= '
			<tr>
				<td>' . $row["nickname"] . '</td>
				<td>' . $row["amount"] . '</td>
				<td>' . $row["message"] . '</td>
			</tr>
		';
	}
	echo $output;
} else {
	echo 'Data Not Found';
}
