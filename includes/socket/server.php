<?php
  error_reporting( E_ALL );
  require_once( dirname( dirname( __FILE__ ) )."/conn/open.php" );
  require_once( dirname( __FILE__ )."/socket.php" );
  
  class Server implements SocketListener {
    protected $user_sockets = array();
    protected $client_ids   = array();

    public function onMessageRecieved( SocketServer $server, SocketClient $sender, $message ) {
      $request  = json_decode( $message );
      $clients  = $server->getClients();

      if ( !isset( $request ) || !isset( $request->event ) ) {
        return;
      }

      $user_id  = false;
      if ( isset( $this->client_ids[$sender->id] ) ) {
        $user_id  = $this->client_ids[$sender->id];
      }

      switch( $request->event ) {
        case "connect":
          if ( isset( $request->user_id ) ) {
            $user_id  = $request->user_id;
            $this->user_sockets[$user_id]  = $sender;
            $this->client_ids[$sender->id]  = $user_id;
            foreach( $clients as $client ) {
              if ( $sender->id != $client->id ) {
                $client->send( json_encode( array(
                  "event" =>  "connected",
                  "user"  =>  $user_id,
                  "time"  =>  time()
                ) ) );
              }
            }
          }
          break;
        case "reconnect":
          if ( isset( $request->user_id ) ) {
            $user_id  = $request->user_id;
            foreach( $clients as $client ) {
              if ( $sender->id != $client->id ) {
                $client->send( json_encode( array(
                  "event" =>  "connected",
                  "user"  =>  $user_id,
                  "time"  =>  time()
                ) ) );
              }
            }
          }
          break;
        case "message":
          $message  = json_encode( $request );
          $is_user  = ( isset( $request->groupID ) && (int)$request->groupID !== 0 ) ? false : true;
          if ( $is_user ) {
            $target_id  = $request->targetID;
            if ( isset( $this->user_sockets[$target_id] ) ) {
              $this->user_sockets[$target_id]->send( $message );
            }
          }
          else {
            if ( isset( $request->users ) && $request->users ) {
              foreach( $request->users->users as $user ) {
                if ( isset( $this->user_sockets[$user] ) && $user != $user_id ) {
                  $this->user_sockets[$user]->send( $message );
                }
              }
            }
          }
          break;
        case "block":
          $users = $request->users;
          $blocked = json_encode( array( "event" => "blocked", "user" => $request->user ) );
          foreach( $users as $user ) {
            if ( isset( $this->user_sockets[$user] ) ) {
              $this->user_sockets[$user]->send( $blocked );
            }
          }
          break;
        case "unblock":
          $users = $request->users;
          $blocked = json_encode( array( "event" => "unblocked", "user" => $request->user ) );
          foreach( $users as $user ) {
            if ( isset( $this->user_sockets[$user] ) ) {
              $this->user_sockets[$user]->send( $blocked );
            }
          }
          break;
        case "status":
          $message = json_encode( $request );
          foreach( $clients as $client ) {
            if ( $sender->id != $client->id ) {
              $client->send( $message );
            }
          }
          break;
        case "typing":
          $user = $request->user;
          $users = ( isset( $request->group ) ) ? (array)$request->group->users : false;

          $data = json_encode(
            array(
              "event" => "typing",
              "idx" => $request->idx,
              "idn" => $request->idn,
              "user" => $user
            )
          );

          if ( $users !== false ) {
            foreach( $users as $uid ) {
              if ( (int)$uid === (int)$user ) {
                continue;
              }
              if ( isset( $this->user_sockets[$uid] ) ) {
                $this->user_sockets[$uid]->send( $data );
              }
            }
          }
          else {
            if ( isset( $this->user_sockets[$request->idx] ) ) {
              $this->user_sockets[$request->idx]->send( $data );
            }
          }
          break;
        case "seen":
          $user = $request->user;
          $users = ( $request->group !== false ) ? (array)$request->group->users : false;

          $data = json_encode(
            array(
              "event" => "seen",
              "idx" => $request->idx,
              "idn" => $request->idn
            )
          );

          if ( $users !== false ) {
            foreach( $users as $uid ) {
              if ( (int)$uid === (int)$user ) {
                continue;
              }
              if ( isset( $this->user_sockets[$uid] ) ) {
                $this->user_sockets[$uid]->send( $data );
              }
            }
          }
          else {
            if ( isset( $this->user_sockets[$request->idx] ) ) {
              $this->user_sockets[$request->idx]->send( $data );
            }
          }
          break;
        default:
          break;
      }
    }
  
    public function onClientConnected( SocketServer $server, SocketClient $newClient ) {
      /*$clients  = $server->getClients();
      foreach( $clients as $client ) {
        if ( $newClient != $client ) {
          $client->send( json_encode( array(
            "event" =>  "connected",
            "user"  =>  $newClient->id,
            "time"  =>  time()
          ) ) );
        }
      }*/
  	}
  
    public function onClientDisconnected( SocketServer $server, SocketClient $leftClient ) {
      if ( !isset( $this->client_ids[$leftClient->id] ) ) {
        return;
      }

      $user_id  = $this->client_ids[$leftClient->id];
      $clients  = $server->getClients();
      unset( $this->client_ids[$leftClient->id], $this->user_sockets[$user_id] );

      foreach( $clients as $client ) {
        if ( $client != $leftClient ) {
          $client->send( json_encode(
            array(
              "event" =>  "disconnected",
              "user"  =>  $user_id,
              "time"  =>  time()
            )
          ) );
        }
      }
    }
  
    public function onLogMessage( SocketServer $server, $message ) {
      //echo $message.PHP_EOL;
  	}
  }
  
  try {
    $webSocket  = new SocketServer( ipgo( "socket_host" ), ipgo( "socket_port" ) );
    $webSocket->addListener( new Server() );
  	$webSocket->start();
  }
  catch( Exception $e ) {
    echo "Fatal exception occured: ".$e->getMessage()." in ".$e->getFile()." on line ".$e->getLine().PHP_EOL;
  }
?>