<?php 
class ci_prueba extends toba_ci
{
	protected $fuente;
	protected $s__sqls_elegidos = array();
	protected $s__sqls_a_ejecutar = array();
	
	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$this->fuente = $editable[1];
			if(! $this->dep('datos')->esta_cargada() ) {
				$this->dep('datos')->cargar(array('fuente_datos'=>$this->fuente));
				$datos = $this->dep('datos')->get();
				if($datos['administrador']) {
					$this->s__sqls_elegidos = explode(',',$datos['administrador']);					
				}
			}
		}else{
			throw new toba_error('ERROR: Esta operacion debe ser llamada desde la zona de fuentes');
		}	
	}

	//-- Elegir SQLs a probar ------

	function evt__form_elegir_sql__modificacion($datos)
	{
		unset($this->s__sqls_elegidos);
		unset($this->s__sqls_a_ejecutar);
		foreach($datos as $id => $dato) {
			if($dato['utilizar']) {
				$this->s__sqls_elegidos[] = $id;
				$this->s__sqls_a_ejecutar[] = $dato['sql'];
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
		$sqls = explode(';',$datos['instancia_id']);
		$temp = array();
		foreach($sqls as $id => $sql) {
			if($sql) {
				$temp[$id]['sql'] = $sql;
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
	}

	function conf__form_test(toba_ei_formulario $form)
	{
	}

	function evt__ejecutar()
	{
		// Grabo los seleccionados
		$temp['administrador'] = implode(',',$this->s__sqls_elegidos);
		$this->dep('datos')->set($temp);
		$this->dep('datos')->sincronizar();		
		
		// Ejecuto las pruebas
		ei_arbol($this->s__sqls_a_ejecutar);

		// configuro la pantalla
		$this->dep('form_elegir_sql')->colapsar();
	}

	//-- Lote de prueba ---------------------------------------------------

	function evt__form_sql__modificacion($datos)
	{
		$temp['instancia_id'] = $datos['sql'];
		$temp['administrador'] = null;
		$this->dep('datos')->set($temp);
	}

	function conf__form_sql(toba_ei_formulario $form)
	{
		$datos = $this->dep('datos')->get();
		$temp['sql'] = $datos['instancia_id'];
		$form->set_datos($temp);		
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
	}
}

?>