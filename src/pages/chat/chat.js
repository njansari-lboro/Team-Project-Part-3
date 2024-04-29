		//fetch("http://35.189.103.73/api/chats")
		//	.then(function(response){ return response.json()})
		//	.then(function(data) { 
        //        console.log(data);
        //    });



chatsJSON='[{"id":"1","name":"Test Chat","is_private":"0","icon_name":null,"last_updated":"2024-04-25 11:34:56"},'+
            '{"id":"2","name":"Bob","is_private":"1","icon_name":null,"last_updated":"2024-04-25 15:01:45"},'+
            '{"id":"3","name":"Mary","is_private":"1","icon_name":null,"last_updated":"2024-04-26 9:54:02"}]';

let chatsJS=JSON.parse(chatsJSON);
console.log(chatsJS);
console.log(chatsJS.length);

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
for(i=0;i<chatsJS.length;i++){

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

console.log(chatsJS[i].id);
text+='<div class="chats" id="'+chatsJS[i].id+'">';
text+=chatsJS[i].name+" "+chatsJS[i].last_updated;
text+='</div>';


}
text+='</div>';
document.querySelector("#mlist").innerHTML=text;
//console.log(document.querySelector("#mlist").innerHTML);




searchInput = document.querySelector(".search");
Container = document.getElementById("mlist");
//gets all the topics so they will all come back when search removed
originalMessages = Array.from(Container.querySelectorAll(".chats"));
searchInput.addEventListener("input", performSearch);
//performSearch();
function performSearch() {
    const searchValue = searchInput.value.toLowerCase();
//shows or hides topics based on the search value inputted by user
    originalMessages.forEach(message => {
	    const Name = message.textContent.toLowerCase();

	    if (Name.includes(searchValue) || searchValue === "") {
		    message.style.display = "block";
	    } else {
		    message.style.display = "none";
	    }
})};

originalMessages.forEach(message=> {
    message.addEventListener("click",showChat)
})

function showChat(){
    document.querySelector("#header").innerHTML=this.textContent;
}
