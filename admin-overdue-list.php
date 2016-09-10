<?php
    mysql_connect("localhost","username","password") or
        die("check DB connection issue" . mysql_error());

    mysql_select_db("db_name");

    $result = mysql_query("SELECT email,asset_tag,serial,last_checkout,expected_checkin, first_name, last_name FROM assets, users WHERE assets.assigned_to=users.id AND assets.company_id='1' AND expected_checkin <= CURRENT_DATE");

	$i = 0;
	$message = 'Dear Admin,<br><br>
	This is the weekly inventory report of demo loans that are <b>overdue</b>.
	
	<br><br>
	<table rules="all" style="border-color: #666;" cellpadding="10">
		<tr style="background: #eee;">
			<td>No.</td>
			<td>Item</td>
			<td>Serial</td>
			<td>First Name</td>
			<td>Last Name</td>
			<td>Check Out Date</td>
			<td>Due Date</td>
		</tr>
	';
    while($row=mysql_fetch_array($result)){
		$i = $i +1;
       
        $message .= "<tr>";
	    $message .= "<td>".$i;
        $message .= "<td>".$row['asset_tag'];
        $message .= "<td>".$row['serial'];
        $message .= "<td>".$row['first_name'];
		$message .= "<td>".$row['last_name'];
		$message .= "<td>".$row['last_checkout'];
        $message .= "<td style='color:red;'>".$row['expected_checkin'];
        $message .= "</tr>";
    }
		$message .= "
		</table>
		<br><br>
		There is in total ".$i ." items that are overdue.  To check status of all items, please visit http://url.com.<br><br>
    
		Thank you,<br>
		Demo Inventory Team";
    
		$subject = 'Weekly Overdue Demo Loan Report - '.$i .' items overdue';
		$to = 'admin@url.com';
		$header = "From:admin@url.com \r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html\r\n";
		//$header .= "CC:admin@url.com\r\n";

		$confirmation = mail($to, $subject, $message, $header);

    
	
	if ($confirmation){
		echo "A total of ".$i." items that are overdue.  Email sent successfully";
	}else {
		echo "Issue sending e-mails. Please contact admin@url.com";
	}
	
?>