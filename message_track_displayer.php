<?php
   
   function display_message($con, $message_id) {
	   $result = mysqli_query($con, "SELECT * from mails WHERE id = '$message_id'");
	   while($row = mysqli_fetch_array($result)) {
			echo "<tr><td>" . $row['from_email']. "</td><td>" . $row['to_email'] . "</td><td>" . $row['subject'] . "</td><td>". $row['body']. "</td><td>". $row['timestamp']. "</td>";
			return $row;
	   }
   }
   
   function display_message_track_for_inbox($con, $message_track) {
	   echo "<table>";
	   for($i=$message_track['child_count'] -1; $i>=0; $i--) {
		   $message_id = $message_track['child'][$i];
		   display_message($con, $message_id);
		   echo "</tr>";
	   }
	   display_message_withReplyForward($con, $message_track['id']);
	   for($i=0; $i<$message_track['prev_track_count']; $i++) {
		   $message_id = $message_track['prev'][$i];
		   display_message($con, $message_id);
		   echo "</tr>";
	   }
	   echo "</table>";
   }
   
   function display_message_track_for_outbox($con, $message_track) {
	   echo "<table>";
	   for($i=$message_track['child_count'] -1; $i>=0; $i--) {
		   $message_id = $message_track['child'][$i];
		   display_message($con, $message_id);
		   echo "</tr>";
	   }
	   display_message($con, $message_track['id']);
	   for($i=0; $i<$message_track['prev_track_count']; $i++) {
		   $message_id = $message_track['prev'][$i];
		   display_message($con, $message_id);
		   echo "</tr>";
	   }
	   echo "</table>";
   }
   
   function call($method, $url, $data = false) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_URL, $url);
	if ($data) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Content-Length: ' . strlen($data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	return curl_exec($ch);
}

   
   function display_message_withReplyForward($con, $message_id) {
	  $row = display_message($con, $message_id);
	  $reply = 'reply'.$message_id;
	  $forward = 'forward'.$message_id;
	  $submit_reply = 'submit_reply'.$message_id;
	  $submit_forward = 'submit_forward'.$message_id;
	  $reply_body = 'reply_body'.$message_id;
	  $forward_to = 'forward_to'.$message_id;
	  echo "
	  <form action='' method='post'>
	  <td> <input type='submit' name='$reply' value='Reply'></td>
	  <td> <input type='submit' name='$forward' value='Forward'</td></form></tr>";
	  if(isset($_POST[$reply])) {
		  echo "
		  <form action='' method='post'>
		  Message:<br><textarea rows='5' name='$reply_body' cols='30'></textarea><br>
		  <input type='submit' name='$submit_reply' value='Send'>
		  </form>
		  ";
	  } else {
		  if(isset($_POST[$forward])) {
		  echo "
		  <form action='' method='post'>
		  Forward To:<input type='text' name='$forward_to' />
		  <input type='submit' name='$submit_forward' value='Send'>
		  </form>
		  ";
	  }
      }
	  if(isset($_POST[$submit_forward])) {
			$object = array('forward_to'=>$_POST[$forward_to]);  
			$url = 'http://localhost/forward/'.$message_id.'/';
		    $response = call('POST', $url,json_encode($object));
			echo 'Successfully forwarded message';		 
	  }
	  if(isset($_POST[$submit_reply])) {
			  $object = array('reply_body'=>$_POST[$reply_body]);  
			  $url = 'http://localhost/reply/'.$message_id.'/';
		      $response = call('POST', $url,json_encode($object));
			  echo 'Successfully replied to message';
		  
	  }
   }
   
   function display_inbox_message_history($con, $login) {
		$logged_in_user = $_SESSION['logged_in_user']['login'];	   
	    $url = 'http://localhost/inbox_message_history/'.$logged_in_user.'/';
	    $response = call('GET', $url);
	    $message_history = json_decode($response,true);
		echo "<br>Sender Receiver Subject Body Time<br>";
	    for($i=0; $i<$message_history['count']; $i++) {
		    display_message_track_for_inbox($con, $message_history[$i]);
		    echo "<br><br><br><br><br>";
	    }
    }
   
   function display_outbox_message_history($con, $login) {
	   $logged_in_user = $_SESSION['logged_in_user']['login'];	   
	    $url = 'http://localhost/outbox_message_history/'.$logged_in_user.'/';
	    $response = call('GET', $url);
	    $message_history = json_decode($response,true);
		echo "<br>Sender Receiver Subject Body Time<br><br>";
	    for($i=0; $i<$message_history['count']; $i++) {
		   display_message_track_for_outbox($con, $message_history[$i]);
		   echo "<br><br><br><br><br>";
	   }
   }
?>
