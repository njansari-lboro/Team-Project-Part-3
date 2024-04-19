<?php
header("Content-Type:application/json");
if (isset($_SESSION['user']) == false){
    	echo(json_encode("Not logged in"));
}
//get a list of messages given a chat
else if(isset($_GET['chat_id'])){

}
//put a message into a chat
else if(isset($_POST['chat_id'])&&isset($_POST['message'])){
    
}
//get a list of chats given a user_id
else if (isset($_SESSION)&&$_SESSION!="") {

} 
//else then return Invalid request
else{
    //return invalid request
	}


?>