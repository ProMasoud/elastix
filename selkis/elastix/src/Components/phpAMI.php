<?php

namespace Selkis\Elastix\Components;
use Exception;

/**
 * 
 * phpAMI
 * 
 * phpAstersikManagerInterface
 * 
 * Pagina Web: http://code.google.com/p/phpami/
 * 
 * Copyright (c) 2012 Romulo Rodriguez (rodriguezrjrr@gmail.com)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 

 * 
 * @author Romulo Rodriguez
 * @version 1.0 -  Mon Feb 29 2012
 * @copyright Copyright (c) 2012 Romulo Rodriguez (rodriguezrjrr@gmail.com)
 */

 /**
  * phpAMI-(phpAstersikManaherInterface)
  * 
  * Esta clase ha sido creado con todas los AMI Actions de la documentacion de astersik 1.8
  * @link https://wiki.asterisk.org/wiki/display/AST/AMI+Actions
  * 
  */
class phpAMI{
	/**
	 * Host a conectar
	 * @var string
	 * @access private
	 */
	private $SERVER;
	/**
	 * Puerto a conectar
	 * @var string
	 * @access private
	 */
	private $PORT;
	/**
	 * Usuario a conectar
	 * @var string
	 * @access private
	 */
	private $USER;
	/**
	 * Clave de usuario
	 * @var string
	 * @access private
	 */
	private $SECRET;
	/**
	 * Socket
	 * @var sock
	 * @access private
	 */
	private $SOCK;
	/**
	 * Privilegios del usuario
	 * @var string
	 * @access private
	 */
	private $PRIVILEGE;
	
	/**
	 * Constructor
	 * @param string $username Nombre de usuario a conectar
	 * @param string $password Clave del usuario
	 * @param string $host Host a conertar
	 * @param string $port Puerto a conectar 
	 */
	function __construct($username,$password,$host="192.168.10.118",$port="5038"){
		if($username != '' and $password != ''){
			$this->USER=$username;
			$this->SECRET=$password;
			$this->SERVER=$host;
			$this->PORT=$port;
		}else{
			$this->__destruct();
			return false;
		}
	}
	
	/**
	 * Comienza la Conexion y envia "ManagerAction_Login"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Login
	 * @return array 
	 * Autentificacion aceptada
	 * Array
	 * (
	 * 		[0] => Asterisk Call Manager/1.1
	 * 		[Response] => Success
	 * 		[Message] => Authentication accepted)
	 * 
	 * Autentificacion Fallida
	 * Array
	 * (
	 * 		[0] => Asterisk Call Manager/1.1
	 * 		[Response] => Error
	 * 		[Message] => Authentication failed
	 * )
	 */
	function login(){
		if($this->openSock()){
			$this->send("Login",array("Username"=>$this->USER,"Secret"=>$this->SECRET));
			$response=$this->readEnd();
			if($response["Response"]=="Success"){
				$response2=$this->readEnd();
				$this->PRIVILEGE=$response2["Privilege"];
				return $response;
			}elseif($response["Response"]=="Error"){
				 return $response;
			}
		}else{
			return false;
		}
	}
	/**
	 * Envia "ManagerAction_Logoff" y cierra el Socket
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Logoff
	 * @return array 
	 * Array
	 * (
	 * 		[Response] => Goodbye
	 * )
	 */	
	function logoff(){
		$return=$this->responceInfo("Logoff",true);
		$this->closeSock();
		return $return;
	}
	/**
	 * Envia "ManagerAction_Ping"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Ping
	 * @return array ver responceInfo()
	 */
	function ping(){
		return $this->responceInfo("Ping",true);
	}
	/**
	 * Envia "ManagerAction_Challenge"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Challenge
	 * @param string $authType (default: MD5) 
	 */
	function challenge($authType="MD5"){
		return $this->responceInfo("Challenge",true,array("AuthType"=>$authType));
	}
	/**
	 * Envia "ManagerAction_AbsoluteTimeout"
	 * Cuelga el canal despues de cierto tiempo
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_AbsoluteTimeout
	 * @param string $channel Canal activo
	 * @param int $timeOut Tiempo de espera en segundos
	 * @return array ver eventSimple()
	 */
	function absoluteTimeout($channel,$timeOut){
		return $this->eventSimple("AbsoluteTimeout",array("Channel"=>$channel,"Timeout"=>$timeOut));
	}
	/**
	 * Envia "ManagerAction_Command"
	 * Ejecuta un Comando CLI de Astersk 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Command
	 * @param string $command Comando CLI 
	 * @return array ver responceInfo()
	 */
	function command($command){
		return $this->responceInfo("Command",true,array("Command"=>$command));
	}
	/**
	 * Envia "ManagerAction_Events" 
	 * Habilita o deshabilita el envio de eventos al cliente 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Events
	 * @param string $eventMask (on/off/system,call,log,...)
	 * @return array ver eventSimple()
	 */
	function events($eventMask){
		return $this->eventSimple("Events",array("EventMask"=>$eventMask),true);
	}
	/**
	 * Envia "ManagerAction_ModuleCheck"
	 * Chequea si un modulo de asteris esta cargado
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ModuleCheck
	 * @param string $mod Modulo de astersik sin la extension
	 * @return array ver responceInfo() agreva vercion de revicion del modulo
	 */
	function moduleCheck($mod){
		$response=$this->responceInfo("ModuleCheck",true,array("Module"=>$mod));
		if($response["Response"]=="Success"){
			return $response;
		}else{
			$mes=trim(fgets($this->SOCK, 1024));
			$did=stripos($mes,":");
			$response[substr($mes,0,$did)]=substr($mes,$did+2);
			return $response;
		}
	}
	/**
	 * Envia "ManagerAction_ModuleLoad"
	 * Carga,descarga o recarga modulos de asterisk en el systema.
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ModuleLoad
	 * @param string $loadType (load/unload/reload) Lo que se va a hacer en el modulo
	 * @param string $mod Modulo de astersik sin la extension (default: todos los modulos / all modules)
	 * @return array ver eventSimple()
	 */
	function moduleLoad($loadType,$mod=null){
		if(!is_null($mod)){
			$arg["Module"]=$mod;
		}
		$arg["LoadType"]=$loadType;
		return $this->eventSimple("ModuleLoad",$arg);
	}
	/**
	 * Envia "ManagerAction_Originate"
	 * Origina una llamada
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Originate
	 * @param string $channel Canal a llamar
	 * @param string $exten  Extension a usar
	 * @param string $context Contexto de la extension 
	 * @param string $priority Prioridad de la Extension (Default: 1)
	 * @param string $callerid Identificador de llamada (Default: null)
	 * @param string $timeout Tiempo de espera para que la llamada sea contestada (en ms)  (default: Default timeout for astersik)
	 * @param string $account Codigo de cuenta  (Default: null)
	 * @param string $codecs Separado por comas los codecs a utilizar para esta llamada 
	 * @param string $variable Variables para el canal (Default: null)
	 * @param string $aplication Aplicacion a ejecutar  (Default: null)
	 * @param string $data Datos requeridos para la aplicacion 
	 * @param string $async Colocar true para una originacion rapida 
	 * @return array ver eventSimple()
	 */
	function originate($channel,$exten,$context,$priority=1,$callerid=null,$timeout=null,$account=null,$codecs=null,$variable=null,$aplication=null,$data=null,$async=null){
		$arg=array("Channel"=>$channel,"Exten"=>$exten,"Context"=>$context,"Priority"=>$priority);
		$argOthes=array("aplication"=>$aplication,"Data"=>$data,"Timeout"=>$timeout,"Callerid"=>$callerid,"Variable"=>$variable,"Account"=>$account,"Async"=>$async,"Codecs"=>$codecs);
		foreach ($argOthes as $key => $value) {
			if(!is_null($value)){
				$arg[$key]=$value;
			}
		}
		return $this->eventSimple("Originate",$arg);
	}
	/**
	 * Envia "ManagerAction_Atxfer" 
	 * Trasferencia Antendida
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Atxfer
	 * @param string $channel Canal
	 * @param string $context Contexto 
	 * @param string $exten Extencion
	 * @param stirng $priority Prioridad de extentncion
	 * @return array ver eventSimple()
	 */
	function aTxfer($channel,$context,$exten,$priority){
		return $this->eventSimple("Atxfer",array("Channel"=>$channel,"Exten"=>$exten,"Context"=>$context,"Priority"=>$priority));
	}
	/**
	 * Envia "ManagerAction_Bridge"
	 * Une dos canales
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Bridge
	 * @param string $channel1 primer canal
	 * @param string $channel2 segundo canal
	 * @param string $tone "yes" para que suene un tono al canal 2 antes de unir (default: "no")
	 * @return array ver eventSimple()
	 */
	function bridge($channel1,$channel2,$tone="no"){
		return $this->eventSimple("Bridge",array("Channel1"=>$channel1,"Channel2"=>$channel2,"tone"=>$tone));
	}
	/**
	 * Envia "ManagerAction_SetVar" 
	 * Crea una variable Global o de canal 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Setvar
	 * @param string $var Nombre de la variable .
	 * @param string $value Valor para la  variable 
	 * @param string $channel canal  (Default: null)
	 * @return array ver eventSimple()
	 */
	function setVar($var,$value,$channel=null){
		if(!is_null($channel)){
			$arg["Channel"]=$channel;
		}
		$arg["Variable"]=$var;
		$arg["Value"]=$value;
		return $this->eventSimple("SetVar",$arg);
	}
	/**
	 * Envia "ManagerAction_Getvar"
	 * Obtiene el valor de una variable Global o de canal 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Getvar
	 * @param string $var Nombre de la variable 
	 * @param string $channel canal  (Default: null)
	 * @return array ver responceInfo()
	 */
	function getVar($var,$channel=null){
		$arg["Variable"]=$var;
		if(!is_null($channel)){
			$arg["Channel"]=$channel;
		}
		return $this->responceInfo("Getvar",true,$arg);
	}
	/**
	 * Envia "ManagerAction_Hangup"
	 * Cuelga un canal
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Hangup
	 * @param string $channel canal
	 * @param string $cause Numeric hangup cause (Default: null)
	 * @return array ver eventSimple()
	 */
	function hangup($channel,$cause=null){
		$arg["Channel"]=$channel;
		if(!is_null($cause)){
			$arg["Cause"]=$cause;
		}
		return $this->eventSimple("Hangup",$arg);
	}
	/**
	 * Envia "ManagerAction_PlayDTMF"
	 * Reproduce un DTMF en un canal especifico
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_PlayDTMF
	 * @param string $channel canal 
	 * @param string $digit Digito DTMF
	 * @return array ver eventSimple()
	 */
	function playDTMF($channel,$digit){
		$arg["Channel"]=$channel;
		$arg["Digit"]=$digit;
		return $this->eventSimple("PlayDTMF",$arg);
	}
	/**
	 * Envia "ManagerAction_Redirect"
	 * Redirecciona una llamda 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Redirect
	 * @param string $channel canal 
	 * @param string $context Contexto de extension 
	 * @param string $exten Extension 
	 * @param string $priority Prioridad 
	 * @param string $extraChannel canal extra (Default: null)
	 * @param string $extraContext Contexto de extension (Default: null)
	 * @param string $extraExten (Default: null)
	 * @param string $extraPriority (Default: null)
	 * @return array ver eventSimple()
	 */
	function redirect($channel,$context,$exten,$priority,$extraChannel=null,$extraContext=null,$extraExten=null,$extraPriority=null){
		$arg["Channel"]=$channel;
		if(!is_null($extraChannel)){
			$arg["ExtraChannel"]=$extraChannel;
		}
		$arg["Exten"]=$exten;
		if(!is_null($extraExten)){
			$arg["ExtraExten"]=$extraExten;
		}
		$arg["Context"]=$context;
		if(!is_null($extraContext)){
			$arg["ExtraContext"]=$extraContext;
		}
		$arg["Priority"]=$priority;
		if(!is_null($extraPriority)){
			$arg["ExtraPriority"]=$extraPriority;
		}
		return $this->eventSimple("Redirect",$arg);
	}
	/**
	 * Envia "ManagerAction_SendText"
	 * Envia un mensaje de texto para el canal
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SendText
	 * @param string $channel canal
	 * @param string $message Mensaje a enviar
	 * @return array ver eventSimple()
	 */
	function sendText($channel,$message){
		$arg["Channel"]=$channel;
		$arg["Message"]=$message;
		return $this->eventSimple("SendText",$arg);
	}
	/**
	 * Envia "ManagerAction_JabberSend"
	 * Envia un mensaje a cliente Jabber
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_JabberSend
	 * @param string $jabber  Cliente o usuario Astersik cata conectar a JABBER
	 * @param string $jid - XMPP/Jabber JID (Name)
	 * @param string $message  Mensaje
	 * @return array ver eventSimple()
	 */
	function jabberSend($jabber,$jid,$message){
		return $this->eventSimple("JabberSend",array("Jabber"=>$jabber,"JID"=>$jid,"Message"=>$message));
	}
	/**
	 * Envia "ManagerAction_AGI"
	 * Envia Un comando AGI
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_AGI
	 * @param string $channel Canal
	 * @param string $command Comando agi
	 * @param string $commandID
	 * @return array ver eventSimple()
	 */
	function agi($channel,$command,$commandID=null){
		$arg["Channel"]=$channel;
		$arg["Command"]=$command;
		if(!is_null($commandID)){
			$arg["CommandID"]=$commandID;
		}
		return $this->eventSimple("AGI",$arg);
	}
	/**
	 * Envia "ManagerAction_AOCMessage"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_AOCMessage
	 * @param string $channel Canal
	 * @param string $msgType tipo de mensaje ("D" para AOC-D o "E" para AOC-E)
	 * @param string $chargeType Define que tipo de carga de este mensaje representa. (NA, FREE, Currency, Unit)
	 * @param string $channelPrefix Prefijo del canal
	 * @param array $unti array(0=>array("UnitAmount"=>"value","UnitType"=>"Value"),N=>array("UnitAmount"=>"value","UnitType"=>"Value"))
	 * @param string $currencyName Especifica el nombre del currency. (10 caracteres)
	 * @param string $currencyAmount Especifica la cantidad unidad de carga como un entero positivo
	 * @param string $currencyMultiplier Especifica el multiplicador del Currency
	 * @param string $totalType Define que tipo de AOC-D total se representa
	 * @param string $aocBillingId Representa un identificador de facturacion asociado con un AOC-D o mensaje de AOC-E
	 * @param string $chargingAssociationId  AssociationId 
	 * @param string $chargingAssociationNumber AssociationNumber
	 * @param string $chargingAssociationPlan AssociationPlan
	 */
	function aocMessage($channel,$msgType,$chargeType,$channelPrefix=null,$unti=null,$currencyName=null,$currencyAmount=null,$currencyMultiplier=null,$totalType=null,$aocBillingId=null,$chargingAssociationId=null,$chargingAssociationNumber=null,$chargingAssociationPlan=null){
		$arg["Channel"]=$channel;
		$arg["MsgType"]=$msgType;
		$arg["ChargeType"]=$chargeType;
		if(!is_null($channelPrefix)){
			$arg["ChannelPrefix"]=$channelPrefix;
		}
		if(!is_null($unti)){
			if(is_array($unti)){
				do{
					$arg["UnitAmount(".key($unti).")"]=$unti[key($unti)]["UnitAmount"];
					$arg["UnitAmount(".key($unti).")"]=$unti[key($unti)]["UnitType"];
				}while(next($unti));
			}
		}
		if(!is_null($currencyName)){
			$arg["CurrencyName"]=$currencyName;
		}
		if(!is_null($currencyAmount)){
			$arg["CurrencyAmount"]=$currencyAmount;
		}
		if(!is_null($currencyMultiplier)){
			$arg["CurrencyMultiplier"]=$currencyMultiplier;
		}
		if(!is_null($totalType)){
			$arg["TotalType"]=$totalType;
		}
		if(!is_null($aocBillingId)){
			$arg["AOCBillingId"]=$aocBillingId;
		}
		if(!is_null($chargingAssociationId)){
			$arg["ChargingAssociationId"]=$chargingAssociationId;
		}
		if(!is_null($chargingAssociationNumber)){
			$arg["ChargingAssociationNumber"]=$chargingAssociationNumber;
		}
		if(!is_null($chargingAssociationPlan)){
			$arg["ChargingAssociationPlan"]=$chargingAssociationPlan;
		}
		return $this->eventSimple("AOCMessage",$arg);
	}
	
	/**
	 * Envia "ManagerAction_ShowDialPlan"
	 * Lista Contextos, Extensiones, y prioridades
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ShowDialPlan
	 * @param string $context Contexto de extension (Default: null)
	 * @param string $exten Extension (Default: null)
	 * @return array ver listEvent()
	 */
	function showDialPlan($context=null,$exten=null){
		if(!is_null($exten)){
			$action["arg"]["Exten"]=$exten;
		}
		if(!is_null($context)){
			$action["arg"]["Context"]=$context;
		}
		if(isset($action)){
			$action["action"]="ShowDialPlan";
		}else{
			$action="ShowDialPlan";
		}
		$result=$this->listEvent($action,array("Context","Extension","Priority","IncludeContext"));
		return $result;
	}
	/**
	 * Envia "ManagerAction_Status" 
	 * Lista estados de canales 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Status
	 * @param string $channel Canal 
	 * @param string $variables Lista de Variables a incluir separadas por coma  (Default: null)
	 * @return array Ver listEvent()
	 */
	function status($channel,$variables=NULL){
		if(!is_null($variables)){
			$action["arg"]["Variables"]=$variables;
		}
		if(isset($action)){
			$action["action"]="Status";
		}else{
			$action="Status";
		}
		return $this->listEvent($action,"Channel",true,"Items");
	}
	/**
	 * Envia "ManagerAction_ExtensionState"
	 * Chequea estado de una extension
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ExtensionState
	 * @param string $context Contexto de extension
	 * @param string $exten Extension
	 * @return array ver responceInfo()
	 */
	function extensionState($exten,$context){
		return $this->responceInfo("ExtensionState",false,array("Exten"=>$exten,"Context"=>$context));
	}
	/**
	 * Envia "ManagerAction_ListCommands"
	 * Lista de Comandos
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ExtensionState
	 * @return array Lista de Comanods
	 * Array
	 * (
	 * 		[Response] => Success
	 * 		[list] => Array
	 * 		(
	 *      [0] => Array
	 *          (
	 *              [command] => WaitEvent
	 *              [des] => Wait for an event to occur.  
	 *              [priv] => <none>
	 *          )
	 * )
	 */	
	function listCommands(){
		$this->send("ListCommands");
		$response=$this->read("Response");
		if($response["Response"]=="Success"){
			$response["list"]=array();
			do{
				$buf=trim(fgets($this->SOCK, 1024));
				$pDiv=strpos($buf,':');
				$pDiv2=strpos($buf,'  (');
				array_push($response["list"],array("command"=>substr($buf,0,$pDiv),"des"=>substr($buf,$pDiv+2,$pDiv2-$pDiv),"priv"=>substr($buf,$pDiv2+9,-1)));
			}while($buf!="");
			return $response;
		}else{
			return array_merge($response,$this->read("Message"));
		}
	}
	/**
	 * Envia "ManagerAction_GetConfig"
	 * Obtiene un archivo de configuracion
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_GetConfig
	 * @param string $file Archivo de configuracion (ver astersik.conf)
	 * @param string $category Caregoria (default: null)
	 * @return array 
	 * Array
	 * (
	 * 		[Response] => Success
	 * 		[list] => Array
	 * 		(
	 * 			[category] => Array
	 * 						(
	 * 						[Variable] => Value
	 * 						[Variable] => Value
                )
	 * )
	 */	
	function getConfig($file,$category=null){
		$arg["Filename"]=$file;
		if(!is_null($category)){
			$arg["Category"]=$category;
		}
		$this->send("GetConfig",$arg);
		$response=$this->read("Response");
		if($response["Response"]=="Success"){
			$response["list"]=array();
			$lastCN="";
			do{
				$buf=trim(fgets($this->SOCK, 1024));
				$pDiv=strpos($buf,':');
				if(substr($buf,0,8) == "Category"){
					$lastCN=substr($buf,$pDiv+2);
					$response["list"][$lastCN]=array();
				}elseif(substr($buf,0,4) == "Line"){
					$varl=substr($buf,$pDiv+2);
					$pDiv2=strpos($varl, '=');
					$response["list"][$lastCN][substr($varl,0,$pDiv2)]=substr($varl,$pDiv2+1);
				}
			}while($buf!="");
			return $response;
		}else{
			return array_merge($response,$this->read("Message"));
		}
	}
	/**
	 * Envia "ManagerAction_CreateConfig"
	 * Crea un Archivo de Configuracion
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_CreateConfig
	 * @param string $file nombre de archivo
	 * @return array ver eventSimple()
	 */
	function createConfig($file){
		return $this->eventSimple("CreateConfig",array("Filename"=>$file));
	}
	/**
	 * Envia "ManagerAction_UpdateConfig"
	 * Actualizar una configuracion
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_UpdateConfig
	 * @param string $src Archivo a leer 
	 * @param string $dst Archivo a escribir
	 * @param array $actions
	 * array(
	 *		0 => array("action"=>"...","cat"=>"...","var"=>"...","value"=>"...","match"=>"...","line"=>"..."),
	 *		1 => array("action"=>"...","cat"=>"...","var"=>"...","value"=>"...","match"=>"...","line"=>"..."),
	 *		xxxxxx => array("action"=>"...","cat"=>"...","var"=>"...","value"=>"...","match"=>"...","line"=>"...")
	 * );
	 * action:
	 * -NewCat
	 * -RenameCat
	 * -DelCat
	 * -EmptyCat
	 * -Update
	 * -Delete
	 * -Append
	 * -Insert
	 * @param string $reload  Si recarga o no se puede colocar el numbre del modulo.
	 * @return array Ver eventSimple()
	 */
	function updateConfig($src,$dst,$actions=null,$reload=null){
		$arg["SrcFilename"]=$src;
		$arg["DstFilename"]=$dst;
		if(!is_null($reload)){
			$arg["Reload"]=$reload;
		}
		if(!is_null($actions) and is_array($actions)){
			do{
				$adds0=6-strlen(key($actions));
				$actionID="";
				for($a=0;$a<$adds0;$a++){
					$actionID.="0";
				}
				$actionID.=key($actions);
				$arg["Action-".$actionID]=$actions[key($actions)]["action"];
				if(isset($actions[key($actions)]["cat"])){
					$arg["Cat-".$actionID]=$actions[key($actions)]["cat"];
				}
				if(isset($actions[key($actions)]["var"])){
					$arg["Var-".$actionID]=$actions[key($actions)]["var"];
				}
				if(isset($actions[key($actions)]["value"])){
					$arg["Value-".$actionID]=$actions[key($actions)]["value"];
				}
				if(isset($actions[key($actions)]["match"])){
					$arg["Match-".$actionID]=$actions[key($actions)]["match"];
				}
				if(isset($actions[key($actions)]["line"])){
					$arg["Line-".$actionID]=$actions[key($actions)]["line"];
				}
			}while(next($actions));
		}
		return $return=$this->eventSimple("UpdateConfig",$arg,true);
	}
	/**
	 * Envia "ManagerAction_GetConfigJSON"
	 * Obtiene un archivo de configuracion en formato JSON 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_GetConfigJSON
	 * @param string $file Archivo de configuracion
	 * @return array ver responceInfo()
	 */
	function getConfigJSON($file){
		return $this->responceInfo("GetConfigJSON",true,array("Filename"=>$file));
	}
	/**
	 * Envia "ManagerAction_ListCategories" 
	 * Lista las categorias en un archivo de configuracion
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ListCategories
	 * @access public
	 * @param string Archivo
	 * @return array 
	 * Array
	 * (
     * 		[Response] => Success
     * 		[list] => Array
	 * 		(
     *       	[0] => general
     *       	[1] => eltercera
	 * 		)
	 * )
	 */
	function listCategories($file){
		$arg["Filename"]=$file;
		$this->send("ListCategories",$arg);
		$response=$this->read("Response");
		if($response["Response"]=="Success"){
			$response["list"]=array();
			do{
				$buf=trim(fgets($this->SOCK, 1024));
				$pDiv=strpos($buf,':');
				if(substr($buf,0,8) == "Category"){
					array_push($response["list"],substr($buf,$pDiv+2));
				}
			}while($buf!="");
			return $response;
		}else{
			return array_merge($response,$this->read("Message"));
		}
	}
	/**
	 * Envia "ManagerAction_ParkedCalls" 
	 * Lista llamadas estacionadas
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ParkedCalls
	 * @access public
	 * @return array Ver listEvent()
	 */
	function parkedCalls(){
		return $this->listEvent("ParkedCalls",array("Parkinglot","Exten"),true,"Total");
	}
	/**
	 * Envia "ManagerAction_park"
	 * Estaciona un canal
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Park
	 * @access public
	 * @param string $channel canal a estacionar
	 * @param string $channel2 Canal de terorno si timeout expira
	 * @param string $parkinglot Numero de estacionamiento
	 * @param string $timeout Tiempo para retorno
	 * @return array Ver eventSimple() 
	 */
	function park($channel,$channel2,$parkinglot,$timeout=null){
		$arg["Channel"]=$channel;
		$arg["Channel2"]=$channel2;
		if(!is_null($timeout)){
			$arg["Timeout"]=$timeout;
		}
		return $this->eventSimple("Park",$arg);
	}
	/**
	 * Envia "ManagerAction_VoicemailUsersList" 
	 * Lista todos los VM
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_VoicemailUsersList
	 * @access public
	 * @return array Ver listEvent()
	 */
	function voicemailUsersList(){
		return $this->listEvent("VoicemailUsersList",array("VMContext","VoiceMailbox"));
	}
	/**
	 * Envia "ManagerAction_MailboxCount"
	 * Chequea la cuenta de VM, Todos los mensajes
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_MailboxCount
	 * @param string $mailbox usuario VM
	 * @return array Ver responceInfo()
	 */
	function mailboxCount($mailbox){
		return $this->responceInfo("MailboxCount",false,array("Mailbox"=>$mailbox));
	}
	/**
	 * Envia "ManagerAction_MailboxStatus"
	 * Chequea la cuenta de VM, mensajes en espera
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_MailboxStatus
	 * @param string $mailbox usuario VM
	 * @return array Ver responceInfo()
	 */
	function mailboxStatus($mailbox){
		return $this->responceInfo("MailboxStatus",false,array("Mailbox"=>$mailbox));
	}
	/**
	 * Envia "ManagerAction_CoreShowChannels" 
	 * Lista todos los canales activos
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_CoreShowChannels
	 * @access public
	 * @return array Ver listEvent()
	 */
	function coreShowChannels(){
		return $this->listEvent("CoreShowChannels","Channel",true);
	}
	/**
	 * Envia "ManagerAction_CoreStatus"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_CoreStatus
	 * @return array Ver responceInfo()
	 */
	function coreStatus(){
		return $this->responceInfo("CoreStatus",true);
	}
	/**
	 * Envia "ManagerAction_CoreSettings"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_CoreSettings
	 * @return array Ver responceInfo()
	 */
	function coreSettings(){
		return $this->responceInfo("CoreSettings",true);
	}
	/**
	 * Envia "ManagerAction_SIPpeers" 
	 * Lista todos los Peer SIP
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SIPpeers
	 * @access public
	 * @return array Ver listEvent()
	 */
	function sipPeers(){
		return $this->listEvent("SIPpeers","ObjectName");
	}
	/**
	 * Envia "ManagerAction_SIPshowpeer"
	 * obtiene informacion sobre un peer sip
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SIPshowpeer
	 * @param $string $peer peerID
	 * @return array Ver responceInfo()
	 */
	function sipShowPeer($peer){
		return $this->responceInfo("SIPshowpeer",true,array("Peer"=>$peer));
	}
	/**
	 * Envia "ManagerAction_SIPshowregistry" 
	 * Lista los registro SIP
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SIPshowregistry
	 * @access public
	 * @return array Ver listEvent()
	 */
	function sipShowRegistry(){
		return $this->listEvent("SIPshowregistry","Host",true);
	}
	/*function sipQualifyPeer($peer){
		$this->send("SIPqualifypeer",array("Peer"=>$peer));
		$this->read("Response");
	}*/
	/**
	 * Envia "ManagerAction_DAHDIShowChannels" 
	 * obtiene el estado de canales DAHDI
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DAHDIShowChannels
	 * @access public
	 * @param string $channel canal DAHDI (default: todos)
	 * @return array Ver listEvent()
	 */
	function dahdiShowChannels($channel=null){
		if(is_null($channel)){
			return $this->listEvent("DAHDIShowChannels","DAHDIChannel",true,"Items");
		}else{
			$action=array("action"=>"DAHDIShowChannels","arg"=>array("DAHDIChannel"=>$channel));
			return $this->listEvent($action,"DAHDIChannel",true,"Items");
		}
	}
	/**
	 * Envia "ManagerAction_DAHDIDNDon"
	 * Enciende el DND en un canal DAHDI
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DAHDIDNDon
	 * @access public
	 * @param string $channel canal DAHDI
	 * @return array Ver eventSimple() 
	 */
	function dahdiDNDOn($channel){
		return $this->eventSimple("DAHDIDNDon",array("DAHDIChannel"=>$channel));
	}
	/**
	 * Envia "ManagerAction_DAHDIDNDoff"
	 * Apaga el DND en un canal DAHDI
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DAHDIDNDoff
	 * @access public
	 * @param string $channel canal DAHDI
	 * @return array Ver eventSimple() 
	 */
	function dahdiDNDOff($channel){
		return $this->eventSimple("DAHDIDNDoff",array("DAHDIChannel"=>$channel));
	}
	/**
	 * Envia "ManagerAction_DAHDIRestart"
	 * Reinicia todos los canales DAHDI
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DAHDIRestart
	 * @access public
	 * @return array Ver eventSimple() 
	 */
	function dahdiRestart(){
		return $this->eventSimple("DAHDIRestart");
	}
	/**
	 * Envia "ManagerAction_DAHDIDialOffhook"
	 * Marca un numero si el canal DAHDI se encuentra descolgado
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DAHDIDialOffhook
	 * @access public
	 * @param string $channel canal DAHDI
	 * @param string $number Numero a marcar
	 * @return array Ver eventSimple() 
	 */
	function dahdiDialOffhook($channel,$number){
		return $this->eventSimple("DAHDIDialOffhook",array("DAHDIChannel"=>$channel,"Number"=>$number));
	}
	/**
	 * Envia "ManagerAction_DAHDIHangup"
	 * cuelga un canal DAHDI
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DAHDIHangup
	 * @access public
	 * @param string $channel canal DAHDI
	 * @return array Ver eventSimple() 
	 */
	function dahdiHangup($channel){
		return $this->eventSimple("DAHDIHangup",array("DAHDIChannel"=>$channel));
	}
	/**
	 * Envia "ManagerAction_DAHDITransfe"
	 * Transfiere un canal DAHDI
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DAHDITransfer
	 * @access public
	 * @param string $channel canal DAHDI
	 * @return array Ver eventSimple() 
	 */
	function dahdiTransfer($channel){
		return $this->eventSimple("DAHDITransfer",array("DAHDIChannel"=>$channel));
	}
	/**
	 * Envia "ManagerAction_IAXpeers" 
	 * lista de IAX peer
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_IAXpeers
	 * @access public
	 * @return  array Ver listEvent()
	 */
	function iaxPeers(){
		return $this->listEvent("IAXpeers","ObjectName");
	}
	/**
	 * Envia "ManagerAction_IAXpeerlist" 
	 * lista de IAX peer
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_IAXpeerlist
	 * @access public
	 * @return array Ver listEvent()
	 */
	function iaxPeerList(){
		return $this->listEvent("IAXpeerlist","ObjectName",true);
	}
	/**
	 * Envia "ManagerAction_IAXnetstats" 
	 * Obtiene lineas de netstat iax
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_IAXnetstats
	 * @access public
	 * @return array Ver listEvent()
	 */
	function iaxNetstats(){
		$this->send("IAXnetstats");
		$bufer=array();
		do{
			$buf=trim(fgets($this->SOCK, 1024));
			if($buf!=""){
				array_push($bufer,$buf);
			}
		}while($buf!="");
		return $bufer;
	}
	/**
	 * Envia "ManagerAction_IAXregistry" 
	 * Ver los registros iax
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_IAXregistry
	 * @access public
	 * @return array Ver listEvent()
	 */
	function iaxRegistry(){
		return $this->listEvent("IAXregistry","Host",true);
	}
	/**
	 * Envia "ManagerAction_Agents" 
	 * Lista a todos los agentes y su estado
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Agents
	 * @access public
	 * @return array Ver listEvent()
	 */
	function agents(){
		return $this->listEvent("Agents","Agent");
	}
	/**
	 * Envia "ManagerAction_AgentLogoff"
	 * Hace logoff aun agente
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_AgentLogoff
	 * @access public
	 * @param string $agent ID del Agente
	 * @param string $soft True para esperar que cuelge si extente una llamada
	 * @return array Ver eventSimple() 
	 */
	function agentLogoff($agent,$soft=null){
		$arg["Agent"]=$agent;
		if(!is_null($soft)){
			$arg["Soft"]=true;
		}
		return $this->eventSimple("AgentLogoff", $arg);
	}
	/**
	 * Envia "ManagerAction_Queues"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Queues
	 * @access public
	 * @return Array
	 * (
     * 		[0] => queue1 has 0 calls (max unlimited) in 'ringall' strategy (0s holdtime, 16s talktime), W:0, C:3, A:2, SL:33.3% within 0s
     * 		[1] => Members:
     * 		[2] => Agent/1001 (Unavailable) has taken 3 calls (last was 221365 secs ago)
     * 		[3] => No Callers
     * 		[4] => queue2 has 0 calls (max unlimited) in 'ringall' strategy (0s holdtime, 0s talktime), W:0, C:0, A:0, SL:0.0% within 0s
     * 		[5] => Members:
     * 		[6] => Agent/1001 (Unavailable) has taken no calls yet
     * 		[7] => No Callers
	 * )
	 */
	function queues(){
		$this->send("Queues");
		$espa=0;
		$buffer=array();
		while($espa<2){
			$buf=trim(fgets($this->SOCK, 1024));
			if($buf==""){
				$espa++;
			}else{
				$espa=0;
				array_push($buffer,$buf);
			}
		}
		return $buffer;
	}
	/**
	 * Envia "ManagerAction_Member"
	 * Obtiene indormacion de colas y agentes
	 * @access public
	 * @param string $queue cola (default: todos)
	 * @param string $member Miembro (default: todos)
	 * @return Array
	 * (
     *		[Response] => Success
     *		[Message] => Queue status will follow
     *		[list] => Array
     *		(
     *			[queue2] => Array
     *			(
     *               [Event] => QueueParams
     *               [Queue] => queue2
	 *				......
     *               [mNumber] => 1
     *               [members] => Array
     *                   (
     *                       [Agent/1001] => Array
     *                           (
     *                               [Event] => QueueMember
     *                               [Queue] => queue2
	 *								.....
     *                           )
     *                   )
     *           )
     *		)
     *		[nQueues] => 1
	 * )
	 */
	function queueStatus($queue=null,$member=null){
		if(!is_null($queue)){
			$arg["Queue"]=$queue;
		}
		if(!is_null($member)){
			$arg["Member"]=$member;
		}
		if(isset($arg)){
			$this->send("queueStatus",$arg);
		}else{
			$this->send("queueStatus");
		}
		$responce=$this->readEnd();
		if($responce["Response"]=="Success"){
			$reg=$responce;
			$reg["list"]=array();
			$q=0;
			do{
				$responce=$this->readEnd();
				if($responce["Event"]=="QueueStatusComplete"){
					break;
				}elseif($responce["Event"]=="QueueParams"){
					$q++;
					$reg["list"][$responce["Queue"]]=$responce;
					$reg["list"][$responce["Queue"]]["mNumber"]=0;
				}elseif($responce["Event"]=="QueueMember"){
					$reg["list"][$responce["Queue"]]["mNumber"]++;
					$reg["list"][$responce["Queue"]]["members"][$responce["Name"]]=$responce;
				}			
			}while($responce["Event"]!="QueueStatusComplete");
			$reg["nQueues"]=$q;
			return $reg;
		}else{
			return $responce;
		}
	}
	/**
	 * Envia "ManagerAction_QueueSummary" 
	 * Sumari de colas 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueueSummary
	 * @access public
	 * @param string $queue cola (default: todos)
	 * @return array Ver listEvent()
	 */
	function queueSummary($queue=null){
		if(!is_null($queue)){
			$arg["arg"]["Queue"]=$queue;
			$arg["action"]="QueueSummary";
		}else{
			$arg="QueueSummary";
		}
		return $this->listEvent($arg,"Queue");
	}
	/**
	 * Envia "ManagerAction_QueueReset"
	 * reinicia las estadisticas de las colas
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueueReset
	 * @param string $queue cola (default: todos)
	 * @return array Ver eventSimple()
	 */
	function queueReset($queue=null){
		if(!is_null($queue)){
			$arg["arg"]=$queue;
			return $this->eventSimple("QueueReset",$arg);
		}else{
			return $this->eventSimple("QueueReset");
		}
	}
	/**
	 * envia "ManagerAction_QueueAdd"
	 * Agraga una interface a una cola
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueueAdd
	 * @param string $queue Cola
	 * @param string $interface Interface (SIP/testphone,DAHDI/1...)
	 * @param string $penalty Priorida del usuario o interface
	 * @param string $paused Si el usuario podra pausar
	 * @param string $memberName Nombre del miembro
	 * @param string $stateInterface
	 * @return array ver eventSimple()
	 */
	function queueAdd($queue,$interface,$penalty=null,$paused=null,$memberName=null,$stateInterface=null){
		$arg["Queue"]=$queue;
		$arg["Interface"]=$interface;
		if(!is_null($penalty)){
			$arg["Penalty"]=$penalty;
		}
		if(!is_null($paused)){
			$arg["Paused"]=$paused;
		}
		if(!is_null($memberName)){
			$arg["MemberName"]=$memberName;
		}
		if(!is_null($stateInterface)){
			$arg["StateInterface"]=$stateInterface;
		}
		return $this->eventSimple("QueueAdd",$arg);
	}
	/**
	 * envia "ManagerAction_QueueRemove"
	 * Elimina una interface a una cola
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueueRemove
	 * @param string $queue Cola
	 * @param string $interface Interface (SIP/testphone,DAHDI/1...)
	 * @return array ver eventSimple()
	 */
	function queueRemove($queue,$interface){
		$arg["Queue"]=$queue;
		$arg["Interface"]=$interface;
		return $this->eventSimple("QueueRemove",$arg);
	}
	/**
	 * Envia "ManagerAction_QueueReload"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueueReload
	 * @param string $queue Cola
	 * @param string $members (yes or no)
	 * @param string $rules (yes or no)
	 * @param string $parameters (yes or no)
	 * @return array ver eventSimple()
	 */
	function queueReload($queue=null,$members=null,$rules=null,$parameters=null){
		if(!is_null($queue)){
			$arg["Queue"]=$queue;
		}
		if(!is_null($members)){
			$arg["Members"]=$members;
		}
		if(!is_null($rules)){
			$arg["Rules"]=$rules;
		}
		if(!is_null($parameters)){
			$arg["Parameters"]=$parameters;
		}
		if(isset($arg)){
			return $this->eventSimple("QueueReload",$arg);
		}else{
			return $this->eventSimple("QueueReload");
		}
	}
	/**
	 * Envia "ManagerAction_QueuePause"
	 * Pausa Una interface
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueuePause
	 * @param string $interface Interface (SIP/testphone,DAHDI/1...)
	 * @param string $paused (tuue or false)
	 * @param string $queue Cola
	 * @param string $reason
	 * @return array ver eventSimple()
	 */
	function queuePause($interface,$paused,$queue=null,$reason=null){
		$arg["Interface"]=$interface;
		$arg["Paused"]=$paused;
		if(!is_null($queue)){
			$arg["queue"]=$queue;
		}
		if(!is_null($reason)){
			$arg["Reason"]=$reason;
		}
		return $this->eventSimple("QueuePause",$arg);
	}
	/**
	 * Envia "ManagerAction_QueuePenalty"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueuePenalty
	 * @param string $interface Interface (SIP/testphone,DAHDI/1...)
	 * @param string $penalty Prioridad de la interface
	 * @param string $queue Cola
	 * @return array ver eventSimple()
	 */
	function queuePenalty($interface,$penalty,$queue=null){
		$arg["Interface"]=$interface;
		$arg["Penalty"]=$penalty;
		if(!is_null($queue)){
			$arg["queue"]=$queue;
		}
		return $this->eventSimple("QueuePenalty",$arg);
	}
	/**
	 * Envia "ManagerAction_QueueRule"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueueRule
	 * @param string $rule Regla
	 * @return array ver eventSimple()
	 */
	function queueRule($rule){
		$arg["Rule"]=$rule;
		return $this->eventSimple("QueueRule",$arg);
	}
	/**
	 * Envia "ManagerAction_QueueLog"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_QueueLog
	 * @return array ver eventSimple()
	 */
	function queueLog($queue,$event,$uniqueid=null,$interface=null,$message=null){
		$arg["Queue"]=$queue;
		$arg["Event"]=$event;
		if(!is_null($queue)){
			$arg["Uniqueid"]=$uniqueid;
		}
		if(!is_null($interface)){
			$arg["Interface"]=$interface;
		}
		if(!is_null($message)){
			$arg["Message"]=$message;
		}
		return $this->eventSimple("QueueLog",$arg);
	}
	/**
	 * Envia "ManagerAction_UserEvent"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_UserEvent
	 * @param string $userEvent evento
	 * @param array $headers array("header1"="value","headerN"="value")
	 * @return array ver eventSimple()
	 */
	function userEvent($userEvent,$headers=null){
		$arg["UserEvent"]=$userEvent;
		if(!is_null($headers)){
			$arg=array_merge($arg,$headers);
		}
		return $this->eventSimple("UserEvent",$arg);
	}
	/**
	 * Envia "ManagerAction_WaitEvent"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_WaitEvent
	 * @param int $timeout
	 * @return array ver listEvent()
	 */
	function waitEvent($timeout){
		$action["arg"]["Timeout"]=$timeout;
		$action["action"]="WaitEvent";
		return $this->listEvent($action,"SequenceNumber");
	}
	/**
	 * Envia "ManagerAction_DBPut"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DBPut
	 * @param string $family
	 * @param array $key
	 * @param array $val
	 * @return array ver eventSimple()
	 */
	function dbPut($family,$key,$val=null){
		$arg["Family"]=$family;
		$arg["Key"]=$key;
		if(!is_null($val)){
			$arg["Val"]=$val;
		}
		return $this->eventSimple("DBPut",$arg);
	}
	/**
	 * Envia "ManagerAction_DBDel"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DBDel
	 * @param string $family
	 * @param array $key
	 * @return array ver eventSimple()
	 */
	function dbDel($family,$key){
		$arg["Family"]=$family;
		$arg["Key"]=$key;
		return $this->eventSimple("DBDel",$arg);
	}
	/**
	 * Envia "ManagerAction_DBDelTree"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DBDelTree
	 * @param string $family
	 * @param array $key
	 * @return array ver eventSimple()
	 */
	function dbDelTree($family,$key=null){
		$arg["Family"]=$family;
		if(!is_null){
			$arg["Key"]=$key;
		}
		return $this->eventSimple("DBDelTree",$arg);
	}
	/**
	 * Envia "ManagerAction_DBGet"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DataGet
	 * @param string $family
	 * @param string $key
	 * @param string $path
	 * @return array ver listEvent()
	 */
	function dbGet($family,$key="",$path=""){
		$action["arg"]["Family"]=$family;
		$action["arg"]["Key"]=$key;
		$action["arg"]["Path"]=$path;
		$action["action"]="DBGet";
		return $this->listEvent($action,"SequenceNumber");
	}
	/**
	 * Envia "ManagerAction_Monitor"
	 * Inicia el monitoreo de un canal
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_Monitor
	 * @access public
	 * @param string $channel canal activo
	 * @param string $format formato para el archivo (gsm, ulaw...)
	 * @param string $file Nombre del archivo
	 * @param string $mix Si va a ser de tipo mix (true or false)
	 * @return array Ver eventSimple() 
	 */
	function monitor($channel,$format=null,$file=null,$mix=null){
		$arg["Channel"]=$channel;
		if(!is_null($file)){
			$arg["File"]=$file;
		}
		if(!is_null($format)){
			$arg["Format"]=$format;
		}
		if(!is_null($mix)){
			$arg["Mix"]=$mix;
		}
		return $this->eventSimple("Monitor", $arg);
	}
	/**
	 * Envia "ManagerAction_UnpauseMonitor"
	 * Pausa el monitoreo en un canal
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_PauseMonitor
	 * @access public
	 * @param string $channel canal activo
	 * @return array Ver eventSimple() 
	 */
	function pauseMonitor($channel){
		return $this->eventSimple("PauseMonitor",array("Channel"=>$channel));
	}
	/**
	 * Envia "ManagerAction_UnpauseMonitor"
	 * prosige el monitoreo en un canal
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_UnpauseMonitor
	 * @access public
	 * @param string $channel canal activo
	 * @return array Ver eventSimple() 
	 */
	function unpauseMonitor($channel){
		return $this->eventSimple("UnpauseMonitor",array("Channel"=>$channel));
	}
	/**
	 * Envia "ManagerAction_StopMonitor"
	 * Detiene el monitoreo en un canal
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_StopMonitor
	 * @access public
	 * @param string $channel canal activo
	 * @return array Ver eventSimple() 
	 */
	function stopMonitor($channel){
		return $this->eventSimple("StopMonitor",array("Channel"=>$channel));
	}
	/**
	 * Envia "ManagerAction_ChangeMonitor"
	 * Cambia el archivo de un canal monitoriado
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_ChangeMonitor
	 * @access public
	 * @param string $channel canal activo
	 * @param string $file archivo
	 * @return array Ver eventSimple() 
	 */
	function changeMonitor($channel,$file){
		return $this->eventSimple("ChangeMonitor",array("Channel"=>$channel,"File"=>$file));
	}
	/**
	 * Envia "ManagerAction_MixMonitorMute"
	 * Habilita o deshabilita el mute en canales motitoriados tipo mix
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_MixMonitorMute
	 * @access public
	 * @param string $channel canal activo
	 * @param int $state 1 on 0 off
	 * @param string $direction read, write o both (default: both)
	 * @return Ver eventSimple() 
	 */
	function mixMonitorMute($channel,$state,$direction=null){
		$arg["Channel"]=$channel;
		$arg["State"]=$state;
		if(!is_null($direction)){
			$arg["Direction"]="both";
		}
		return $this->eventSimple("MixMonitorMute",$arg);
	}
	/**
	 * Envia "ManagerAction_MeetmeList" 
	 * Lista los participantes en una conferencia 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_MeetmeList
	 * @access public
	 * @param string $conference Numero meetme (Default: todas las activas)
	 * @return array Ver listEvent()
	 */
	function meetmeList($conference=null){
		if(!is_null($conference)){
			$arg["arg"]["Conference"]=$conference;
			$arg["action"]="MeetmeList";
			return $this->listEvent($arg,array("Conference","UserNumber"),true);
		}else{
			return $this->listEvent("MeetmeList",array("Conference","UserNumber"),true);
		}
		
	}
	/**
	 * Envia "ManagerAction_MeetmeMute"
	 * Coloca el mute a un usuario dentro de una sala Meetme 
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_MeetmeMute
	 * @access public
	 * @param string $conference Numero meetme
	 * @param int $usernum Numero de usuario 
	 * @return array Ver eventSimple() 
	 */
	function meetmeMute($conference,$usernum){
		$arg["Meetme"]=$conference;
		$arg["Usernum"]=$usernum;
		return $this->eventSimple("MeetmeMute",$arg);
	}
	/**
	 * Envia "ManagerAction_MeetmeUnmute"
	 * Quita el mute a un usuario dentro de una sala Meetme
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_MeetmeUnmute
	 * @access public
	 * @param string $conference Numero meetme
	 * @param int $usernum Numero de usuario
	 * @return array Ver eventSimple()
	 */
	function meetmeUnmute($conference,$usernum){
		$arg["Meetme"]=$conference;
		$arg["Usernum"]=$usernum;
		return $this->eventSimple("MeetmeUnmute",$arg);
	}
	/**
	 * Envia "ManagerAction_SKINNYdevices"
	 * Lista los Dispositivos SKINNY
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SKINNYdevices
	 * @access public
	 * @return array Ver listEvent()
	 */
	function skinnyDevices(){
		$responce=$this->eventSimple("SKINNYdevices",false);
		if($responce["Response"]=="Success"){
			$reg=$responce;
			$reg["list"]=array();
			$responce=$this->readEnd(true);
			$div=0;
			$compete=false;
			for($g=0;$g<count($responce);$g++){
				$serpa=strpos($responce[$g], ':');
				$var=substr($responce[$g],0,$serpa);
				$val=substr($responce[$g],$serpa+2);
				if(!$compete){
					switch ($var) {
						case 'Event':
							if($val=="DevicelistComplete"){
								$compete=true;
								$reg["listInfo"][$var]=$val;
							}elseif($val=="DeviceEntry"){
								$div++;
								$reg["list"][$div][$var]=$val;
							}
							break;
						default:
							$reg["list"][$div][$var]=$val;
							break;
					}
				}else{
					$reg["listInfo"][$var]=$val;
				}
			}
			if($reg["listInfo"]["ListItems"]==$div){
				return $reg;
			}else{
				return false;
			}
		}else{
			return $responce;
		}
	}
	/**
	 * Envia "ManagerAction_SKINNYlines"
	 * Lista las lineas SKINNY
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SKINNYlines
	 * @access public
	 * @return array Ver listEvent()
	 */
	function skinnyLines(){
		$responce=$this->eventSimple("SKINNYlines",false);
		if($responce["Response"]=="Success"){
			$reg=$responce;
			$reg["list"]=array();
			$responce=$this->readEnd(true);
			$div=0;
			$compete=false;
			for($g=0;$g<count($responce);$g++){
				$serpa=strpos($responce[$g], ':');
				$var=substr($responce[$g],0,$serpa);
				$val=substr($responce[$g],$serpa+2);
				if(!$compete){
					switch ($var) {
						case 'Event':
							if($val=="LinelistComplete"){
								$compete=true;
								$reg["listInfo"][$var]=$val;
							}elseif($val=="LineEntry"){
								$div++;
								$reg["list"][$div][$var]=$val;
							}
							break;
						default:
							$reg["list"][$div][$var]=$val;
							break;
					}
				}else{
					$reg["listInfo"][$var]=$val;
				}
			}
			if($reg["listInfo"]["ListItems"]==$div){
				return $reg;
			}else{
				return false;
			}
		}else{
			return $responce;
		}
	}
	/**
	 * Envia "ManagerAction_SKINNYshowdevice"
	 * Obtiene informacion de un dispositivo SKINNY
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SKINNYshowdevice
	 * @access public
	 * @param strinf $divice Id del Dispisitivo SKINNY
	 * @return Array
	 * (
	 *     [Channeltype] => SKINNY
	 *     [ObjectName] => florian
	 *     [ChannelObjectType] => device
	 * 	   .........
	 * )
	 * En caso de error el arra estara Vacio
	 */
	function skinnyShowDevice($device){
		$this->send("SKINNYshowdevice",array("Device"=>$device));
		$buffer=array();
		$buf="";
		do{
			$saltar=false;
			$buf=trim(fgets($this->SOCK, 1024));
			if($buf!=""){
				if($pDiv=strpos($buf,':')){
					$buffer[substr($buf,0,$pDiv)]=substr($buf,$pDiv+2);
				}else{
					array_push($buffer,$buf);
				}
			}
		}while($buf!="");
		return $buffer;
	}
	/**
	 * Envia "ManagerAction_SKINNYshowline"
	 * Obtiene informacion de na linea SKINNY
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_SKINNYshowline
	 * @access public
	 * @param strinf $line Id de la linea SKINNY
	 * @return Array
	 * (
	 *     [Channeltype] => SKINNY
	 *     [ObjectName] => florian
	 *     [ChannelObjectType] => line
	 * 	   .........
	 * )
	 * En caso de error el arra estara Vacio
	 */
	function skinnyShowLine($line){
		$this->send("SKINNYshowline",array("Line"=>$line));
		$buffer=array();
		$buf="";
		do{
			$saltar=false;
			$buf=trim(fgets($this->SOCK, 1024));
			if($buf!=""){
				if($pDiv=strpos($buf,':')){
					$buffer[substr($buf,0,$pDiv)]=substr($buf,$pDiv+2);
				}else{
					array_push($buffer,$buf);
				}
			}
		}while($buf!="");
		return $buffer;
	}
	/**
	 * Envia "ManagerAction_DataGet"
	 * @link https://wiki.asterisk.org/wiki/display/AST/ManagerAction_DataGet
	 * @param string $path
	 * @param string $search
	 * @param string $file
	 * @return array Lista el resultado del la consulta en un array
	 */
	function dataGet($path,$search=null,$filter=null){
		$arg["Path"]=$path;
		if(!is_null($search)){
			$arg["search"]=$search;
		}
		if(!is_null($filter)){
			$arg["Filter"]=$filter;
		}
		$this->send("DataGet",$arg);
		$responce=trim(fgets($this->SOCK, 1024));
		if(substr($responce,0,8)=="Response"){
			$reg[substr($responce,0,8)]=substr($responce,stripos($responce,":")+2);
			$responce=trim(fgets($this->SOCK, 1024));
			$reg[substr($responce,0,stripos($responce,":"))]=substr($responce,stripos($responce,":")+2);
			return $reg;
		}elseif(substr($responce,0,5)=="Event"){
			return $this->readEnd(true);
		}
	}
	
	/*
	 * Private functions
	 */
	/**
	 * Obtiene respuesta de listas
	 * @access private  
	 * @param string array $action array("actions"=>"ManagerAction" ,"RequiredHeader" => Value,"OptionalHeader" => Value) or "ManagerAction"
	 * @param string array $keys Valores para tomar como llaves array("Header","Header") or "Header"
	 * @param bool $conp true compueba el numero de resultados obtenidos cuando $items es diferente a "ListItems"
	 * @param string $items Cadena de respuesta de numero de itens obtenidos ListItems-Items...  (Default: ListItems)
	 * @return array Respuesta de Listas  
	 * Array
	 * (
	 * 		[Response] => "Success" or "Error"...
	 * 		[EventList] => start
	 * 		[Message] => .....
	 * 		[list] => Array
	 * 		(
	 * 		    [ID] => Array
	 * 		        (
	 * 		            [Event] => List...
	 * 		            [Variable] => Value
	 * 						...
	 * 		       )
	 * 		)
	 * 		[listInfo] => Array
	 * 		(
	 * 		            [Event] => ...Complete
	 * 		            [EventList] => Complete
	 * 		            [ListItems] => XX
	 * 		            ....
	 * 		)
	 * )
	 */
	private function listEvent($action,$keys,$conp=false,$items="ListItems"){
		if(is_array($action)){
			$action2=$action["action"];
			$arg=$action["arg"];
			$response=$this->eventSimple($action2,$arg);
		}else{
			$action2=$action;
			$response=$this->eventSimple($action2);
		}
		$return=$response;
		$reg=array();
		if($response["Response"]=="Success"){
			if(isset($response["EventList"])){
				$list=true;
			}else{
				$list=false;
			}
			if(is_array($keys)){
				$id=true;
			}else{
				$id=false;
			}
			$error=false;
			do{
				$response=$this->readEnd();
				if (isset($response["Response"])){
					$error=true;
					break;
				}
				if(substr($response["Event"],-8)=="Complete"){
					break;
				}
				if($id){
					$key="";
					for($a=0;$a<count($keys);$a++){
						if(isset($response[$keys[$a]])){
							if($a>0){
								$key.=":";
							}
							$key.=$response[$keys[$a]];
						}
					}
					$reg[$key]=$response;
				}else{
					$reg[$response[$keys]]=$response;
				}
			}while(substr($response["Event"],-8)!="Complete");
			$return["List"]=$reg;
			if($error){
				$return["Error"]=$response;
				return $return;
			}
			$ss=$items;
			if($list){
				if($response[$ss] == count($reg)){
					$return["ListInfo"]=$response;
					return $return;
				}else{
					return false;
				}
			}else{
				if($conp){
					if($response[$ss] == count($reg)){
						$return["ListInfo"]=$response;
						return $return;
					}else{
						return false;
					}
				}else{
					return $return;
				}
			}
		}else{
			return $return;
		}
	}
	/**
	 * Obtiene respuesta de eventos 
	 * @access private  
	 * @param string $action ManagerAction
	 * @param array $arguments arra("RequiredHeader" => Value,"OptionalHeader" => Value)
	 * @param bool $wm without Message Algunas acciones des satisfactorio sin dar un mensaje
	 * @return array Respuesta de eventos
	 * Array
	 * (
	 * 		[Response] => "Success" or "Error"
	 * 		[Message] => xxxxxxxxx
	 * )
	 */
	private function eventSimple($action,$arg=array(),$wm=null){
		if(count($arg)!=0){
			$this->send($action,$arg);
		}else{
			$this->send($action);
		}
		if(is_null($wm)){
			return $this->read("Message");
		}else{
			return $this->read("Response");
		}
	}
	/**
	 * Obtiene respuesta de informacion
	 * @access private  
	 * @param string $action ManagerAction
	 * @param bool $wm without Message Algunas acciones dan satisfactorio sin dar un mensaje
	 * @param array $arg arra("RequiredHeader" => Value,"OptionalHeader" => Value)
	 * @return array Respuesta de informacion
	 * Array
	 * (
	 * 		[Response] => "Follows" or "Success" or "Error"
	 *		[list] => Array
	 *		(
	 * 			[x] => Linea simpre de respuesta / Simpre line response
	 * 			...
	 * 			[variable] => valor / Value
	 * 			...
	 *		)
	 * )
	 */
	private function responceInfo($action,$wm,$arg=array()){
		$this->send($action,$arg);
		if($wm){
			$response=$this->read("Response");
		}else{
			$response=$this->read("Message");
		}
		if($response["Response"]=="Success"){
			$response["Info"]=$this->readEnd();
			return $response;
		}elseif($response["Response"]=="Follows"){
			$response["Info"]=$this->readEnd();
			return $response;
		}else{
			return $response;
		}
	}
	/**
	 * Envia peticion al server
	 * @access private
	 * @param string $action ManagerAction
	 * @param array $arguments arra("RequiredHeader" => Value,"OptionalHeader" => Value)
	 * @link see https://wiki.asterisk.org/wiki/display/AST/AMI+Action+Template+Page
	 */
	private function send($action,$arguments=array()){
		$comand="Action: $action\r\n";
		fwrite($this->SOCK, $comand);
		if(count($arguments)!=0){
			foreach($arguments as $arg => $value){
				$comand=$arg.": ".$value."\r\n";
				fwrite($this->SOCK, $comand);
			}
		}
		fwrite($this->SOCK, "\r\n");
	}
	/**
	 * Obtiene respuestas de servidor hasta una linea en definida
	 * @access private
	 * @param string $end texto de la ultima linea a leer
	 * @return arry
	 */
	private function read($end){
		$buffer=array();
		$buf="";
		do{
			$buf=trim(fgets($this->SOCK, 1024));
			if($pDiv=strpos($buf, ':')){
				$buffer[substr($buf,0,$pDiv)]=substr($buf,$pDiv+2);
			}elseif($buf==$end){
				array_push($buffer,$buf);
				$end=substr($buf,0,strlen($end));
			}elseif($buf!=""){
				array_push($buffer,$buf);
			}
		}while(substr($buf,0,strlen($end))!=$end);
		return $buffer;
	}
	/**
	 * Obtiene respuestas de servidor hasta una linea en blanco
	 * @access private
	 * @param bool $sep true para no separar por ":"
	 * @return array
	 */
	private function readEnd($sep=false){
		$buffer=array();
		$buf="";
		do{
			$saltar=false;
			$buf=trim(fgets($this->SOCK, 1024));
			if($buf=="" and count($buffer)==0){
				$buf="a";
			}else{
				if($buf!=""){
					if($sep){
						array_push($buffer,$buf);
					}else{
						if($pDiv=strpos($buf,':')){
							$buffer[substr($buf,0,$pDiv)]=substr($buf,$pDiv+2);
						}else{
							array_push($buffer,$buf);
						}
					}
				}
			}
		}while($buf!="");
		return $buffer;
	}
	/**
	 * Abre el secket
	 * @access private
	 * @return bool true si abrio el socket false ocurrio un error 
	 */
	private function openSock(){
		$this->SOCK=@fsockopen($this->SERVER,$this->PORT);
		if(!$this->SOCK){
			return false;
		}else{
			return true;
		}
	}
	/**
	 * Cierra el Socket
	 * @access private
	 */
	private function closeSock(){
		fclose($this->SOCK);
	}
	/**
	 * Destructor
	 */
	function __destruct() {
       unset($this);
   }
}
?>