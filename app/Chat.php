<?php

namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\model\User;
use Ratchet\Http\HttpServer;


require_once __DIR__.'/../model/User.php';//no need to autoload just one classf for now


class Chat implements MessageComponentInterface {

    protected $clients; // an array which in all user connected object will be stored
    protected $clientPseudo=[];


    //constructor
    public function __construct(){

        $this->clients= []; 
        
    }

    public function onOpen(ConnectionInterface $connection) {

        //when Chat is instanced we store the informations about the connection in the array clients
       
        //trying to get the pseudo of user from ws url

        $str=$connection->httpRequest->getUri()->getQuery();
        $pattern='/=(.+)/';
        preg_match($pattern,$str,$matches);

        $user=new User($matches[1]); // create user, could be good later 

        //for now i set only user->getPseudo, set with user itself later ? 
        // create a client class ?

        array_push($this->clients,array('user'=>$user->getPseudo(),'connection'=>$connection)); // storing the connection binded with pseudo

        array_push($this->clientPseudo,$user->getPseudo()); // storing pseudo
        
        // we send list of pseudo from user who connects to all users

        $this->sendMessage('liste',$this->clientPseudo);
            
            
        // on terminal
        
        $this->chatStatus($user->getPseudo());
        
    }

    public function onMessage(ConnectionInterface $from, $content) {

        $numRecv=count($this->clients)-1; // number of other client connected whitout client sending

        // //a message in formated string 
        // // instance id, content of the message, number of other clients connected, and plural or not with ternaire expression


        // $format ="Client %d envoie le message suivant : %s to %d client%s \n";

        // $message=sprintf($format,$from->resourceId, $content, $numRecv, $numRecv == 1 ? '' : 's');

        if(count($this->clients)==1){ // if user is alone on the chat

                $user='le Chat ';
                $content="Vous ne pouvez envoyer de message car vous êtes seul sur le chat.\n";
           
            $this->sendMessage($user,$content);

        }
        
        // for every client stored in clients we send $message
        else{


            //need pseudo from

            foreach($this->clients as $client){

                if($client['connection']===$from){

                    $pseudo=$client['user'];
                }
            }

            $this->sendMessage($pseudo,$content);


        }
    }

    public function onClose(ConnectionInterface $connection) {

       foreach($this->clients as $client){

            if($client['connection']===$connection){

             
                $pseudo=$client['user'];
                $this->sendMessage('Le chat',"$pseudo vient de se déconnecter");
                $keyPseudo=array_search($pseudo,$this->clientPseudo);
                unset($this->clientPseudo[$keyPseudo]);//delete the pseudo of liste


                //unset client from list clients 
                $key=array_search($client,$this->clients);
                unset($this->clients[$key]);   
                //on terminal
                $this->chatStatus($client['user'],FALSE);
            }
       }

       $this->sendMessage('liste',$this->clientPseudo);//refresh the list of users connected
       
    
        
       
    }

    public function onError(ConnectionInterface $connection, \Exception $exception) {

        echo 'Une erreur est survenue => '.$exception->getMessage().'\n';

    }

    //function use to send message to all clients of $clients
    //with a step to encode in json format 
    private function sendMessage(string $user,mixed $content){

        $message=array(

            'user'=>$user, // user liste is a condition read in ChatFrame ; 
            'content'=>$content
        );

        //sending message to all users connected ; saying new client is connected

        foreach($this->clients as $client){

            $client['connection']->send(json_encode($message));

        }
    }

    //give som info on the chat on terminal

    private function chatStatus(string $pseudo, bool $flag=TRUE):void 
    {
        if($flag){
            echo $pseudo." vient de se connecter.\n";
        }
        else{
            echo $pseudo ." vient de se déconnecter.\n";
        }
        
        echo 'Il y a actuellement '.count($this->clients)." utilisateur sur le chat.\n";
    }
}