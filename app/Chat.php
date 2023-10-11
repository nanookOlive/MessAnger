<?php

namespace App;



use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\model\User;
use Ratchet\Http\HttpServer;


require_once __DIR__.'/../model/User.php';

class Chat implements MessageComponentInterface {

    protected $clients; // a splObjectStorage which in all user connected object will be stored

    //constructor 


    public function __construct(){

        $this->clients= new \SplObjectStorage; 
        
    }

    public function onOpen(ConnectionInterface $connection) {

        //when Chat is instanced we store the informations about the connection in the SplObjetcStorage
        $this->clients->attach($connection);
      //trying to get the pseudo of user
        $str=$connection->httpRequest->getUri()->getQuery();

        $pattern='/=(.+)/';
        preg_match($pattern,$str,$matches);

        $user=new User($matches[1]);
        //for now i set only user->getPseudo, set with user itself later ? 

        $this->clients->setInfo($user->getPseudo());
        echo 'Le client '.$connection->resourceId." vient de se connecter \n";//TD show information about client and connection ?        
        echo 'Il y a actuellement '.count($this->clients)." utilisateur sur le chat \n";

        var_dump($this->clients->getInfo());
        
    }

    public function onMessage(ConnectionInterface $from, $content) {

        $numRecv=count($this->clients)-1; // number of other client connected whitout client sending

        // //a message in formated string 
        // // instance id, content of the message, number of other clients connected, and plural or not with ternaire expression


        // $format ="Client %d envoie le message suivant : %s to %d client%s \n";

        // $message=sprintf($format,$from->resourceId, $content, $numRecv, $numRecv == 1 ? '' : 's');

        if(count($this->clients)==1){ // if user is alone on the chat

            $message=array(

                'user'=>$from->resourceId,
                'content'=>"Vous ne pouvez envoyer de message car vous êtes seul sur le chat.\n"
            );

            $jsonMessage=json_encode($message);
            $from->send($jsonMessage); //could be a user statut ??? 

        }

        
        // for every client stored in clients we send $message
        else{

            foreach($this->clients as $client){

                //we don't want the client which sends the message

                //if($from !== $client){

                    $message=array(

                        'user'=>$this->clients->getInfo(),
                        'content'=>$content
                    );

                    $jsonMessage=json_encode($message);
                    $client->send($jsonMessage);
            // }
                }

        }
       
        



    }

    public function onClose(ConnectionInterface $connection) {

        $this->clients->detach($connection);
        
        echo 'le client '.$connection->resourceId.' vient de déconnecter';
    }

    public function onError(ConnectionInterface $connection, \Exception $exception) {

        echo 'Une erreur est survenue => '.$exception->getMessage().'\n';

    }
}