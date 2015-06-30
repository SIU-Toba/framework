<?php
/**
*	Manipulacion de procesos Windows/Linux
* @package Varios
*/
class toba_manejador_procesos
{
	static function background($command)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::background_win($command);
		else 
			return self::background_linux($command);		
	}

	static function is_running($PID)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::is_running_win($PID);
		else 
			return self::is_running_linux($PID);	
	}

	static function kill($PID)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::kill_win($PID);
		else 
			return self::kill_linux($PID);
	}

	static function buscar_pids($match)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::buscar_pids_win($match);
		else 
			return self::buscar_pids_linux($match);
	}

	static function crear_identificador($id)
	{
		return "pid_sistema_$id";
	}

	static function quitar_identificador($cadena)
	{
		return str_replace('pid_sistema_','',$cadena);
	}

	// LINUX ------------------------------------------------------------------------------------
	// Ojo: Si el comando es un scritp, devuelve solo su PID. 
	// Por ej: un script que haga "toba ejecutar item ...." genera 3 procesos:
	//  - Uno para el script.
	//	- Uno para el script toba.
	//	- Uno para php.

	static function background_linux($command)
	{
		$PID = trim(shell_exec("nohup $command > /dev/null & echo $!"));
		return($PID);
	}

	static function is_running_linux($PID)
	{
		exec("ps $PID", $ProcessState);
		return(count($ProcessState) >= 2);
	}

	static function kill_linux($PID)
	{
		if (self::is_running($PID))	{
			exec("kill -KILL $PID");
			return true;
		} else
	   		return false;
	}

	/**
	 * Busca los PIDs de todos los procesos que matcheen con el parametro de entrada $match.
	 * Retorna un arreglo con los PIDs.
	 *
	 * @param string $match
	 * @return array
	 */
	static function buscar_pids_linux($match)
	{
		$match = escapeshellarg($match);
		exec("ps fx|grep $match|grep -v grep|awk '{print $1}'", $output, $ret);
		if($ret) return 'you need ps, grep, and awk installed for this to work';
		return $output;
	}

	// WINDOWS ----------------------------------------------------------------------------------

	static function background_win($command)
	{
		$WshShell = new COM("WScript.Shell");
		return $WshShell->Run($command, 0, false);
	}

	static function is_running_win($pid)
	{
		//NO IMPLEMENTADO
		return false;
	}

	static function kill_win($pid)
	{
		//NO IMPLEMENTADO
		return false;
	}

	static function buscar_pids_win($match)
	{
		//NO IMPLEMENTADO
		return array();
	}
}

?>