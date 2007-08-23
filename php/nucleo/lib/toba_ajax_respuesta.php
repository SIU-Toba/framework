<?php
require_once(toba_dir() . '/php/3ros/JSON.php');

class toba_ajax_respuesta
{
	protected $modo;
	protected $contenido;
	
	function __construct($modo)
	{
		$this->modo = $modo;
	}
	
	
	function set($contenido)
	{
		$this->contenido = $contenido;
	}
	
	function comunicar()
	{
		if (isset($this->contenido) && isset($this->modo)) {
			switch ($this->modo) {
				case 'D':
					$json = new Services_JSON();
					toba::logger()->debug("Respuesta AJAX: ".var_export($this->contenido, true));	
					echo $json->encode($this->contenido);
					break;
				case 'H':
					echo $this->contenido;
					break;
			}
		}
	}

	
	
}