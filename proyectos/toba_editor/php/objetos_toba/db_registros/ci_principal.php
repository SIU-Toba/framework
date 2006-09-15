<?php
require_once('objetos_toba/ci_editores_toba.php'); 
require_once('admin_util.php');

class ci_principal extends ci_editores_toba
{
	protected $db_tablas;
	protected $clase_actual = 'objeto_datos_tabla';	
	protected $s__ap_php_db;							// La base posee registro de la existencia de una extension??
	protected $s__ap_php_archivo;						// El archivo de la extension existe en el sistema de archivos??
	
	function conf()
	{
		parent::conf();
		//Mecanismo para saber si la extension PHP de un AP ya exite en la DB y posee archivo
		if ( !isset($this->s__ap_php_db) ) {
			$this->s__ap_php_db = false;
			$this->s__ap_php_archivo = false;
			if ( $this->componente_existe_en_db() ) {
				$datos_ap = $this->get_entidad()->tabla('prop_basicas')->get();
				if( ( $datos_ap['ap'] == 0 ) && $datos_ap['ap_clase'] && $datos_ap['ap_archivo'] ) {
					$this->s__ap_php_db = true;	//El AP esta extendido
				}
				if( admin_util::existe_archivo_subclase($datos_ap['ap_archivo']) ) {
					$this->s__ap_php_archivo = true; //La extension existe
				}
			}
		}
	}

	function evt__procesar()
	{
		parent::evt__procesar();
		unset($this->s__ap_php_db);
		unset($this->s__ap_php_archivo);
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas($form)
	{
		if ( $this->s__ap_php_db ) {
			// Hay extension
			$parametros = info_componente::get_utileria_editor_parametros(array('proyecto'=>$this->id_objeto['proyecto'],
																				'componente'=> $this->id_objeto['objeto']),
																			'ap');
			$form->evento('ver_php')->vinculo()->set_parametros($parametros);
			if ( $this->s__ap_php_archivo ) {
				// El archivo de la extension existe
				$abrir = info_componente::get_utileria_editor_abrir_php(array('proyecto'=>$this->id_objeto['proyecto'],
																				'componente'=> $this->id_objeto['objeto']),
																			'ap');
				$form->set_js_abrir( $abrir['js'] );
			} else {
				$form->eliminar_evento('abrir_php');
			}
		} else {
			$form->eliminar_evento('ver_php');	
			$form->eliminar_evento('abrir_php');
		}
		$form->set_datos($this->get_entidad()->tabla("prop_basicas")->get());
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
	}

	//*******************************************************************
	//**  COLUMNAS  *****************************************************
	//*******************************************************************
	
	function conf__columnas()
	{
		return $this->get_entidad()->tabla('columnas')->get_filas(null,true);	
	}

	function evt__columnas__modificacion($datos)
	{
		$this->get_entidad()->tabla('columnas')->procesar_filas($datos);
	}

	//-- Generacion automatica de columnas!!
	
	function evt__columnas__leer_db()
	{
		$columnas = $this->obtener_definicion_columnas();
		//ei_arbol($columnas);		
		$dbr = $this->get_entidad()->tabla("columnas");
		$actuales = $dbr->get_filas(null, true);
		for($a=0;$a<count($columnas);$a++){
			try{
				//--- Evita incluir dos veces el mismo nombre
				$nueva = true;
				foreach ($actuales as $id => $actual) {
					if ($columnas[$a]['columna'] == $actual['columna']) {
						$nueva = false;
					}
				}
				if ($nueva) {
					$dbr->nueva_fila($columnas[$a]);
				}
			}catch(toba_error $e){
				toba::notificacion()->agregar("Error agregando la COLUMNA '{$columnas[$a]['columna']}'. " . $e->getMessage());
			}
		}
	}

	function obtener_definicion_columnas()
	//Utilizo ADODB para recuperar los metadatos
	{
		//-[ 1 ]- Obtengo datos
		$tabla = $this->get_entidad()->tabla("prop_basicas")->get_fila_columna(0,"tabla");
		$reg = $this->get_entidad()->tabla("base")->get();
		$proyecto = $reg['fuente_datos_proyecto'];
		$id_fuente = $reg['fuente_datos'];
		$fuente = toba::db($id_fuente);
		try{
			$columnas = $fuente->get_definicion_columnas($tabla);
			foreach(array_keys($columnas) as $id){
				$columnas[$id]['columna'] = $columnas[$id]['nombre'];	
				$columnas[$id]['largo'] = $columnas[$id]['longitud'];	
				$columnas[$id]['no_nulo_db'] = $columnas[$id]['not_null'];	
			}
			return $columnas;
		}catch(toba_error $e){
			toba::notificacion()->agregar( $e->getMessage() );
		}
	}	

}
?>