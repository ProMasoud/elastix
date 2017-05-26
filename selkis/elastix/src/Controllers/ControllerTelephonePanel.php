<?php

namespace Selkis\Elastix\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Selkis\Elastix\Components\ECCP;
use Selkis\Elastix\Components\phpAMI;
use Log;

class ControllerTelephonePanel extends Controller
{
    
    function __construct(){
		$this->ECCP = new ECCP();
		$this->host = config('elastix.host');
		$this->user = config('elastix.user');
		$this->pass = config('elastix.password');
		$this->model = config('elastix.ext_model');
		$this->port = config('elastix.port');
		$this->phpAMI = new phpAMI($this->user,$this->pass,$this->host,$this->port);
	}

	public function getVista(Request $request){
		return view('elastix::barra');
	}

	public function getIndex(Request $request){
		try {
			$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        $r = $ECCP->getagentstatus($ext[config('elastix.value_ext_in')]);
	        $this->ECCP->disconnect();
	        return ['status' => $r->status, 'onhold'=>floatval($r->onhold)];
		} catch (Exception $e) {
			Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::getIndex', $e);
			return;
		}
	}
    /**
     * [agentlogin description]
     * @return [type] [description]
     */
    public function agentlogin(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        $r = $ECCP->loginagent($ext[config('elastix.value_ext_in')]);
	        $bFalloLogin = FALSE;
	        $this->ECCP->disconnect();
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::agentlogin', $e);
        }
        
    }

    /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function agentlogout(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        $r = $ECCP->logoutagent($ext[config('elastix.value_ext_in')]);
	        $bFalloLogin = FALSE;
	        $this->ECCP->disconnect();
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::agentlogout', $e);
        }
    }

    /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function abreak(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        $r = $ECCP->pauseagent($ext[config('elastix.value_ext_in')]);
	        $bFalloLogin = FALSE;
	        $this->ECCP->disconnect();
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::abreak', $e);
        }
    }

     /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function unbreak(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        $r = $this->ECCP->unpauseagent('2');
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::unbreak', $e);
        }
    }

     /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function hold(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $channel="DAHDI/i2/2126264000-";
	        $parkinglot="70";
			$timeout="30000";
			$login = $this->phpAMI->login();
			if ($login["Response"]=="Success") {
				$this->phpAMI->park($channel,$agent,$parkinglot,$timeout);
				$this->phpAMI->logoff();
			}
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::abreak', $e);
        }
    }

    /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function unhold(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        print_r($this->ECCP->getAgentStatus());
	        $r = $this->ECCP->unhold();
	        $this->ECCP->disconnect();
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::abreak', $e);
        }
    }

    /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function hangup(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        print_r($this->ECCP->getAgentStatus());
	        $r = $this->ECCP->hangup();
	        $this->ECCP->disconnect();
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::abreak', $e);
        }
    }

    /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function colgar(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
			$login = $this->phpAMI->login();
			if ($login["Response"]=="Success") {
				$array = $this->phpAMI->coreShowChannels();	
				$SIP 	= "SIP/".$ext[config('elastix.value_ext_out')]."-";
				foreach($array as $datos2){
					foreach($datos2 as $datos3){
						foreach($datos3 as $dato){
							if (strpos($dato, $SIP) !== false) {
								$SIP = $dato; 
							}
						}
					}
			 	}
			 	print_r($this->phpAMI->hangup($SIP));
				$this->phpAMI->logoff();
				
			}
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::abreak', $e);
        }
    }

    /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function call(Request $request)
    {
        try {
        	if ($request->destino != null) {
        		$ext = $this->model::where('ip', $request->ip())->first();
        		#Especificamos el contexto
                $strContext = "default";
				#indicamos el tiempo de espera de la marcación
                $strWaitTime = config('elastix.waitTime');
				#La prioridad para colocar la llamada
                $strPriority = 1;
				#maximo de reintentos
                $strMaxRetry = "1";
				#Troncal (Linea)
				$troncal=config('elastix.troncal');
				#Extensión origen
                $extension=$ext[config('elastix.value_ext_out')];
        		$errno=0;
                $errstr=0;	
                $oSocket = fsockopen($this->host, $this->port, $errno, $errstr, 20);
                if (!$oSocket) {
                	return ['mgs'=>$errstr, 'code' => $errno];
                }else{
                	puts($oSocket, "Action: login\r\n");
                    fputs($oSocket, "Events: off\r\n");
                    fputs($oSocket, "Username: ".$strUser."\r\n");
                    fputs($oSocket, "Secret: ".$strSecret."\r\n\r\n");
                    fputs($oSocket, "Action: originate\r\n");
                    fputs($oSocket, "Channel: ".$troncal."".$destino."\r\n");
                    fputs($oSocket, "WaitTime: ".$strWaitTime."\r\n");
                    fputs($oSocket, "CallerId: ".$destino."\r\n");
                    fputs($oSocket, "Exten: ".$extension."\r\n");
                    fputs($oSocket, "Context: ".$strContext."\r\n");
                    fputs($oSocket, "Priority: ".$strPriority."\r\n\r\n");
                    fputs($oSocket, "Action: Logoff\r\n\r\n");
                    sleep(2);
                    fclose($oSocket);
                    return ['mgs' => $extension.' llamando a '.$request->destino, 'code'=>'WS001'];
                }
        	}	
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::call', $e);
        }
    }

     /**
     * [agentlogin description]$this->ECCP
     * @return [type] [description]
     */
    public function transfer(Request $request)
    {
        try {
        	$ext = $this->model::where('ip', $request->ip())->first();
	        $agent = "Agent/".$ext[config('elastix.value_ext_in')]."";
	        $this->ECCP->connect($this->host, $this->user, $this->pass);
	        $this->ECCP->setAgentNumber($agent);
	        $this->ECCP->setAgentPass($ext[config('elastix.value_ext_in')]);
	        print_r($this->ECCP->getAgentStatus());
	        $r = $this->ECCP->transfercall($request->extnum);
	        $this->ECCP->disconnect();
	        return ['msg'=>'Transferido','code'=>'WS001'];
        } catch (Exception $e) {
        	Log::erro('Ha ocurrido un error inesperado en ControllerTelephonePanel::transfer', $e);
        }
    }
}
