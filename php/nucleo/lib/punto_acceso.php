<?
/*
*	Esta clase maneja e
*/

class punto_acceso
{
	protected $instancias_disponibles;
	
	function __construct()
	{
		global $instancia;
		$this->instancias_disponibles = $instancia;
	}
	
	function cambiar_instancia_actual($nueva)
	//Cambia la instancia a la que el punto de acceso debe conectarse
	{
		if (array_key_exists($nueva, $this->instancias_disponibles))
		{
			$archivo = $_SERVER['SCRIPT_FILENAME'];
			$handle = fopen ($archivo, "r");
			if ($handle)
			{
				$contenido = fread ($handle, filesize ($archivo));
				$nuevo_contenido = str_replace('"'.apex_pa_instancia.'")', "\"$nueva\")", $contenido);
				fclose ($handle);
	
				$handle = fopen ($archivo, "w");
				if ($handle)
				{
					fwrite($handle, $nuevo_contenido);
					fclose ($handle);
					return true;
				}
			}
		}
		return false;
	}
	
	function get_instancias_posibles()
	{
		return $this->instancias_disponibles;
	}

}

?>