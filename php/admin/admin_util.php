<?php

/**
*	Utilidades varias para el administrador Toba
*/
class admin_util
{
	
	/**
	*	Refresca el frame izquierdo del editor
	*/
	function refrescar_editor_item()
	{
		echo js::abrir();
		$frame = "parent.".apex_frame_lista;
		echo "
			if ($frame.objeto_ci_1381) {
				$frame.objeto_ci_1381.set_evento(new $frame.evento_ei('refrescar', true, '' ));
			}
		";
		echo js::cerrar();		
	}
	
	
}


?>