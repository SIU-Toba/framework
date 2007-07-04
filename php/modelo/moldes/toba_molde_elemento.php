<?php
/*
*	Unidad METADATO/EXTENSION
*/
class toba_molde_elemento
{
	protected $asistente;
	protected $proyecto;
	protected $carpeta_archivo;		
	protected $datos;				// Datos relacion que persiste el componente
	protected $archivo;				// Manejador de archivos

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

	function set_carpeta_archivo($carpeta_relativa)
	{
		$this->carpeta_archivo = $carpeta_relativa;
	}

	function archivo_relativo()
	{
		return $this->carpeta_archivo .'/'. $this->archivo;		
	}
	
	function archivo_absoluto()
	{
		return toba::proyecto($this->proyecto)->get_path() . '/php/'. $this->archivo_relativo();
	}

	//---------------------------------------------------
	//-- Guardar METADATO / ARCHIVO 
	//---------------------------------------------------

	function generar()
	{
		if (isset($this->archivo) ) {
			toba_manejador_archivos::crear_arbol_directorios($this->carpeta_archivo);
			$this->generar_archivo();
			$this->asociar_archivo();
		}
		$this->guardar_metadatos();
	}
	
	protected function generar_archivo()
	{
		$php = $this->get_codigo_php();
		file_put_contents($this->archivo_absoluto(), "<?php\n$php\n?>");
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