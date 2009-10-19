<?php
class datos_relacion_perfiles extends toba_ap_relacion_db
{
	function evt__post_sincronizacion()
	{
		$this->objeto_relacion->controlador()->validar_ciclos();
	}

}

?>