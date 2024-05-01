		//fetch("http://35.189.103.73/api/chats")
		//	.then(function(response){ return response.json()})
		//	.then(function(data) { 
        //        console.log(data);
        //    });


// GET chats
chatsJSON='[{"id":"1","name":"Test Chat","is_private":"0","icon_name":null,"last_updated":"2024-04-25 11:34:56"},'+
            '{"id":"2","name":"private1","is_private":"1","icon_name":null,"last_updated":"2024-04-25 15:01:45"},'+
         '{"id":"3","name":"private2","is_private":"1","icon_name":null,"last_updated":"2024-04-26 9:54:02"}]';

//chatsJSON='[]';




chat1usersJSON='[{"user_id":"1"},{"user_id":"2"},{"user_id":"3"}]';

chat2usersJSON='[{"user_id":"1"},{"user_id":"2"}]';

chat3usersJSON='[{"user_id":"1"},{"user_id":"3"}]';


chat1messagesJSON='[{"id":"1","chat_id":"1","author_id":"1","body":"hi","date_posted":"2024-04-25 9:00:02"},'+
                '{"id":"2","chat_id":"1","author_id":"2","body":"hello","date_posted":"2024-04-25 9:54:02"},'+
                '{"id":"3","chat_id":"1","author_id":"3","body":"whats up","date_posted":"2024-04-25 11:34:56"}]';

chat2messagesJSON='[{"id":"1","chat_id":"2","author_id":"1","body":"how r u","date_posted":"2024-04-25 15:00:01"},'+
                '{"id":"2","chat_id":"2","author_id":"2","body":"very bad","date_posted":"2024-04-25 15:01:45"}]';

chat3messagesJSON='[{"id":"1","chat_id":"3","author_id":"1","body":"im bored","date_posted":"2024-04-25 9:50:02"},'+
                '{"id":"2","chat_id":"3","author_id":"3","body":"same","date_posted":"2024-04-26 9:54:02"}]';


let chatsJS=JSON.parse(chatsJSON);

let chat1usersJS=JSON.parse(chat1usersJSON);
let chat2usersJS=JSON.parse(chat2usersJSON);
let chat3usersJS=JSON.parse(chat3usersJSON);

let chat1messagesJS=JSON.parse(chat1messagesJSON);
let chat2messagesJS=JSON.parse(chat2messagesJSON);
let chat3messagesJS=JSON.parse(chat3messagesJSON);


function getMessages(chat_id){

    if(chat_id=="1"){
        return chat1messagesJS;            
    } else if(chat_id=="2"){
        return chat2messagesJS;    
    } else if(chat_id=="3"){
        return chat3messagesJS;    
    } 
    
    else {
        return "";
    }

}

function getUsers(chat_id){

    if(chat_id=="1"){
        return chat1usersJS;            
    } else if(chat_id=="2"){
        return chat2usersJS;    
    } else if(chat_id=="3"){
        return chat3usersJS;    
    } 
    
    else {
        return "";
    }

}





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


//showChat()
let topChat=document.getElementById('1')
if(topChat!=null){
    document.getElementById('1').click();
}


function showChat(){
    let chat="";
    if(chatsJS.length==0) {
        return;
    }
    else if(this.id==undefined){
         chat=document.querySelector(".chats");
    } else {
        chat=this;
    }
    document.querySelector("#bottom").style.display="block";
    let users=getUsers(chat.id);
    let userStr="";
    for(let i=0;i<users.length;i++){
        userStr+='user'+users[i].user_id+' ';
    }
    document.querySelector("#header").innerHTML=chat.textContent+'<br>'+userStr;
    let currentChat=getMessages(chat.id);
    document.querySelector("#container").innerHTML=displayMessages(currentChat);
}






function displayMessages(messages){
    let text="";
    for(let i=0;i<messages.length;i++){
        text+='<div class="message" id="message '+messages[i].id+'">user'+messages[i].author_id+'<br>'+messages[i].body+'<br>'+messages[i].date_posted+'</div>';
    }

    return text;

};


document.querySelector('#sendMessage').addEventListener("click",function(){
    let textBox=document.querySelector('#typeMessage')
    let message=textBox.value;
    console.log(message);
    textBox.value="";
    console.log(chatsJS);

});

document.querySelector('#typeMessage').addEventListener("keyup",function(){
    if(event.key=="Enter" && document.querySelector('#typeMessage').value!=""){
        document.querySelector('#sendMessage').click();
    }
});