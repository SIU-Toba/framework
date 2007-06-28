<?php
/*
*	Unidad METADATO/EXTENSION
*/
class toba_molde_elemento
{
	protected $asistente;
	protected $proyecto;
	protected $datos;				// Datos relacion que persiste el componente
	protected $extension;			// Molde del codigo de la extension
	protected $carpeta_base;		
	protected $archivo;

	function __construct($asistente)
	{
		$this->asistente = $asistente;
		$this->proyecto = $this->asistente->get_proyecto();
		//Busco el datos relacion correspondiente al componente
		$id = toba_info_editores::get_dr_de_clase($this->clase);
		$componente = array('proyecto' => $id[0], 'componente' => $id[1]);
		$this->datos = toba_constructor::get_runtime($componente);
		$this->datos->tabla('base')->nueva_fila(array(	'nombre'=>$this->clase.' generado automaticamente',
														'proyecto'=>$this->proyecto) );
		$this->datos->tabla('base')->set_cursor(0);
		$this->ini();
	}

	function ini(){}
	
	//----------------------------------------------------
	//-- API CONSTRUCCION
	//----------------------------------------------------
	
	function set_nombre($nombre)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'nombre',$nombre);
	}

	function set_carpeta_archivos($carpeta)
	{
		$this->carpeta_base = $carpeta;
	}

	function extender_clase($archivo)
	{
		
	}

	//---------------------------------------------------
	//-- Guardar METADATO / ARCHIVO 
	//---------------------------------------------------

	function generar()
	{
		$this->generar_archivo();
		$this->asociar_archivo();
		$this->guardar_metadatos();
	}
	
	protected function generar_archivo()
	{
		
	}
	
	protected function asociar_archivo()
	{
		
	}
	
	protected function guardar_metadatos()
	{
		ei_arbol($this->datos->get_conjunto_datos_interno(), $this->clase);
		$this->datos->get_persistidor()->desactivar_transaccion();
		$this->datos->sincronizar();
		$clave = $this->get_clave_componente_generado();
		$this->asistente->registrar_elemento_creado(	$this->clase, 
														$clave['proyecto'],
														$clave['clave'] );
	}
	
}
?>