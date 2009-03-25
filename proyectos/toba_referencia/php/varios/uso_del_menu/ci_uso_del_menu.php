<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_uso_del_menu extends toba_ci
{
	protected $cambiar_js = false;
	
	function evt__pant_agregar_opcion__entrada()
	{
		//-- Crea una nueva operación (apuntando a esta misma)
		$datos = array('nombre' => 'Opción Nueva');
		toba::menu()->agregar_opcion($datos);
		
		//-- Borra la carpeta tutorial
		$id = '3292';
		toba::menu()->quitar_opcion($id);
	}
	

	function evt__pant_cambiar_opcion__entrada()
	{
		$datos = array(
			'nombre' => 'ATENCION! Abre popup',
			'imagen_recurso_origen' => 'toba',
			'imagen' => 'warning.gif',
			'js' => 'abrir_popup("goggle", "http://www.google.com")'
		);
		toba::menu()->set_datos_opcion('30000005', $datos);
	}


	function evt__pant_cambiar_comportamiento__entrada()
	{
		$this->cambiar_js = true;
	}

	function extender_objeto_js()
	{
		if ($this->cambiar_js) {
			echo "
				function callback_menu(proyecto, operacion, url, es_poup) {
					return confirm('Esta a punto de abandonar la edición  sin grabar, ¿Desea continuar?');
				}
				
				toba.set_callback_menu(callback_menu);
			";
		}
	}
}

?>