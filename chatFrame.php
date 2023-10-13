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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Pacifico&display=swap" rel="stylesheet">
    <title>Be Nasty</title>
</head>
<body class='chat-body'>

    <div class='welcome'>Welcome <?=$pseudo?> ! <div id='status'></div></div>
    <div class='chat-mamaFlex'>
        <div class='chat-container-left'>

            <img src='catcher_left.jpg' class='picture-catcher'>
            
            <div id='member-connected'>
                <h3>Nasty bears connected</h3>
                <ul id='liste'></ul>
            </div>

        </div>    

        <div class='chat-container-right'>
            <div id='frame'></div>
            <div class='button-input'>
                <input type='text' id='content'>
                <input type='submit' value='Yell' id='submit'>
                <button type='button' id='deco'>Rage Quit</button>
            </div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>


var connection = new WebSocket('ws://localhost:8080?pseudo=<?=$pseudo?>');
 
connection.onopen=function(event){

    $('#status').append(' You\'re connected ... go ahead !!!');
  
   
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
        $('#frame').append("<p class='message-user'>"+user+" dit : "+content+"</p>");
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