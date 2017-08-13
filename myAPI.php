<?php
require_once 'api_class.php';
class MyAPI extends API
{
    protected $User;

    public function __construct($request, $origin) {
        parent::__construct($request);
		
    }

    /**
     * Example of an Endpoint
     */
	 
	protected function reply($args) {
		$data = json_decode(file_get_contents('php://input'), true);
		$con = mysqli_connect('localhost', 'root', '', 'mailing_application');
		//echo "args[0] = ".$args[0];
		$mail_message_id = $args[0];
		$result = mysqli_query($con, "SELECT * from mails WHERE id = '$mail_message_id'");
		if (!$result) {
			printf("Error: %s\n", mysqli_error($con));
			exit();
		}
		
		while($row = mysqli_fetch_array($result)) {
			$receiver = $row['to_email'];
			$sender = $row['from_email'];
			$sender_subject = $row['subject'];
			$receiver_reply_body = $data['reply_body'];
			$query = "INSERT INTO mails (from_email, to_email, subject, body) VALUES ('$receiver', '$sender', '$sender_subject', '$receiver_reply_body')";
		    $res = mysqli_query($con, $query);
			$last_insert_id = $con->insert_id;
			mysqli_query($con, "INSERT INTO reply_forward_mails (parent_id, child_id, tag) VALUES ('$mail_message_id', '$last_insert_id', 'r')");
		}
		return 1;
	 }
	 
	 protected function forward($args) {
		$data = json_decode(file_get_contents('php://input'), true);
		$con = mysqli_connect('localhost', 'root', '', 'mailing_application');
		$mail_message_id = $args[0];
		$result = mysqli_query($con, "SELECT * from mails WHERE id = '$mail_message_id'");
		if (!$result) {
			printf("Error: %s\n", mysqli_error($con));
			exit();
		}
		
		$forwarded_to = $data['forward_to'];
		while($row = mysqli_fetch_array($result)) {
			$receiver = $row['to_email'];
			$forward_to = $data['forward_to'];
			$sender_subject = $row['subject'];
			$sender_body = $row['body'];
			$query = "INSERT INTO mails (from_email, to_email, subject, body) VALUES ('$receiver', '$forwarded_to', '$sender_subject', '$sender_body')";
			$res = mysqli_query($con, $query);
			$last_insert_id = $con->insert_id;
			mysqli_query($con, "INSERT INTO reply_forward_mails (parent_id, child_id, tag) VALUES ('$mail_message_id', '$last_insert_id', 'f')");	
		}
		
		return 1;
	 }
	
	/**
     * Property: get_message_history
     * Function to merge the connected Emails related to mail with id as message_id
     */
	
	function get_message_track($con, $message_id) {
	   $message_track['id'] = $message_id;
	   $message_track['prev_track_count'] = 0;
	   $message_track['child_count'] = 0;
	   $curr_node = $message_id;
	   $prev_node = $message_id;
	   while(1) {
		   $result = mysqli_query($con, "SELECT * from reply_forward_mails WHERE child_id = '$curr_node'");
		   $prev_node = $curr_node;
		   while($row = mysqli_fetch_array($result)) {
			   $curr_node = $row['parent_id'];
			   $message_track['prev'][$message_track['prev_track_count']] = $curr_node;
			   $message_track['prev_track_count']++;
		   }
		   if($prev_node === $curr_node) {
			   break;
		   }
	   }
	   
	   $result = mysqli_query($con, "SELECT * from reply_forward_mails WHERE parent_id = '$message_id' ORDER BY 'id' ASC");
	   while($row = mysqli_fetch_array($result)) {
		   $message_track['child'][$message_track['child_count']] = $row['child_id'];
		   $message_track['child_count']++;
	   }
	   return $message_track;
   }
   
    /**
     * Property: get_message_history
     * Function to get the mail history for a user and merge the connected Emails
     */
   
   
	function get_message_history($con, $result) {
		$result_count = 0;
	   while($row = mysqli_fetch_array($result)) {
		   $result_array[$result_count++] = $row;
	   }
	   for($i=0; $i<500; $i++) {
		   $visited[$i] = false;
	   }
	   $message_history['count'] = 0;
	   for($i=0; $i<$result_count; $i = $i + 1) {
		   $row = $result_array[$i];
		   $message_id = $row['id'];
		   if(!$visited[$message_id]) {
			   
		   $message_track = $this->get_message_track($con, $message_id);
		   for($j = 0; $j < $message_track['prev_track_count']; $j++) {
			   $index = $message_track['prev'][$j];
			   $visited[$index] = true;
		   }
		   for($k = 0; $k < $message_track['child_count']; $k++) {
			   $visited[$message_track['child'][$k]] = true;
		   }
		   $message_history[$message_history['count']] = $message_track;
		   $message_history['count']++;
	   }
	  }
	   return $message_history;
   }
   
   /**
     * Property: inbox_message_history
     * Extract the whole tree(which is required to show) of the received mail to the user
     */
   
    protected function inbox_message_history($args) {
		$data = json_decode(file_get_contents('php://input'), true);
		$con = mysqli_connect('localhost', 'root', '', 'mailing_application');
		$login = $args[0];

		$result = mysqli_query($con, "SELECT * from mails WHERE to_email = '$login' ORDER BY id DESC");
		$response = $this->get_message_history($con, $result);
		return $response;
   }
    /**
     * Property: outbox_message_history
     * Extract the whole tree(which is required to show) of the sent mail from the user
     */
    protected function outbox_message_history($args) {
		$data = json_decode(file_get_contents('php://input'), true);
		$con = mysqli_connect('localhost', 'root', '', 'mailing_application');
		$login = $args[0];

		$result = mysqli_query($con, "SELECT * from mails WHERE from_email = '$login' ORDER BY id DESC");
	    return ($this->get_message_history($con, $result));
   }
    
	
}
 
 ?>