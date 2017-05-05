<?php
/**
 * Manipulacion de procesos Windows/Linux
 * 
 * @package Varios
 */	
class toba_manejador_procesos
{
	/**
	 * Ejecuta un comando en background
	 * @param string $command
	 * @return integer
	 */
	static function background($command)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::background_win($command);
		else 
			return self::background_linux($command);		
	}

	/**
	 * Determina si existe en ejecucion un proceso con el PID dado
	 * @param integer $PID
	 * @return boolean
	 */
	static function is_running($PID)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::is_running_win($PID);
		else 
			return self::is_running_linux($PID);	
	}

	/**
	 * Mata el proceso que tenga el PID indicado
	 * @param integer $PID
	 * @return boolean
	 */
	static function kill($PID)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::kill_win($PID);
		else 
			return self::kill_linux($PID);
	}

	/**
	 * Busca PIDs que concuerden con el criterio indicado
	 * @param type $match
	 * @return type
	 */
	static function buscar_pids($match)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			return self::buscar_pids_win($match);
		else 
			return self::buscar_pids_linux($match);
	}

	/**
	 * Genera un identificador
	 * @param mixed $id
	 * @return string
	 */
	static function crear_identificador($id)
	{
		return "pid_sistema_$id";
	}

	/*
	 * Quita la cadena inicial del identificador dejando unicamente el ID
	 * @return mixed
	 */
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
	/**
	 * @ignore
	 */
	static function background_linux($command)
	{
		$PID = trim(shell_exec("nohup $command > /dev/null & echo $!"));
		return($PID);
	}

	/**
	 * @ignore
	 */	
	static function is_running_linux($PID)
	{
		exec("ps $PID", $ProcessState);
		return(count($ProcessState) >= 2);
	}

	/**
	 * @ignore
	 */	
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
	 * @ignore
	 */
	static function buscar_pids_linux($match)
	{
		$match = escapeshellarg($match);
		exec("ps fx|grep $match|grep -v grep|awk '{print $1}'", $output, $ret);
		if($ret) return 'you need ps, grep, and awk installed for this to work';
		return $output;
	}

	// WINDOWS ----------------------------------------------------------------------------------
	/**
	 * @ignore
	 */
	static function background_win($command)
	{
		$WshShell = new COM("WScript.Shell");
		return $WshShell->Run($command, 0, false);
	}

	/**
	 * @ignore
	 */
	static function is_running_win($pid)
	{
		//NO IMPLEMENTADO
		return false;
	}
	
	/**
	 * @ignore
	 */
	static function kill_win($pid)
	{
		//NO IMPLEMENTADO
		return false;
	}

	/**
	 * @ignore
	 */	
	static function buscar_pids_win($match)
	{
		//NO IMPLEMENTADO
		return array();
	}
	
	//-----------------------------------------------------------------------------------------------------
	/**
	 * Ejecuta un comando especifico redireccionando la salida de consola
	 * @param string $cmd
	 * @param string $stdout Salida de consola
	 * @param string $stderr	Salida en caso de error
	 * @return int
	 */
	static function ejecutar($cmd, &$stdout, &$stderr)
	{
		$outfile = tempnam(toba_dir().'/temp', "cmd");
		$errfile = tempnam(toba_dir().'/temp', "cmd");
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("file", $outfile, "w"),
			2 => array("file", $errfile, "w")
		);
		$proc = proc_open($cmd, $descriptorspec, $pipes);

		if (!is_resource($proc)) return 255;

		fclose($pipes[0]);

		$exit = proc_close($proc);
		$stdout = file_get_contents($outfile);
		$stderr = file_get_contents($errfile);
		unlink($outfile);
		unlink($errfile);
		return $exit;
	}		
}
?>