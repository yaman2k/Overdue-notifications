<?php

try {
	$host = 'localhost';
	$dbname = 'databasename';
	$username = 'username';
	$password = 'password';
	$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
	$db = new PDO($dsn, $username, $password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die('Database connection error.');
}

try {
	$result = $db->query("SELECT email, asset_tag, serial, last_checkout, expected_checkin, first_name, last_name, employee_num FROM assets, users WHERE assets.assigned_to = users.id AND expected_checkin <= CURRENT_DATE");
} catch (PDOException $e) {
	die('Query failed.');
}

$message = 'Dear Admins,<br><br>
This is the daily inventory report of loans that are <b>overdue</b>.

<br><br>
<table rules="all" style="border-color: #666;" cellpadding="10">
	<tr style="background: #eee;">
		<td>Item</td>
		<td>First Name</td>
		<td>Last Name</td>
  		<td>Phone Number</td>
		<td>Check Out Date</td>
		<td>Due Date</td>
	</tr>
';

try {
	$rows = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	die('Exception: Failed to fetch rows from the result.');
}

if ($rows === false) {
	die('Failed to fetch rows from the result.');
}

foreach ($rows as $row) {
    $message .= "<tr>";
  	$message .= "<td>" . $row['asset_tag'];
    $message .= "<td>" . $row['first_name'];
  	$message .= "<td>" . $row['last_name'];
	$message .= "<td>" . $row['employee_num'];
    $message .= "<td>" . $row['last_checkout'];
    $message .= "<td style='color:red;'>" . $row['expected_checkin'];
    $message .= "</tr>";
}

$row_count = count($rows);

$message .= "
</table>
<br><br>
There is in total " . $row_count ." items that are overdue.  To check status of all items, please visit https://hostname.domain.tld.<br><br>

Thank you,<br>
SR-VALE-PCDB";

$subject = 'Daily Overdue Loan Report - '. $row_count .' items overdue';
$to = 'destination@domain.tld';
$header = "From:snipeit@domain.tld \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";
$header .= "Reply-To:destination@domain.tld \r\n";
//$header .= "CC:admin@url.com\r\n";

$confirmation = mail($to, $subject, $message, $header);
if ($confirmation) {
	echo "A total of ". $row_count . " items that are overdue.  Email sent successfully";
} else {
	echo "Issue sending e-mails. Please contact admin";
}
