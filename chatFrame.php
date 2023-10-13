<?php

use App\model\User;

require_once 'model/User.php';


if(isset($_POST['pseudo'])){

    $user=new User($_POST['pseudo']);
    $pseudo = $user->getPseudo();
     
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='app.css'>
    <title>Be Nasty</title>
</head>
<body>
    <div class='welcome'>Welcome <?=$pseudo?></div>
    <div id='status'></div>
    <div class='chat-container'>
        
        <div id='frame'></div>
        <div id='member-connected'>
            <h3>membres connectés </h3>
            <ul id='liste'></ul>
        </div>
    </div>
    <input type='text' id='content'>
    <input type='submit' value='Yell' id='submit'>
    <button type='button' id='deco'>Rage Quit</button>
    
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>


var connection = new WebSocket('ws://localhost:8080?pseudo=<?=$pseudo?>');
 
connection.onopen=function(event){

    $('#status').append('you\'re connected ... go ahead !!!');
  
   
 }

connection.onmessage=function(event){

    var data = JSON.parse(event.data)
    var user = data.user;
    var content = data.content;

    if(user === 'liste'){

        $('#liste').html('');
        $.each(content,function(){

            $('#liste').append('<li>'+this+'</li>')
        });
    }
    else{
        $('#frame').append("<p>"+user+" dit : "+content+"</p>");
    }

    //clear input field 

    $("#content").val('');
    
    
}
//sending message on enter

$('input').on('keypress',function(event){

    if(event.key==='Enter'){

        if($('#content').val()!==''){

        connection.send($('#content').val());

        }
    }
    
})

$('#submit').on('click',function(){

    if($('#content').val()!==''){

    connection.send($('#content').val());
    }
});

$('#deco').on('click',function(){

    connection.close();

})
connection.onclose=function(){

    //status become disconnect
    $("#status").html('');
    $("#status").html('You\'re no longer connected :(');
    $('#liste').html('');
}

</script>
</html>