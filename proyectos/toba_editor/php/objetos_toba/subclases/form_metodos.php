<?php
	
class separador_con_js extends toba_ef_barra_divisora 	
{
	function get_input()
	{
		$escapador = toba::escaper();
		echo "<div class='". $escapador->escapeHtmlAttr($this->clase_css)."' id='". $escapador->escapeHtmlAttr($this->id_form)."'>". $this->etiqueta ."</div>\n";
	}
}
	
class form_metodos extends toba_ei_formulario
{	
	protected function crear_elementos_formulario()
	{
		$this->_lista_ef = array();
		for($a=0; $a<count($this->_info_formulario_ef); $a++)
		{
			//-[1]- Separa los efs segun su tipo en varias listas.
			$id_ef = $this->_info_formulario_ef[$a]['identificador'];
			$es_separador = (substr($id_ef, 0, 4) == 'sep_');
			$this->separar_listas_efs($id_ef, $this->_info_formulario_ef[$a]['elemento_formulario']);
			
			//Preparo el identificador del dato que maneja el EF.
			$dato = $this->clave_dato_multi_columna($this->_info_formulario_ef[$a]['columnas']);
									
			$parametros = $this->_info_formulario_ef[$a];
			if (isset($parametros['carga_sql']) && !isset($parametros['carga_fuente'])) {
				$parametros['carga_fuente']=$this->_info['fuente'];
			}
			$this->_parametros_carga_efs[$id_ef] = $parametros;
			
			//Nombre	del formulario.
			$clase_ef = (! $es_separador) ? 'toba_'.$this->_info_formulario_ef[$a]['elemento_formulario'] : 'separador_con_js';			
			$this->instanciar_ef($id_ef, $clase_ef, $a, $dato, $parametros);
		}
		//--- Se registran las cascadas porque la validacion de efs puede hacer uso de la relacion maestro-esclavo
		$this->_carga_opciones_ef = new toba_carga_opciones_ef($this, $this->_elemento_formulario, $this->_parametros_carga_efs);
		$this->_carga_opciones_ef->registrar_cascadas();
	}
	
	function extender_objeto_js()
	{
		echo "
			function cambiar_grupo(id_grupo, estado) {
				var form = ". toba::escaper()->escapeJs($this->objeto_js).";
				var efs = form.efs();
				for (var i in efs) {
					if (i.indexOf(id_grupo) != -1) {
						efs[i].chequear(estado);
					}	
				}				
			}
		";
	}
}
?>