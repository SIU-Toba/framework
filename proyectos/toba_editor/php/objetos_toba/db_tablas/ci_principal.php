<?php
require_once('objetos_toba/ci_editores_toba.php'); 
/*
	Cosas faltantes:

		- Control de que existan dependencias
		- Borrado de dependencias
		- Validacion de relaciones
*/
class ci_principal extends ci_editores_toba
{
	protected $s__seleccion_relacion;
	protected $s__seleccion_relacion_anterior;
	protected $s__ap_php_db;							// La base posee registro de la existencia de una extension??
	protected $s__ap_php_archivo;						// El archivo de la extension existe en el sistema de archivos??
	protected $clase_actual = 'toba_datos_relacion';

	function ini()
	{
		parent::ini();
		//TODO: Bug ei_esquema con navegacion ajax en windows
		toba_ci::set_navegacion_ajax(false);
	}
	
	function conf()
	{
		parent::conf();
		//Mecanismo para saber si la extension PHP de un AP ya exite en la DB y posee archivo
		if ( !isset($this->s__ap_php_db) ) {
			$this->s__ap_php_db = false;
			$this->s__ap_php_archivo = false;
			if ( $this->componente_existe_en_db() ) {
				$datos_ap = $this->get_entidad()->tabla('prop_basicas')->get();
				if (( $datos_ap['ap'] == 3 ) && $datos_ap['ap_clase'] && $datos_ap['ap_archivo']) {
					$this->s__ap_php_db = true;	//El AP esta extendido
				}
				if (admin_util::existe_archivo_subclase($datos_ap['ap_archivo'])) {
					$this->s__ap_php_archivo = true; //La extension existe
				}
			}
		}
	}

	function evt__procesar()
	{
		//Se retrasa el chequeo de constraints para permitir la modificacion de ident. de dependencias
		$this->get_entidad()->persistidor()->retrasar_constraints();
		parent::evt__procesar();
		unset($this->s__ap_php_db);
		unset($this->s__ap_php_archivo);
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas(toba_ei_formulario $form)
	{
		$param_editor = toba_componente_info::get_utileria_editor_parametros(array('proyecto'=>$this->id_objeto['proyecto'],
																				'componente'=> $this->id_objeto['objeto']),
																			'ap');
		$eliminar_extension = !isset($this->id_objeto); //Si es alta no se puede extender
		if ( $this->s__ap_php_db ) {
			// Hay extension
			$form->evento('ver_php')->vinculo()->set_parametros($param_editor);
			if ( $this->s__ap_php_archivo ) {
				// El archivo de la extension existe
				$abrir = toba_componente_info::get_utileria_editor_abrir_php(array('proyecto'=>$this->id_objeto['proyecto'],
																				'componente'=> $this->id_objeto['objeto']),
																			'ap');
				$form->set_js_abrir($abrir['js']);
				$eliminar_extension = true;
			} else {
				$form->evento('ver_php')->set_imagen('nucleo/php_ap_inexistente.gif');
				$form->eliminar_evento('abrir_php');
				$form->evento('extender_ap')->vinculo()->set_parametros($param_editor);				
			}
		} else {
			$form->eliminar_evento('ver_php');	
			$form->eliminar_evento('abrir_php');
			$form->evento('extender_ap')->vinculo()->set_parametros($param_editor);
		}
		if ($eliminar_extension) {
			$form->eliminar_evento('extender_ap');
		}
		$form->ef('ap_archivo')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
		$form->set_datos($this->get_entidad()->tabla('prop_basicas')->get());
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	//*******************************************************************
	//**  DEPENDENCIAS  *************************************************
	//*******************************************************************

	function conf__dependencias()
	{
		return $this->get_entidad()->tabla('dependencias')->get_filas(null, true);	
	}

	function evt__dependencias__modificacion($datos)
	{
		foreach ($datos as $id => $dep) {
			switch ($dep[apex_ei_analisis_fila]) {
				case 'B':
					// Si se borra una dependencias hay que borrar antes las relaciones donde participa
					$id_dep = $this->get_entidad()->tabla('dependencias')->get_fila_columna($id, 'identificador');					
					$this->eliminar_relaciones_con_dependencia($id_dep);
					break;
				case 'M':
					//Si se modifica el identificador de una dependencia, propagarlo a las relaciones
					$id_anterior = $this->get_entidad()->tabla('dependencias')->get_fila_columna($id, 'identificador');
					$id_nuevo = $dep['identificador'];
					if ($id_anterior != $id_nuevo) {
						$this->modificar_id_en_relaciones($id_anterior, $id_nuevo);
					}
					break;
			}
		}
		
		$this->get_entidad()->tabla('dependencias')->procesar_filas($datos);
	}

	//*******************************************************************
	//**  RELACIONEs  *************************************************
	//*******************************************************************

	function get_tabla_relaciones()
	{	//Abastecimiento al CI de relaciones
		return $this->get_entidad()->tabla('relaciones');	
	}

	function get_lista_tablas()
	{
		$datos = array();
		$tablas = $this->get_entidad()->tabla('dependencias')->get_filas();
		for ($a = 0; $a < count($tablas); $a++) {
			$datos[$a]['objeto'] = $tablas[$a]['identificador']. ',' .$tablas[$a]['objeto_proveedor'];
			$datos[$a]['desc'] = $tablas[$a]['identificador'];
		}
		return $datos;
	}

	function evt__relaciones__salida()
	{
		$this->dependencia('relaciones')->limpiar_seleccion();
	}
	
	/**
	*	Elimina toda relacion que haga referencia a la dependencia 
	*/
	function eliminar_relaciones_con_dependencia($id_dep)
	{
		$relaciones = $this->get_entidad()->tabla('relaciones')->get_filas(array(), true);
		foreach ($relaciones as $id => $relacion) {
			if ($relacion['hijo_id'] == $id_dep || $relacion['padre_id'] == $id_dep) {
				$this->get_entidad()->tabla('relaciones')->eliminar_fila($id);
			}
		}
	}
	
	function modificar_id_en_relaciones($anterior, $nuevo)
	{
		$relaciones = $this->get_entidad()->tabla('relaciones')->get_filas(array(), true);
		foreach ($relaciones as $id => $relacion) {
			if ($relacion['hijo_id'] == $anterior) {
				$this->get_entidad()->tabla('relaciones')->set_fila_columna_valor($id, 'hijo_id', $nuevo);
			}
			if ($relacion['padre_id'] == $anterior) {
				$this->get_entidad()->tabla('relaciones')->set_fila_columna_valor($id, 'padre_id', $nuevo);
			}
		}
	}
	
	//---------------------------------------------------------------------------
	//--------------------    SINCRONIZACION     --------------------------------
	//---------------------------------------------------------------------------	
	
	function conf__opciones()
	{
		return $this->get_entidad()->tabla('prop_basicas')->get();
	}
	
	function evt__opciones__modificacion($datos)
	{
		return $this->get_entidad()->tabla('prop_basicas')->set($datos);
	}
}
?>