<?php
class pant_descripciones extends toba_ei_pantalla
{
	protected $url;
	
	function generar_layout()
	{
		echo toba_recurso::link_css('tree');
		
		$param = array('ajax-metodo' => 'get_estructura_arbol', 'ajax-modo' => 'D');		
		$opciones = array('servicio' => 'ajax', 'objetos_destino' => array($this->controlador()->get_id()));
		$this->url = toba::vinculador()->get_url(null, null, $param, $opciones, true);
		echo "<ul id='desc_tree' class=\"easyui-tree\"></ul>";
	}
	
	function get_consumo_javascript()
	{
		$consumo_js = parent::get_consumo_javascript();
		$consumo_js[] = 'utilidades/jquery.easyui.min';
		return $consumo_js;
	}
	
	function extender_objeto_js()
	{
		$id = $this->objeto_js;
		echo "
			$(function () {
				$('#desc_tree').tree({
								url:'{$this->url}',
								dnd:false,
								onClick: function (node) {
									 $(this).tree('beginEdit',node.target);
								},
								onAfterEdit: function(node) {
									var param = {'id_nodo':node.id, 'descripcion':node.text};
									$id.ajax('set_descripcion_nodo', param, $id, $id.confirma_edicion);
								}								
				});
			});
			
			$id.confirma_edicion =  function (dato) {
				if (dato != 'OK') {
					notificacion.agregar('Hubo un error, intente editar nuevamente');
				}
			}";
	}	
}

?>