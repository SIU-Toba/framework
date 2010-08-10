<?php

class toba_punto_montaje_pers extends toba_punto_montaje_proyecto
{
	function get_tipo()
	{
		return toba_punto_montaje::tipo_pers;
	}

	protected function get_clase_autoload()
	{
		return str_replace('%id_proyecto%', $this->get_proyecto_referenciado(),
							toba_modelo_proyecto::patron_nombre_autoload_pers);
	}
}
?>
