<?php
require_once('objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $db_tablas;
	protected $clase_actual = 'objeto_datos_tabla';	
	protected $s__ap_php_db;
	
	function conf()
	{
		parent::conf();
		if ( !isset($this->s__ap_php_db) ) {
			// Flag que indica si la extension de los AP esta registrada en la DB
			//	( Si no esta en la DB no se puede saltar al EDITOR
			if ( $this->componente_existe_en_db() ) {
				$datos_ap = $this->get_entidad()->tabla('prop_basicas')->get();
				if( ( $datos_ap['ap'] == 0 ) && $datos_ap['ap_clase'] && $datos_ap['ap_archivo'] ) {
					$this->s__ap_php_db = true;
				} else {
					$this->s__ap_php_db = false;
				}
			} else {
				$this->s__ap_php_db = false;
			}
		}
	}

	function evt__procesar()
	{
		parent::evt__procesar();
		unset($this->s__ap_php_db);
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas($form)
	{
		if ( $this->s__ap_php_db ) {
			// Incluyo los eventos que permiten abrir y editar archivos
			$parametros = array (apex_hilo_qs_zona => $this->id_objeto['proyecto'] . apex_qs_separador .
														$this->id_objeto['objeto'],
									'subcomponente' => 'ap');
			$form->evento('ver_php')->vinculo()->set_parametros($parametros);
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