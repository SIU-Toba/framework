<?php
require_once('comando_toba.php');
//require_once('modelo/personalizacion/personalizacion.php');

class comando_personalizacion extends comando_toba
{
    protected static $schema_original;
    
    static function get_info()
	{
		return 'Administracion de PERSONALIZACIONES';
	}


//    function opcion__test()
//    {
//		$p = $this->get_proyecto();
//		$p->get_pms()->migrar_proyecto();
////		define('apex_pa_proyecto', $p->get_id());
////		toba::puntos_montaje($p)->usar_punto_montaje_proyecto();
//    }

    /**
     * Prepara un proyecto para comenzar a ser personalizado
     */
	function opcion__iniciar()
	{
		$this->consola->mensaje('Preparando personalización.');
		$p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
		$pers->iniciar();
		$this->consola->mensaje('Personalización preparada');
	}

	/**
	 * Exporta la personalización realizada
	 */
	function opcion__exportar()
	{
		$this->consola->mensaje('Exportando la personalizacion. Este proceso puede llevar varios minutos...');
		$p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
		$pers->exportar();
		$this->consola->mensaje('Exportacion terminada.');
	}

	/**
	 * Desactiva la personalización
	 */
	function opcion__desactivar()
	{
		$p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
		$pers->desactivar();
		$this->consola->mensaje('La personalización actual fue desactivada.');
	}
	
	/**
	 * Chequeo de conflictos. Ejecute este comando antes de importar la personalización
	 */
	function opcion__conflictos()
	{
		$p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
		$pers->chequear_conflictos();
		toba_manejador_archivos::crear_archivo_con_datos($p->get_dir(). '/temp/conflictos_verificados.mrk', 'mark1');
	}

	/**
	*  Importa la personalización que está en el directorio personalización del proyecto
	* @consola_parametros Opcional: [-t] Ejecuta toda la importacion dentro de una transaccion
	*/
	function opcion__importar()
	{
		$p = $this->get_proyecto();
		
		//Verificar que se haya ejecutado si o si la opcion de conflicto
		$archivo = $p->get_dir().'/temp/conflictos_verificados.mrk';
		$verifico_conflictos = toba_manejador_archivos::existe_archivo_en_path($archivo);
		if (! $verifico_conflictos) {
			$this->consola->mensaje('Antes de realizar la importación debe ejecutar el comando \'conflictos\'');
			return;
		}
		
		$this->consola->mensaje('Importando la personalizacion...');
		$pers = new toba_personalizacion($p, $this->consola);
		
		//Verifico si la ejecucion se quiere hacer dentro de una transaccion.
		$param = $this->get_parametros();
		if ( isset($param['-t'] )) {
			$pers->set_ejecucion_con_transaccion_global();
		}
		
		//Aplico la personalizacion
		$pers->aplicar();
		$this->consola->mensaje('Proceso Finalizado');
		
		//Elimino la marca  de conflictos para que no pueda volver a ejecutarse la importacion.
		unlink($archivo);
	}
}
?>
