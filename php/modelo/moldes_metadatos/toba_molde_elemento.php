<?php
/*
*	Unidad METADATO/EXTENSION


	FALTA:
		- Si no se guardan datos, borrar archivos.


*/
class toba_molde_elemento
{
	protected $id;					//ID unico del elemento el todo el molde
	protected $asistente;
	protected $proyecto;
	protected $carpeta_archivo;		
	protected $datos;				// Datos relacion que persiste el componente
	protected $archivo;				// Manejador de archivos
	protected $pisar_archivo = true;

	function __construct($asistente)
	{
		$this->asistente = $asistente;
		$this->asistente->registrar_molde($this);
		$this->id = $this->asistente->get_id_elemento();
		$this->proyecto = $this->asistente->get_proyecto();
		//Busco el datos relacion correspondientes al componente
		$id = toba_info_editores::get_dr_de_clase($this->clase);
		$componente = array('proyecto' => $id[0], 'componente' => $id[1]);
		$this->datos = toba_constructor::get_runtime($componente);
		$this->datos->inicializar();
		$datos = array(	'nombre'=>$this->clase.' generado automaticamente',	
						'proyecto'=>$this->proyecto);
		if ($this->asistente->tiene_fuente_definida()) {
			$datos['fuente_datos_proyecto'] = $this->proyecto;
			$datos['fuente_datos'] = $this->asistente->get_fuente();
			
		}
		$this->datos->tabla('base')->set($datos);
		$this->ini();
	}

	/**
	*	Ventana para que cada componente setee su estado inicial
	*/
	function ini(){}
	
	//----------------------------------------------------
	//-- API CONSTRUCCION
	//----------------------------------------------------
	
	function set_nombre($nombre)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'nombre',$nombre);
	}

	function set_punto_montaje($pm)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0, 'punto_montaje', $pm);						
	}
		
	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------

	function generar()
	{
		if (isset($this->archivo) ) {
			if (!isset($this->carpeta_archivo)) {
				throw new toba_error_asistentes('La carpeta no fue definida.');	
			}
			toba_manejador_archivos::crear_arbol_directorios(dirname($this->archivo_absoluto()));
			if ($this->generar_archivo()) {
				$this->asociar_archivo();
			}
		}
		$this->guardar_metadatos();
	}
	
	protected function generar_archivo()
	{
		$php = $this->get_codigo_php();
		toba_manejador_archivos::crear_archivo_con_datos($this->archivo_absoluto(), "<?php" . "\n" . $php . "\n" .  "?>");
		$this->asistente->registrar_elemento_creado('archivo', $this->proyecto,	$this->archivo_relativo() );
		return true;
	}
	
	protected function guardar_metadatos()
	{
		//ei_arbol($this->datos->get_conjunto_datos_interno(), $this->clase);
		$this->datos->persistidor()->desactivar_transaccion();
		$this->datos->sincronizar();
		$clave = $this->get_clave_componente_generado();
		$this->asistente->registrar_elemento_creado($this->clase, $clave['proyecto'], $clave['clave'] );
	}

	function get_id_opcion_archivo()
	{
		return 'elemento_' . $this->id . '_archivo';	
	}

	//-- PATHs -------------------------------------------------------------	

	function archivo_relativo()
	{
		return $this->directorio_relativo() .'/'. $this->archivo;		
	}
	
	function archivo_absoluto()
	{
		return $this->directorio_absoluto() .'/'. $this->archivo;		
	}

	function directorio_absoluto()
	{
		$path_proyecto = toba::instancia()->get_path_proyecto($this->proyecto);
		return  $path_proyecto . '/php/'. $this->directorio_relativo();
	}

	function directorio_relativo()
	{
		return $this->carpeta_archivo;
	}
}
?>