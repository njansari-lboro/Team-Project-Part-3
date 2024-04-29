		//fetch("http://35.189.103.73/api/chats")
		//	.then(function(response){ return response.json()})
		//	.then(function(data) { 
        //        console.log(data);
        //    });



chatsJSON='[{"id":"1","name":"Test Chat","is_private":"0","icon_name":null,"last_updated":"2024-04-25 11:34:56"},'+
            '{"id":"2","name":"Bob","is_private":"1","icon_name":null,"last_updated":"2024-04-25 15:01:45"},'+
            '{"id":"3","name":"Mary","is_private":"1","icon_name":null,"last_updated":"2024-04-26 9:54:02"}]';

let chatsJS=JSON.parse(chatsJSON);


usersJSON='[{"user_id":"1","chat_id":"1"},'+
            '{"user_id":"2","chat_id":"1"},'+
            '{"user_id":"3","chat_id":"1"},'+
            '{"user_id":"1","chat_id":"2"},'+
            '{"user_id":"2","chat_id":"2"},'+
            '{"user_id":"1","chat_id":"3"},'+
            '{"user_id":"3","chat_id":"3"}]';



messagesJSON='[{"id":"1","chat_id":"1","author_id":"","body":"","date_posted":"2024-04-26 9:54:02"}'+
                '{}';


//console.log(document.querySelector("#mlist").innerHTML);

//let text="<li class='message-title' style='font-size:18px;'>Recent</li><br>";
let text='<div>'
for(i=0;i<10;i++){

//text+=                            '<li>'+
 //                               '<a href="#">'+
  //                           '<span class="message-info">'+
   //                              '<span class="message-name">Angela</span>'+
     //                            '<span class="message-text">Thank you, I recieved your email!</span>'+
       //                      '</span>'+
         //                    '<span>'+
           //                      '<span class="message-unread">1</span>'+
            //                     '<span class="message-time">11:40</span>'+
             //                '</span>'+
              //                  '</a>'+
                //             '</li>';

                




}
text+='</div>';
document.querySelector("#mlist").innerHTML=text;
//console.log(document.querySelector("#mlist").innerHTML);