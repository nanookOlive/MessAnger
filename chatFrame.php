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
    <title>Document</title>
</head>
<body>
    <h1>Bonjour <?=$pseudo?></h1>
    <div id='status'></div>
    <div id='member-connected'>
        <h3>membres connectés </h3>
        <ul id='liste'></ul>
    </div>
    <div id='frame'></div>
    <input type='text' id='content' required>
    <input type='submit' value='envoyer' id='submit'>
    
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>


var connection = new WebSocket('ws://localhost:8080?pseudo=<?=$pseudo?>');
 
connection.onopen=function(event){

    $('#status').append('vous êtes connecté');
  
   
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


</script>
</html>