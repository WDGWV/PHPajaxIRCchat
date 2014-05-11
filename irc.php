<?php
/*
                       :....................,:,              
                    ,.`,,,::;;;;;;;;;;;;;;;;:;`              
                  `...`,::;:::::;;;;;;;;;;;;;::'             
                 ,..``,,,::::::::::::::::;:;;:::;            
                :.,,``..::;;,,,,,,,,,,,,,:;;;;;::;`          
               ,.,,,`...,:.:,,,,,,,,,,,,,:;:;;;;:;;          
              `..,,``...;;,;::::::::::::::'';';';:''         
              ,,,,,``..:;,;;:::::::::::::;';;';';;'';        
             ,,,,,``....;,,:::::::;;;;;;;;':'''';''+;        
             :,::```....,,,:;;;;;;;;;;;;;;;''''';';';;       
            `,,::``.....,,,;;;;;;;;;;;;;;;;'''''';';;;'      
            :;:::``......,;;;;;;;;:::::;;;;'''''';;;;:       
            ;;;::,`.....,::;;::::::;;;;;;;;'''''';;,;;,      
            ;:;;:;`....,:::::::::::::::::;;;;'''':;,;;;      
            ';;;;;.,,,,::::::::::::::::::;;;;;''':::;;'      
            ;';;;;.;,,,,::::::::::::::::;;;;;;;''::;;;'      
            ;'';;:;..,,,;;;:;;:::;;;;;;;;;;;;;;;':::;;'      
            ;'';;;;;.,,;:;;;;;;;;;;;;;;;;;;;;;;;;;:;':;      
            ;''';;:;;.;;;;;;;;;;;;;;;;;;;;;;;;;;;''';:.      
            :';';;;;;;::,,,,,,,,,,,,,,:;;;;;;;;;;'''';       
             '';;;;:;;;.,,,,,,,,,,,,,,,,:;;;;;;;;'''''       
             '''';;;;;:..,,,,,,,,,,,,,,,,,;;;;;;;''':,       
             .'''';;;;....,,,,,,,,,,,,,,,,,,,:;;;''''        
              ''''';;;;....,,,,,,,,,,,,,,,,,,;;;''';.        
               '''';;;::.......,,,,,,,,,,,,,:;;;''''         
               `''';;;;:,......,,,,,,,,,,,,,;;;;;''          
                .'';;;;;:.....,,,,,,,,,,,,,,:;;;;'           
                 `;;;;;:,....,,,,,,,,,,,,,,,:;;''            
                   ;';;,,..,.,,,,,,,,,,,,,,,;;',             
                     '';:,,,,,,,,,,,,,,,::;;;:               
                      `:;'''''''''''''''';:.                 
                                                             
     ,,,::::::::::::::::::::::::;;;;,::::::::::::::::::::::::
     ,::::::::::::::::::::::::::;;;;,::::::::::::::::::::::::
     ,:; ## ## ##  #####     ####      ## ## ##  ##   ##  ;::
     ,,; ## ## ##  ## ##    ##         ## ## ##  ##   ##  ;::
     ,,; ## ## ##  ##  ##  ##   ####   ## ## ##   ## ##   ;::
     ,,' ## ## ##  ## ##    ##    ##   ## ## ##   ## ##   :::
     ,:: ########  ####      ######    ########    ###    :::
     ,,,:,,:,,:::,,,:;:::::::::::::::;;;:::;:;:::::::::::::::
     ,,,,,,,,,,,,,,,,,,,,,,,,:,::::::;;;;:::::;;;;::::;;;;:::
                                                             
    	     (c) WDGWV. 2013, http://www.wdgwv.nl            
    	 websites, Apps, Hosting, Services, Development.      

	PHP Ajax IRC Chat (PAIC)
	Version:   0.0.0.1 Alpha
	Website:   http://www.wdgwv.com
	Revision:  100
	Last Edit: 27-NOV-2013 21:00 By WdG
	Authors:   WdG, Wesley de Groot, wes@wdgwv.com, http://www.wdgwv.com
		       Your initials, Your name, your mail, your website
*/

if ( file_exists ( "./killall" ) )
	{
		exit('Al Died');
	}

@session_start();


//error_reporting(E_ALL ^E_PARSE);
//error_reporting(0);
	
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

 if ( isset ( $_GET['server'] ) )
  $_SESSION['server'] = $_GET['server'];

 if ( isset ( $_GET['chan'] ) )
  $_SESSION['chan'] = $_GET['chan'];
  
 if ( isset ( $_GET['nick'] ) )
  $_SESSION['nick'] = $_GET['nick'];

 if ( !isset ( $_SESSION['server'] ) )
  $_SESSION['server'] = 'home.wdgss.nl';

 if ( !isset ( $_SESSION['chan'] ) )
  $_SESSION['chan'] = 'woc';

 if ( !isset ( $_SESSION['nick'] ) )
  $_SESSION['nick'] = 'Guest_' . rand(1000,9999);

define('nl','<br />');
  
Class WdgssAjaxIrc
	{
		protected $debugging;
		protected $ip;
		protected $lang;
		protected $connection;
		protected $nickname;
		
		private function handle_error($type, $string, $file, $line, $vars)
			{
				switch ( $type )
					{
						case FATAL:
						case ERROR:
						case WARNING:
							$this -> console ( $_SERVER['REMOTE_ADDR'] , $type, $file . ' @ ' . $line ) ;
						break;
					}
			}
			
		public function __construct ( $lan = 'en' , $deb = false ) 
			{
				set_error_handler(array($this, 'handle_error'));

				$this->debugging = $deb;
				$this->lang      = $lan;
				
					if ( file_exists ( "data/" . $_SERVER['REMOTE_ADDR'] . ".nick" ) && !isset ( $_GET['nick'] ) )
						 $this->nickname  = file_get_contents("data/" . $_SERVER['REMOTE_ADDR'] . ".nick");
					else
						$this -> nickname = $_SESSION['nick'];

					if ( file_exists ( "lang/" . $lan . ".php" ) )
							include "lang/" . $lan . ".php";
					elseif ( file_exists ( "lang/en.php" ) )
							include "lang/eng.php";
					else
							return 0; #ByPass
							#exit('Missing Language File!!!');
		    }
		    
		protected function Lang   ( $text ) 
			{
				return $lang [ $text ] ;
			}
			
		public function EncodeIp  ( $ip )
			{
				$ip = explode (".", $ip) ;
				return chr ( $ip [ 0 ] ) . 
				       chr ( $ip [ 1 ] ) . 
				       chr ( $ip [ 2 ] ) . 
				       chr ( $ip [ 3 ] ) ;
			}
			
		public function DecodeIp  ( $ip )
			{
				return 	ord ( 
							substr ( $ip , 0 , 1 ) 
						) . 
			   '.' . 
						ord (
							substr ( $ip , 1 , 1 )
						) .
			   '.' . 
						ord ( 
							substr ( $ip , 2 , 1 )
						) . 
			   '.' . 
						ord ( 
							substr ( $ip , 3 , 1 )
						)
				   ;
			}
    
    private function Debug     ( $tx )
		{
			if ( $this -> debugging )
				{
					echo $tx . nl;
				}
		}

    private function console ( $ip, $nick, $e, $E = 'red' )
		{
	         $X = "<table><tr><td><font color='{$E}'>*** (".date('H:i:s').")</font></td><td><font color='{$E}'>&lt;{$nick}&gt;</font></td><td><font color='{$E}'>{$e}</font></td></tr></table>";
	         $file = "data/{$ip}.txt.sock";
	         $f = @fopen ( $file, 'a' ) ;
	         @fwrite($f,$X);
	         @fclose($f);
		}

	private function sent_raw_command($command) 
		{
			@fwrite($this->connection, $command . "\n");
		}

	private function nickname($str) 
		{
			return substr ( array_shift ( explode ( '!' , $str ) ) , 0 ) ;
		} 
		
	public function replace2html($topicin, $s=null, $maxlength = -1, $dourl = -1) 
		{
		  $topicin = preg_replace("/(.?)\\\$\+(.?)/", null,  $topicin); #strip " $+ "
		  $topicin = preg_replace("/%color/",      "||14", $topicin); #rep: %color to color4
		  $topicin = preg_replace("//",             null,  $topicin); #strip "" Unknown Mod.

			$count = 0;
			$topicout = "";
			$lcount = 0;
			
			$flags = array ( 
								'c' => array ( 'b' => false, 'k' => false, 'u' => false, 'r' => false ),
								'p' => array ( 'b' => false, 'k' => false, 'u' => false, 'r' => false)
						   ) ;
							
			$colours['cf'] = "#000000";
			$colours['cb'] = "";
			$colours['pf'] = "#000000";
			$colours['pb'] = "";
			$colours['s'] = "";

			$colourcode = array ( 
									"#FFFFFF", "#000000" , "#000080" , "#008000" , "#FF0000" , "#800000" , "#800080" , "#FE4E00" , "#FFFF00" , "#00FF00" , "#008080" , "#00FFFF" , "#0000FF" , "#FF00FF" , "#808080" , "#C0C0C0" , "#000000" , "#000000" , "#000000" , "#000000" 
								) ;

			$topicin = htmlspecialchars($topicin);
			$topiclen = strlen($topicin);

			$topicout .= "<span style=\"color:".$colourcode[1]."; font-family: Tahoma;\">";
			
			for ($count=0; $count<$topiclen; $count++) {
				$chr = substr($topicin, $count, 1);
				$cparam = substr($topicin, $count + 1, 5);
				
				if ( ord($chr) == 2 ) {
					if ( $flags['c']['b'] ) {
						$flags['c']['b'] = false;
					} else {
						$flags['c']['b'] = true;
					}
				}
				if ( ord($chr) == 31 ) {
					if ( $flags['c']['u'] ) {
						$flags['c']['u'] = false;
					} else {
						$flags['c']['u'] = true;
					}
				}
				if ( ord($chr) == 22 ) {
					if ( $flags['c']['r'] ) {
						$flags['c']['r'] = false;
					} else {
						$flags['c']['r'] = true;
						$flags['c']['k'] = false;
					}
				}
				if ( ord($chr) == 3 ) {
					if ( $flags['c']['k'] ) {
						$flags['c']['k'] = false;
					} else {
						$flags['c']['k'] = true;
						$flags['c']['r'] = false;
					}
					preg_match_all("/^([0-1]?[0-9])?(,([0-1]?[0-9]))?/", $cparam, $matched);
					if ( $matched[0][0] != "" ) {
						$flags['c']['k'] = true;
						$flags['c']['r'] = false;
						if ( $matched[1][0] != "" ) {
							$colours['cf'] = $colourcode[(integer) $matched[1][0]];
						}
						if ( $matched[3][0] != "" ) {
							$colours['cb'] = $colourcode[(integer) $matched[3][0]];
						}
						$count += strlen($matched[0][0]);
					}
					if ( $flags['c']['k'] == false ) {
						$colours['cf'] = "#000000";
						$colours['cb'] = "";
					}
				}
				if ( ord($chr) == 15 ) {
					$flags['c']['b'] = false;
					$flags['c']['u'] = false;
					$flags['c']['r'] = false;
					$flags['c']['k'] = false;
					$colours['cf'] = "#000000";
					$colours['cb'] = "";
					$colours['pf'] = "#000000";
					$colours['pb'] = "";
				}
				
				$cstyle = "font-family: Tahoma; ";
				if ( ( $flags['c']['b'] != $flags['p']['b'] ) or ( $flags['c']['u'] != $flags['p']['u'] ) or ( $flags['c']['r'] != $flags['p']['r'] ) or ( $flags['c']['k'] != $flags['p']['k'] )  or ( $colours['cf'] != $colours['pf'] ) or ( $colours['cb'] != $colours['pb'] ) ) {
					if ( $flags['c']['b'] != $flags['p']['b'] ) {
						$flags['p']['b'] = $flags['c']['b'];
					}
					if ( $flags['c']['u'] != $flags['p']['u'] ) {
						$flags['p']['u'] = $flags['c']['u'];
					}
					if ( $flags['c']['r'] != $flags['p']['r'] ) {
						$flags['p']['r'] = $flags['c']['r'];
					}
					if ( $flags['c']['k'] != $flags['p']['k'] ) {
						$flags['p']['k'] = $flags['c']['k'];
					}
					
					if ( $flags['c']['b'] ) {
						$cstyle .= "font-weight: bold; ";
					}
					if ( $flags['c']['u'] ) {
						$cstyle .= "text-decoration: underline; ";
					}
					if ( $flags['c']['r'] ) {
						if ( $colours['cb'] == "" ) {
							$cstyle .= "color:#FFFFFF; background-color:".$colours['cf']."; ";
						} else {
							$cstyle .= "color:".$colours['cb']."; background-color:".$colours['cf']."; ";
						}
						$colours['pf'] = $colours['pf'];
						$colours['cb'] = $colours['cb'];
					}
					if ( $colours['cb'] == "" ) {
						$cstyle .= "color:".$colours['cf']."; ";
					} else {
						$cstyle .= "color:".$colours['cf']."; background-color:".$colours['cb']."; ";
					}
					$colours['pf'] = $colours['cf'];
					$colours['pb'] = $colours['cb'];
					
					$topicout .= "</span><span style=\"".$cstyle."\">";
				} else {
					if ( $maxlength != -1 ) {
						if ( $maxlength > $lcount ) {
							$topicout .= $chr;
						}
					} else {
						$topicout .= $chr;
					}
					$lcount++;
				}
			}
			
			if ( $flags['c']['b'] ) {
				$flags['c']['b'] = false;
			}
			if ( $flags['c']['u'] ) {
				$flags['c']['u'] = false;
			}
			if ( $flags['c']['k'] ) {
				$flags['c']['k'] = false;
			}
			if ( $flags['c']['r'] ) {
				$flags['c']['r'] = false;
			}
			
			$topicout .= "</span>";

			if ( $dourl ) 
				{
					$topicout = preg_replace("/(style=\"([^\"]*)\"[^<]*?)?(http:\/\/[^\s<]*)/i", "$1<a href=\"$3\" style=\"font-family: Tahoma; $2\" target=\"_blank\">$3</a>", $topicout);
					$topicout = preg_replace("/(style=\"([^\"]*)\"[^<]*?)?(https:\/\/[^\s<]*)/i", "$1<a href=\"$3\" style=\"font-family: Tahoma; $2\" target=\"_blank\">$3</a>", $topicout);
					$topicout = preg_replace("/(style=\"([^\"]*)\"[^<]*?)?(ftp:\/\/[^\s<]*)/i", "$1<a href=\"$3\" style=\"font-family: Tahoma; $2\" target=\"_blank\">$3</a>", $topicout);
					$topicout = preg_replace("/(style=\"([^\"]*)\"[^<]*?)?(irc:\/\/[^\s<]*)/i", "$1<a href=\"$3\" style=\"font-family: Tahoma; $2\" target=\"_blank\">$3</a>", $topicout);
				}
		  $topicout = preg_replace("/" . chr(2) . "(.*)" . chr(2) . "/"	, "<b>\\1</b>"	, $topicout); # BOLD
		  $topicout = preg_replace("/" . chr(182) . "/"					, "&para;"		, $topicout); # &para; ;D 
		  $topicout = preg_replace("/�/"  								, null			, $topicout); # Weird GONE... 
		  $topicout = preg_replace("/\|\|/"								, "<br>"		, $topicout); # || is newline ;D
		  $topicout = preg_replace("/<br><br>/"							, "<br>"		, $topicout); # strip more than 1 newline

		if($s != null) {
		 $topicout = preg_replace("/" . $s . "/","<font style='background: yellow'>" .  $s . "</font>", $topicout);
		}


			if ( $maxlength == -1 ) {
				return $topicout;
			} else {
				if ( $topiclen < $maxlength ) {
					return $topicout;
				} else {
					return $topicout . "...";
				}
			}
	}
	
	public function PreSendMessage ( $ip, $message, $channel ) 
		{
			$file = "data/{$ip}.message.sock";
			$f = @fopen ( $file, 'w' ) ;
			@fwrite($f, $channel . "{WDG}" . $message ) ;
			@fclose($f);
		}

	public function SendMessage ( $ip, $message, $channel ) 
		{
			$file = "data/{$ip}.lastmessage.sock";
			$f = @fopen ( $file, 'w' ) ;
			@fwrite($f, ( date("Hi") + ( 5 ) ) ) ;
			@fclose($f);
			$this -> sent_raw_command("PRIVMSG " . $channel . " :" . $message);
			$this -> sent_raw_command ( "NAMES #{$_SESSION['chan']}" ) ;
			$this -> console ( $ip, $this->nickname, $message ) ;
		}
	
	public function RunServer ( $ip ) 
		{
		  $done = "<b>\\/</b>";
		  $this->debug("Starting (Debug) Log... " . $done);
-
		  $this->debug("Connecting... " . $done);
		  $this->console($ip,'Startup','Loading Addional Configs...');
		    if ( !isset ( $_SESSION['server'] ) )
				{
					$_SESSION['server'] = 'irc.wocnl.nl';
				}
			if ( ! ( $this->connection = ( @fsockopen($server = $_SESSION['server'], $port = 6667, $errno, $errstr, 10) ) ) )
			{
				$this -> debug("Can`t Connect... " . $done);
				$this -> console ( $ip, 'System', "Error: Can`t Connect! Reason: " . $errstr ) ;
			}
			Else
			{ /* &frac14;  &frac12;  &frac34; */
				$this -> debug("Connected... " . $done);
				$this -> console($ip, 'Info', "Connected To " . $server . " @ " . $port);
				$this -> debug("Go And Run That Loop...");

				while (!feof($this->connection)) 
				{
					if ( file_exists ( "./killall" ) )
						{
							exit('Al Died');
						}

					if (connection_aborted()) 
						{
							fwrite( $this->connection, "NICK disconnected\n");
						}

					if ( file_exists ( "data/{$ip}.message.sock" ) ) // <dit doet het pas na een bericht..? WTF
						{
							$f = file_get_contents("data/{$ip}.message.sock");
							$f = explode ( "{WDG}" , $f );
							$this -> SendMessage ( $ip, $message = $f[1], $channel = $f[0] ) ;
							if ( ! ( @unlink("data/{$ip}.message.sock") ) )
								{
									$this -> console ( $ip, 'ERROR', 'Cant Delete Some Neccorary Files.. Disconnecting now...' );
									$this -> sent_raw_command('QUIT :Force Quit By Error, WesDeGroot Ajax Irc Webchat.');
								}
						}

					$input = array(
						"from" 			=> "",
						"event" 		=> "",
						"to" 			=> "",
						"parameters" 	=> ""
					);
					
					$data = fgets($this->connection);
					//	$this->console($ip,'DEBUG',$data);
					

					if ( file_exists ( "data/{$ip}.lastmessage.sock" ) )
						{
							$DT = file_get_contents("data/{$ip}.lastmessage.sock");
						}
					else
						{
							$DT = ( date("Hi") + ( 5 ) );
						}
						
					$DT = substr ($DT , 0, 6 );

					if ( $DT > "2400" )
						$DT = "0005";
								
					if ( $DT == date("Hi" ) )
						{
							$this -> console($ip, 'Warning', "I`ll Gonna Disconnect. I Want Once In 5 Minutes An Action From You");
							$this -> sent_raw_command("QUIT :Time Out - WesDeGroot Ajax Webchat");
							@unlink("data/{$ip}.lastmessage.sock");
							@unlink("data/{$ip}.nickname");
						}
					
					if (preg_match("/:([^\s]+) NOTICE AUTH \:\*\*\* Found your h*ostname(.*)/", $data, $params) || 
					    preg_match("/:([^\s]+) NOTICE AUTH \:\*\*\* Couldn\'t resolve your hostname\; using your IP address instead(.*)/", $data, $params)) 
					    {
							$this -> server_name = $params[1];					
							$this -> sent_raw_command("USER {$this->nickname} {$ip} {$this->nickname} :WDG Ajax Irc ({$ip})");
							$this -> sent_raw_command("NICK {$this->nickname}");
							$this -> debug("Sending Auth..." . $done);
						}

					elseif (preg_match("/PING \:(.*)/", $data, $host)) 
						{					
							$this -> sent_raw_command("PONG " . $host[1]);
							$this -> sent_raw_command("PONG :" . $host[1]);					
							$this -> debug("Sended Ping-&gt;Pong... " . $host[1] . ' ' . $done);
						}
						
					elseif (preg_match("/:(.*) PRIVMSG (.*) :" . chr ( 1 ) . "VERSION" . chr ( 1 ) . "/", $data, $params)) 
						{
							$this -> sent_raw_command($x="NOTICE " . $this -> nickname ( $params[1] ) . " :" . chr ( 1 ) . "VERSION WesDeGroot Ajax Irc Version 0.1" . chr ( 1 ) );
							$this -> Console($ip,'Info',$this -> nickname ( $params[1] ) . ' Asked Your Version'); 
						}
						
					elseif (preg_match("/:(.*) PRIVMSG (.*) :" . chr ( 1 ) . "QUIT" . chr ( 1 ) . "/", $data, $params)) 
						{
							$this -> sent_raw_command($x="QUIT :FORCED DISCONNECT BY ".$this -> nickname ( $params[1] )." @ WesDeGroot Ajax Irc Version 0.1" );
							$this -> Console($ip,'Info',$this -> nickname ( $params[1] ) . ' disconnected you from this network'); 
						}
						
					elseif (preg_match("/(.*) 376 (.*)\:End of \/MOTD command\./", $data) || preg_match("/.* 422 .*\:MOTD File is missing/", $data)) 
						{
							$this -> sent_raw_command("JOIN #{$_SESSION['chan']}");
							$this -> sent_raw_command("NAMES #{$_SESSION['chan']}");
							$this -> debug("Joining Channels...");
							$this -> SendMessage($ip,'I`ll Sended A Action',"#{$_SESSION['chan']}");
						}
						
					elseif (preg_match("/(.*) 353 {$this->nickname}/", $data)) 
						{
							$ex = explode(" ",$data); #6 is usable #2 is 353 or 366..
							$xe = explode(":",$data); #Vanaf De Namen Werken...
							
							# Alleen Nicknames Nemen
							$ee = array();
							for ( $i=1; $i < sizeof($xe); $i++) 
								{ 
									$E_E = explode ( "!" , $xe [ $i ] ) ; 
									$ee[$i] = $E_E[0]; 
								}
							$users = explode(" ", $ee[2]);

							 unlink ( 'data/' . $ip . '.nicklist' ) ;
							 
							 $q = fopen ( 'data/' . $ip . '.nicklist' , 'a' ) ;
							 fwrite ( $q , implode ( ',' , $users ) ) ;
							 fclose ( $q ) ;							 
						}						
						
					elseif (preg_match("/:([^\s]+) ([^\s]+) ([^\s]+) [:]?(.*)/", $data, $params))
						{
								$input = array(
									"from" 			=> $this -> nickname ( $params[1] ),
									"event" 		=> $params[2],
									"destination" 	=> $parameters[3],
									"parameters" 	=> $params[4]
								);							

								if ( !preg_match ( "#(.*)\.(.*)\.(.*)#" , $input['from'] ) )
									{
										if ( $input['event'] == 'PRIVMSG' )
											{
												if ( preg_match ( "#" . chr(1) . "ACTION#" , $input['parameters'] ) )
													{
														#Replace Actions..
															$input['parameters'] = preg_replace ( "#" . chr(1) . "ACTION#" , null, $input['parameters'] ) ;
															$input['parameters'] = preg_replace ( "#" . chr(1) . "#" , null, $input['parameters'] ) ;
															$Y = "<tr><td><font color='purple'>(" . date("H:i:s") . ")</font></td>
																	  <td><font color='purple'>* " . $input['from'] . "<font></td>
																	  <td><font color='purple'>";
															$X = addslashes(chr(3) . '6' . $input['parameters']);
															$Z = "</td></tr>";
													}
												else
													{
														#Parse Messages
															$Y = "<tr><td>(" . date("H:i:s") . ")</td><td>&lt;" . $input['from'] . "&gt;</td><td>";
															$X = addslashes($input['parameters']);
															$Z = "</td></tr>";
													}
											}
										elseif ( $input['event'] == 'NOTICE')
											{
												$Y = "<tr><td>(" . date("H:i:s") . ")</td><td>NOTICE-&gt;" . $input['from'] . ":</td><td>";
												$X = addslashes($input['parameters']);
												$Z = "</td></tr>";
											}
										else 
											{
											 // echi $input['event']
											}
											
										if ( isset ( $X, $Y, $Z ) )
											{
												$X = $this -> replace2html ( $X, $this->nickname, -1, true );
												$file = "data/{$ip}.txt.sock";
												$f = fopen ( $file, 'a' ) ;
												fwrite($f,$Y . $X . $Z);
												fclose($f);
											}
									}
						 }
				 }			
		 }
				$this -> debug("DisConnected... " . $done);
				$this -> console($ip, 'Warning', "DisConnected From " . $server . " @ " . $port);
		 } // END FUNCT




	
	public function GetText   ( $ip )
		{
			$file = "data/{$ip}.txt.sock";
			  if ( file_exists ( $file ) )
				   {
						//$this -> sent_raw_command ( "NAMES #{$_SESSION['chan']}" ) ;
						echo "<font color='black'>" .
							 file_get_contents($file) .
							 "</font>";
							 
						if ( ! ( @unlink ( $file ) ) )
							 {
								echo "<font color='red'>(".date('H:i:s').") &lt;System&gt; Error: Can`t Delete Your Old Messages...</font><br>";
							 }
				   }
		}
	 
	private function Color     ( $co )
		{
			switch ( $co )
				{
					case 'info':
					case 'blue':
						return '<font color=\'blue\'>';
					break;

					case 'error':
					case 'red':
						return '<font color=\'red\'>';
					break;
				}
		}
	  
	public function PrintMain ( $ip )
		{
			$nick = $this -> nickname;
			$ipe  = md5($this->EncodeIp($_SERVER['REMOTE_ADDR']));
			$myid = explode(".",$_SERVER['REMOTE_ADDR']);
			$myid = "&#{$myid[0]};&#{$myid[1]};&#{$myid[2]};&#{$myid[3]};";
			$myse = explode(".",$_SERVER['REMOTE_ADDR']);
			$myse = "{$myse[0]}{$myse[1]}{$myse[2]}{$myse[3]}";
?>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<!--
/*
                       :....................,:,              
                    ,.`,,,::;;;;;;;;;;;;;;;;:;`              
                  `...`,::;:::::;;;;;;;;;;;;;::'             
                 ,..``,,,::::::::::::::::;:;;:::;            
                :.,,``..::;;,,,,,,,,,,,,,:;;;;;::;`          
               ,.,,,`...,:.:,,,,,,,,,,,,,:;:;;;;:;;          
              `..,,``...;;,;::::::::::::::'';';';:''         
              ,,,,,``..:;,;;:::::::::::::;';;';';;'';        
             ,,,,,``....;,,:::::::;;;;;;;;':'''';''+;        
             :,::```....,,,:;;;;;;;;;;;;;;;''''';';';;       
            `,,::``.....,,,;;;;;;;;;;;;;;;;'''''';';;;'      
            :;:::``......,;;;;;;;;:::::;;;;'''''';;;;:       
            ;;;::,`.....,::;;::::::;;;;;;;;'''''';;,;;,      
            ;:;;:;`....,:::::::::::::::::;;;;'''':;,;;;      
            ';;;;;.,,,,::::::::::::::::::;;;;;''':::;;'      
            ;';;;;.;,,,,::::::::::::::::;;;;;;;''::;;;'      
            ;'';;:;..,,,;;;:;;:::;;;;;;;;;;;;;;;':::;;'      
            ;'';;;;;.,,;:;;;;;;;;;;;;;;;;;;;;;;;;;:;':;      
            ;''';;:;;.;;;;;;;;;;;;;;;;;;;;;;;;;;;''';:.      
            :';';;;;;;::,,,,,,,,,,,,,,:;;;;;;;;;;'''';       
             '';;;;:;;;.,,,,,,,,,,,,,,,,:;;;;;;;;'''''       
             '''';;;;;:..,,,,,,,,,,,,,,,,,;;;;;;;''':,       
             .'''';;;;....,,,,,,,,,,,,,,,,,,,:;;;''''        
              ''''';;;;....,,,,,,,,,,,,,,,,,,;;;''';.        
               '''';;;::.......,,,,,,,,,,,,,:;;;''''         
               `''';;;;:,......,,,,,,,,,,,,,;;;;;''          
                .'';;;;;:.....,,,,,,,,,,,,,,:;;;;'           
                 `;;;;;:,....,,,,,,,,,,,,,,,:;;''            
                   ;';;,,..,.,,,,,,,,,,,,,,,;;',             
                     '';:,,,,,,,,,,,,,,,::;;;:               
                      `:;'''''''''''''''';:.                 
                                                             
     ,,,::::::::::::::::::::::::;;;;,::::::::::::::::::::::::
     ,::::::::::::::::::::::::::;;;;,::::::::::::::::::::::::
     ,:; ## ## ##  #####     ####      ## ## ##  ##   ##  ;::
     ,,; ## ## ##  ## ##    ##         ## ## ##  ##   ##  ;::
     ,,; ## ## ##  ##  ##  ##   ####   ## ## ##   ## ##   ;::
     ,,' ## ## ##  ## ##    ##    ##   ## ## ##   ## ##   :::
     ,:: ########  ####      ######    ########    ###    :::
     ,,,:,,:,,:::,,,:;:::::::::::::::;;;:::;:;:::::::::::::::
     ,,,,,,,,,,,,,,,,,,,,,,,,:,::::::;;;;:::::;;;;::::;;;;:::
                                                             
    	     (c) WDGWV. 2013, http://www.wdgwv.nl            
    	 websites, Apps, Hosting, Services, Development.      

	PHP Ajax IRC Chat (PAIC)
	Version:   0.0.0.1 Alpha
	Website:   http://www.wdgwv.com
	Revision:  100
	Last Edit: 27-NOV-2013 21:00 By WdG
	Authors:   WdG, Wesley de Groot, wes@wdgwv.com, http://www.wdgwv.com

*/
-->

<script type="text/javascript" src="http://jquery.com/src/jquery-latest.js"></script>
<script type="text/javascript" src="jquery.forceredraw.js"></script>
<script language='javascript' type='text/javascript'>
var sec         = 5; //? seconden interval

function GetText () {
    /*    chat window */
        $.ajax({ 
			url: 'get.php?sid=SESSIONID' + ((Math.floor(Math.random()*10))+1), 
			type: "get", 
			success: function(data) { 
										$('#irc').append('<table>' + data + '</table>'); 
										setTimeout('GetText()'  , sec * 1000); 
									} 
				});
				
        $('#irc').css("height","500");
        $('#irc').css("width","650");
        $('#irc').css("overflow","scroll");
//        $('#irc').forceRedraw(true);

    /* chatwindow omlaag scrollen */
        document.getElementById("irc").scrollTop =  document.getElementById("irc").scrollHeight + 500;
}

function GetNicks () {
    /* nicklist */
        $.ajax({ 
			url: 'nicklist.php?sid=SESSIONID' + ((Math.floor(Math.random()*10))+1), 
			type: "get", 
			success: function(data) { 
										$('#nicklist').html(data); 
										setTimeout('GetNicks()'  , sec * 1000);
									} 
				});
        $('#nicklist').css("height","500");
        $('#nicklist').css("width","150");
        $('#nicklist').css("overflow","scroll");
//        $('#nicklist').forceRedraw(true);
}

function StartChat () {
    /* hide nickname ding */
        $('#hidemesoon').css("visibility","hidden");

    /* hide nickname ding */
        $('#showmesoon').css("visibility","");

    /* opnieuw aanvragen in x seconden */
        GetNicks();
        GetText();
}
</script>

<table>
	<tr>
		<td>
			<div name='irc' id='irc' class='irc' width='150' height='500' style='overflow: scroll;'>
				<table>
					<tr>
						<td>
							&nbsp;
						</td>
						<td>
							Info:
						</td>
						<td>
								<?php echo $this->Color('info') . "Wesley De Groot`s Ajax Irc Chat..."; ?><br>
								Version: 0.1<br>
								Website: <a href='http://www.wdgwv.nl' target='_blank'><?php echo $this -> Color('error'); ?>http://www.wdgwv.nl</font></a><br>
								<br>
								<br>
								Your Id: <?php echo $myid; ?><br>
								<Br>
								<br>
								Server: <?php echo $_SESSION['server']; ?><br>
								Channel: #<?php echo $_SESSION['chan']; ?><br>
						</td>
					</tr>
				</table>
			</div>
			
			<form method='post' action='post.php?sid=WDGIRC' target='post'>
				<table style='visibility:hidden' name='showmesoon' class='showmesoon' id='showmesoon'>
					<tr>
						<td>
							<input type='text' name='msg'><input type='hidden' name='nick' value='<?php echo $nick; ?>'>
						</td>
						<td>
							<input type='submit' value='Send'>
						</td>
					</tr>
				</table>
			</form>
		</td>
		<td>
			<div name='nicklist' id='nicklist' class='nicklist' width='150' height='500' style='overflow: scroll;'>
				<!--
				<table>
					<tr>
						<td>
							~
						</td>
						<td>
							NickList...
						</td>
					</tr>
				</table>
				-->
			</div>
			<br>
			<div id='hidemesoon' class='hidemesoon' name='hidemesoon'>
				<form method='post' action='server.php?sid=<?php echo $ipe; ?>' target='server'>
						<table>
							<tr>
								<td>
									<input type='text' name='nick' value='<?php echo $nick; ?>'>
								</td>
								<td>
									<input type='submit' value='Connect' Onclick='StartChat();'>
								</td>
							</tr>
						</table>
				</form>
			</div>
		</td>
	</tr>
</table>
			<iframe style='odisplay: none;' src='pressconnect.html' name='server'>
				Please Press Connect...
			</iframe>
			
			<iframe style='display: none;' src='pressconnect.html' name='post'>
				No Iframes?
			</iframe>
<?php
		}
}
?>