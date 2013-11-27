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

session_start();
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

#IF no channel defined, kill the post.
if ( !isset($_SESSION['chan']) )
	{
		echo "<script>alert('Can`t Post!!!, No Session');</script>";
		exit;
	}

#IF channel is empty, kill the post.
if ( empty($_SESSION['chan']) )
	{
		echo "<script>alert('Can`t Post!!!, No Chan');</script>";
		exit;
	}

	#Trying to post without first need to recive a message BUGID: #1.
	if ( isset ( $_GET['sid'] ) && isset ( $_POST['nick'] ) && isset ( $_POST['msg'] ) )
	{
		#IRC.PHP HUNG DO IT ANOTHER WAY
		/*
			include "irc.php";
			$irc = new WdgssAjaxIrc ( ) ;
			$x = $irc -> EncodeIp       ( $_SERVER['REMOTE_ADDR'] ) ;
			echo $irc -> PreSendMessage ( $_SERVER['REMOTE_ADDR'], $_POST['msg'], '#' . $_SESSION['chan'] ) ;
		*/

			#SEND "PRE" MESSAFE
			$file = "data/{$_SERVER['REMOTE_ADDR']}.message.sock";
			$f = @fopen ( $file, 'w' ) ;
			@fwrite($f, $_SESSION['chan'] . "{WDG}" . $_POST['msg'] ) ;
			@fclose($f);
	}
	else
	{
		#ERROR MISSING ( SID=Session ID, or Nick, or Message (=msg) )
		echo "ERROR [No Post]";
	}
?>