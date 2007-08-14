<?php 
class form_cuadro extends toba_ei_formulario
{
	function generar_layout()
	{
		$this->generar_html_ef('cuadro_eliminar_filas');
		$this->generar_html_ef('cuadro_eof');
		$this->generar_html_ef('sep');
		$this->generar_html_ef('cuadro_carga_origen');
		$this->generar_html_ef('cuadro_carga_php');
		$this->generar_html_ef('cuadro_carga_php_metodo');
		$this->generar_html_ef('cuadro_carga_sql');
	}
	
	protected function generar_input_ef($ef)
	{
		if ($ef == 'cuadro_carga_php_metodo') {
			echo $this->_elemento_formulario[$ef]->get_input();
			$this->generar_input_ef('cuadro_carga_php_metodo_nuevo');				
		} else {
			parent::generar_input_ef($ef);	
		}
	}	

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__cuadro_carga_origen__procesar = function(es_inicial)
		{
			if (this.ef('cuadro_carga_origen').get_estado() == 'consulta_php') {
				this.ef('cuadro_carga_php').mostrar();
				this.ef('cuadro_carga_php_metodo').mostrar();
			} else {
				this.ef('cuadro_carga_php').ocultar();
				this.ef('cuadro_carga_php_metodo').ocultar();
			}
		}
		
		{$this->objeto_js}.evt__cuadro_carga_php_metodo__procesar = function(es_inicial)
		{
			if (this.ef('cuadro_carga_php_metodo').get_estado() == apex_ef_no_seteado) {
				this.ef('cuadro_carga_php_metodo_nuevo').mostrar();
			} else {
				this.ef('cuadro_carga_php_metodo_nuevo').ocultar();
			}
		}		
		";
	}	
}

?>