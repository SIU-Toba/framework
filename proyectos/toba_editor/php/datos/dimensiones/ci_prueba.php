<?php 

/**
		Falta: 
			- Si no hay perfiles, avisar
			- Si no hay lote, avisar
	
*/
class ci_prueba extends toba_ci
{
	protected $fuente;
	protected $s__sqls_elegidos = array();
	protected $s__sqls_a_ejecutar = array();
	protected $s__detalle_test = array();
	protected $cabecera_prueba = '';
	protected $pruebas = array();
	
	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$this->fuente = $editable[1];
			if(! $this->dep('datos')->esta_cargada() ) {
				$this->dep('datos')->cargar(array('fuente_datos'=>$this->fuente));
				$datos = $this->dep('datos')->get();
				if($datos['clave']) {
					$this->s__sqls_elegidos = explode(',',$datos['clave']);					
				}
				if($datos['base']) {
					$this->s__detalle_test = unserialize(stripslashes($datos['base']));					
				}
			}
		}else{
			throw new toba_error('ERROR: Esta operacion debe ser llamada desde la zona de fuentes');
		}	
	}
	
	//-- Obtener salida de la prueba --------------------------------------------
	
	function get_cabecera_prueba()
	{
		return $this->cabecera_prueba;	
	}
	
	function get_pruebas_realizadas()
	{
		return $this->pruebas;
	}

	//-- Elegir SQLs a probar --------------------------------------------

	function evt__form_elegir_sql__modificacion($datos)
	{
		unset($this->s__sqls_elegidos);
		unset($this->s__sqls_a_ejecutar);
		foreach($datos as $id => $dato) {
			if($dato['utilizar']) {
				$this->s__sqls_elegidos[] = $id;
				$this->s__sqls_a_ejecutar[] = trim($dato['sql']);
			}
		}
	}

	function conf__form_elegir_sql(toba_ei_formulario_ml $form_ml)
	{
		$form_ml->set_datos( $this->get_sqls_form() );
	}

	function get_sqls_form()
	{
		$datos = $this->dep('datos')->get();
		$sqls = explode(';',$datos['usuario']);
		$temp = array();
		foreach($sqls as $id => $sql) {
			if($sql) {
				$temp[$id]['sql'] = trim($sql);
				if( in_array($id, $this->s__sqls_elegidos) ) {
					$temp[$id]['utilizar'] = 1;
				} else {
					$temp[$id]['utilizar'] = 0;
				}
			}	
		}
		return $temp;
	}

	//-- Ejecuto pruebas ---------------------------------------------------

	function evt__form_test__modificacion($datos)
	{
		$this->s__detalle_test = $datos;
	}

	function conf__form_test(toba_ei_formulario $form)
	{
		if(isset($this->s__detalle_test)) {
			$form->set_datos($this->s__detalle_test);	
		}
	}

	function get_perfiles_datos()
	{
		$proyecto = toba_editor::get_proyecto_cargado();
		return toba_proyecto_implementacion::get_perfiles_datos($proyecto);
	}

	function evt__ejecutar()
	{
		$this->grabar_info_test();
		$this->ejecutar_test();
		$this->dep('form_elegir_sql')->colapsar();
		$this->dep('form_test')->colapsar();
	}

	function grabar_info_test()
	{
		$temp['clave'] = implode(',',$this->s__sqls_elegidos);
		$temp['base'] = addslashes(serialize($this->s__detalle_test));
		$this->dep('datos')->set($temp);
		$this->dep('datos')->sincronizar();			
	}
	
	function ejecutar_test()
	{
		// Preparo el TEST
		toba::perfil_de_datos()->set_perfil($this->s__detalle_test['perfil_datos']);		
		//----- Cabecera ----------------------------
		$this->cabecera_prueba = 'Perfil de datos: ' . toba::perfil_de_datos()->get_id();
		if ( $this->s__detalle_test['sql_original'] ) {
			$this->cabecera_prueba .= '';
		}
		//----- Detalle del TEST --------------------
		foreach($this->s__sqls_a_ejecutar as $id => $sql) {
			//-[1]- Mostrar el SQL original
			if($this->s__detalle_test['sql_original']) {
				$this->pruebas[$id]['SQL Original'] = $sql;
			}
			//-[2]- Mostrar el SQL modificado
			if($this->s__detalle_test['sql_modificado']) {
				$sql_modif = toba::perfil_de_datos()->filtrar($sql, $this->fuente);
				$this->pruebas[$id]['SQL Modificado'] = $sql_modif;
			}
		}
	}

	//-- Manejo del lote de prueba ---------------------------------------------------

	function evt__form_sql__modificacion($datos)
	{
		$temp['usuario'] = $datos['sql'];
		$temp['clave'] = null;
		$this->dep('datos')->set($temp);
	}

	function conf__form_sql(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->get();
		$temp['sql'] = $datos['usuario'];
		$form->set_datos($temp);		
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
	}
}

?>