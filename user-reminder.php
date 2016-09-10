<?php

    //connect to MySQL
    mysql_connect("localhost","username","password") or
        die("check DB connection issue" . mysql_error());

    // select DB
    mysql_select_db("db_name");

    $result = mysql_query("SELECT email,asset_tag,serial,last_checkout,expected_checkin, first_name, last_name FROM assets, users WHERE assets.assigned_to=users.id AND assets.company_id='1' AND expected_checkin BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 4 DAY)");

	$i = 0;
        
    while($row=mysql_fetch_array($result)){
        $to = $row['email'];
		$i = $i +1;
        $message = "Dear ".$row['first_name'].',<br><br>
        This is an automated notification that the following collaboration demo item under your name is due <b>soon</b>:
        
		<br><br>
        <table rules="all" style="border-color: #666;" cellpadding="10">
            <tr style="background: #eee;">
                <td>Item</td>
                <td>Serial</td>
				<td>Check Out Date</td>
				<td>Due Date</td>
            </tr>
        ';
        $message .= "<tr>";
        $message .= "   <td>".$row['asset_tag'];
        $message .= "   <td>".$row['serial'];
		$message .= "   <td>".$row['last_checkout'];
        $message .= "   <td>".$row['expected_checkin'];
        $message .= "</tr>";
    
		$message .= "
		</table>
		<br><br>
		Please ensure that you return this item as soon as possible.  To check status of all items, please visit http://url.com.<br><br>
    
		Thank you,<br>
		Inventory Demo Team";
    
		$subject = 'Reminder: Upcoming Due Date for Demo Item';
		$header = "From:admin@url.com \r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html\r\n";
		$header .= "CC:admin@url.com\r\n";

		$confirmation = mail($to, $subject, $message, $header);

    }
	
	if ($confirmation){
		echo "A total of ".$i." emails sent successfully";
	}else {
		echo "Issue sending e-mails. Please contact admin@url.com";
	}
	
?>