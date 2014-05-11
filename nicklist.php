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
error_reporting(E_ALL ^E_PARSE);
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

#HUNG
//include "irc.php";

/*
Need in this way
1. ~ 
2. &amp; (&)
3. @
4. %
5. +
6. &nbsp; (space) = No Status (empty)
*/

if ( file_exists ( 'data/' . $_SERVER['REMOTE_ADDR'] . '.nicklist' ) )
	{
		$f = file_get_contents('data/' . $_SERVER['REMOTE_ADDR'] . '.nicklist');
			if ( $f == null ) 
				{ 
					echo "ERROR?!"; 
				}
		$f = explode(',', $f);
		$nicklist = array();
		$nicklist['q'] = array(); // ~ Owner
		$nicklist['a'] = array(); // & Admin
		$nicklist['o'] = array(); // @ Operator
		$nicklist['h'] = array(); // % Halfop
		$nicklist['v'] = array(); // + Voice
		$nicklist['r'] = array(); // Nothing (R)egular
		
		for ( $i=0; $i<sizeof($f); $i++) 
		{ 
			switch ( substr ( $f[$i] , 0, 1 ) ) 
			{
				case '~':
					$nicklist['q'][] = substr ( $f[$i], 1, strlen($f[$i] ) ) ;
				break;

				case '&':
					$nicklist['a'][] = substr ( $f[$i], 1, strlen($f[$i] ) ) ;
				break;

				case '@':
					$nicklist['o'][] = substr ( $f[$i], 1, strlen($f[$i] ) ) ;
				break;

				case '%':
					$nicklist['h'][] = substr ( $f[$i], 1, strlen($f[$i] ) ) ;
				break;

				case '+':
					$nicklist['v'][] = substr ( $f[$i], 1, strlen($f[$i] ) ) ;
				break;
				
				default:
					$nicklist['r'][] = substr ( $f[$i], 0, strlen($f[$i] ) ) ;
				break;
			}
		}
		echo "<table>";
		for ( $i=0; $i<sizeof($nicklist['q']); $i++) 
			{ 
				echo "<tr><td>~</td><td>{$nicklist['q'][$i]}</td></tr>"; 
			}
			
		for ( $i=0; $i<sizeof($nicklist['a']); $i++) 
			{ 
				echo "<tr><td>&amp;</td><td>{$nicklist['a'][$i]}</td></tr>"; 
			}
		
		for ( $i=0; $i<sizeof($nicklist['o']); $i++) 
			{ 
				echo "<tr><td>@</td><td>{$nicklist['o'][$i]}</td></tr>"; 
			}
		
		for ( $i=0; $i<sizeof($nicklist['h']); $i++) 
			{ 
				echo "<tr><td>%</td><td>{$nicklist['h'][$i]}</td></tr>"; 
			}
		
		for ( $i=0; $i<sizeof($nicklist['v']); $i++) 
			{ 
				echo "<tr><td>+</td><td>{$nicklist['v'][$i]}</td></tr>"; 
			}
		
		for ( $i=0; $i<sizeof($nicklist['r']); $i++) 
			{ 
				echo "<tr><td>&nbsp;</td><td>{$nicklist['r'][$i]}</td></tr>"; 
			}
		echo "</table>";
	}
else
	{
		echo "No Info Avaible";
	}
?>