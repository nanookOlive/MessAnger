<?php
session_start();


if(isset($_SESSION['user'])){


    $nbUser=count($_SESSION['user']);
}
else{

    $nbUser=0;
}
var_dump(session_id());
var_dump($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MessAnger</title>
</head>
<body>

    <div id='nb-user-on-chat'>Il y  a  <?= $nbUser?> utilisateurs  sur le chat</di>
    <form method='POST' action='chatFrame.php'>
        <input type='text' name='pseudo' required>
        <input type='submit' value='rentrer' id='boubou'>
    </form>
    
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>


</html>