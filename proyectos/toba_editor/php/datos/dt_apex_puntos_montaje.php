<?php
class dt_apex_puntos_montaje extends toba_datos_tabla
{
	function get_listado()
	{
        return toba::puntos_montaje()->get_puntos_montaje_array();
	}

}
?>