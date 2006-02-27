<?
require_once('comando_toba.php');
/**
*	Publica los servicios de la clase PROYECTO a la consola toba
*/
class comando_proyecto extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de los METADATOS de PROYECTOS';
	}

	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba proyecto OPCION [-p id_proyecto] [-i id_instancia]");
		$this->consola->enter();
		$this->get_info_parametro_proyecto();
		$this->get_info_parametro_instancia();
		$this->consola->enter();
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Brinda informacion sobre los metadatos.
	*/
	function opcion__info()
	{
		$datos = $this->get_proyecto()->info();
		$this->consola->tabla( $datos, array('tipo','componentes') ,'COMPONENTES' );
	}

	/**
	*	Exporta los metadatos.
	*/
	function opcion__exportar()
	{
		$p = $this->get_proyecto();
		$p->exportar();
		$p->get_instancia()->exportar_local();
	}

	/**
	*	Importa los metadatos.
	*/
	function opcion__importar()
	{
		$this->get_proyecto()->importar_autonomo();
	}

	/**
	*	Elimina los metadatos.
	*/
	function opcion__eliminar()
	{
		if ( $this->consola->dialogo_simple("Desea eliminar el proyecto '"
				.$this->get_id_proyecto_actual()."' de la instancia '"
				.$this->get_id_instancia_actual()."'") ) {
			$this->get_proyecto()->eliminar();
		}
	}

	/**
	*	Compila los metadatos.
	*/
	function opcion__compilar()
	{
		$this->get_proyecto()->compilar();
	}

	/**
	*	Crea un proyecto nuevo. Falta Terminar
	*/
	function opcion__crear()
	{
		$id_proyecto = $this->get_id_proyecto_actual();
		$instancia = $this->get_instancia();
		$id_instancia = $instancia->get_id();
		$this->consola->mensaje( "Creando el proyecto '$id_proyecto' en la instancia '$id_instancia'" );
		$instalacion = $this->get_instalacion();
		$instalacion->crear_proyecto( $instancia, $id_proyecto );
		return;
		$proyecto = $this->get_proyecto();
		$proyecto->exportar();
		$proyecto->info();
	}
}
?>