<?php

class toba_modelo_elemento
{
	protected $manejador_interface;	

	function set_manejador_interface( $manejador_interface )
	{
		$this->manejador_interface = $manejador_interface;
	}
	
	function get_manejador_interface()
	{
		if( ! isset( $this->manejador_interface ) ) {
			return new toba_mock_proceso_gui();	
		} else {
			return $this->manejador_interface;
		}
	}
	

	function migrar_rango_versiones($desde, $hasta, $recursivo, $con_transaccion=true)
	{
		$versiones = $desde->get_secuencia_migraciones($hasta);
		foreach ($versiones as $version) {
			$this->manejador_interface->enter();
			$this->manejador_interface->subtitulo("Versión ".$version->__toString());
			$this->migrar_version($version, $recursivo, $con_transaccion);
		}
	}	
}
?>