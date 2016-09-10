<?php
    //connect to MySQL 
    mysql_connect("localhost","username","password") or
        die("check DB connection issue" . mysql_error());

    //select database
    mysql_select_db("snipe_db_name");

    //fetch user and asset details from database with due date less or equal today
    //I have multiple companies, so you can customize that
    $result = mysql_query("SELECT email,asset_tag,serial,last_checkout,expected_checkin, first_name, last_name FROM assets, users WHERE assets.assigned_to=users.id AND assets.company_id='1' AND expected_checkin <= CURRENT_DATE");

    //counter for number of assets overdue
	$i = 0;
       
    //loop through assets and send an e-mail notification to the user with asset details.
    while($row=mysql_fetch_array($result)){
        $to = $row['email'];
		$i = $i +1;
        $message = "Dear ".$row['first_name'].',<br><br>
        This is an automated notification that the following demo item under your name is <b>overdue</b>:
        
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
        $message .= "   <td style='color:red;'>".$row['expected_checkin'];
        $message .= "</tr>";
    
		$message .= "
		</table>
		<br><br>
		Please ensure that you return this item as soon as possible.  To check status of all items, please visit http://SNIPEITURL.COM.<br><br>
    
		Thank you,<br>
		Inventory Demo Team";
    
		$subject = 'Overdue Demo Item: '.$row['asset_tag'];
		$header = "From:snipe-admin@snipeitapp.com\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html\r\n";
		$header .= "CC:snipe-admin@snipeitapp.com\r\n";

		$confirmation = mail($to, $subject, $message, $header);

    }
	
	if ($confirmation){
		echo "A total of ".$i." emails sent successfully";
	}else {
		echo "Issue sending e-mails. Please contact snipe-admin@snipeitapp.com";
	}
	
?>