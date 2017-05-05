<?php
require_once('seleccion_imagenes.php');

class form_prop_basicas extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			if (window.toggle_editable) {
				toggle_editable();
			}
			
			{$id_js}.evt__menu__procesar = function() {
				if (this.ef('menu').chequeado())
					this.ef('orden').mostrar();
				else
					this.ef('orden').ocultar();
			}
			
			{$id_js}.evt__zona__procesar = function() {
				if (this.ef('zona').valor() != apex_ef_no_seteado) {
					this.ef('zona_listar').mostrar();
				} else {
					this.ef('zona_listar').ocultar();
				}
				this.evt__zona_listar__procesar();
			}
			
			{$id_js}.evt__zona_listar__procesar = function() {
				if (this.ef('zona_listar').chequeado()) {
					this.ef('zona_orden').mostrar();
				} else {
					this.ef('zona_orden').ocultar();				
				}
			}
			
			
			{$id_js}.evt__solicitud_tipo__procesar = function() {
				var efs_web = [		
								'seccion_web', 'pagina_tipo', 'menu', 'orden', 
								'retrasar_headers', 'imagen_recurso_origen', 'imagen', 'descripcion', 'zona',
								'zona_listar', 'zona_orden'	
							];
					switch (this.ef('solicitud_tipo').get_estado()) {
					case 'accion':
						this.controlador.ocultar_tab('pant_dependencias');
						this.controlador.mostrar_tab('pant_permisos');
						this.ef('accion').mostrar();	
						this.ef('punto_montaje').mostrar();
						this.ef('publico').mostrar();
						for (var i = 0; i < efs_web.length; i++) {
							this.ef(efs_web[i]).ocultar();
						}
						break;
					case 'web':
						this.controlador.mostrar_tab('pant_dependencias');
						this.controlador.mostrar_tab('pant_permisos');											
						this.ef('accion').mostrar();
						this.ef('punto_montaje').mostrar();
						this.ef('publico').mostrar();
						for (var i = 0; i < efs_web.length; i++) {
							this.ef(efs_web[i]).mostrar();
						}
						this.evt__menu__procesar();
						this.evt__zona__procesar();
						this.evt__zona_listar__procesar();
						break;
					case 'servicio_web':
						this.controlador.mostrar_tab('pant_dependencias');
						this.controlador.ocultar_tab('pant_permisos');						
						this.ef('accion').ocultar();
						this.ef('punto_montaje').ocultar();
						this.ef('publico').ocultar();
						for (var i = 0; i < efs_web.length; i++) {
							this.ef(efs_web[i]).ocultar();
						}						
						break;
					case 'consola':
					default:
						this.controlador.ocultar_tab('pant_dependencias');					
						this.controlador.ocultar_tab('pant_permisos');
						this.ef('accion').mostrar();
						this.ef('punto_montaje').mostrar();
						this.ef('publico').ocultar();
						for (var i = 0; i < efs_web.length; i++) {
							this.ef(efs_web[i]).ocultar();
						}						
				}
			}
			
			{$id_js}.evt__punto_montaje__procesar = function(inicial) {
				if (!inicial) {
					this.ef('accion').cambiar_valor('');
				}
			}

			{$id_js}.modificar_vinculo__ef_accion = function(id_vinculo)
			{
				var estado = this.ef('punto_montaje').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
			}
			
			{$id_js}.modificar_vinculo__extender = function(id_vinculo)
			{
				var estado = this.ef('punto_montaje').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
			}
		";
		seleccion_imagenes::generar_js($this->objeto_js);
	}
	
	function generar_input_ef($ef)
	{
		if ($ef == 'imagen') {
			echo "<div class='editor-imagen-preview'>";
			$this->generar_input_ef('imagen_recurso_origen');	
		}
		parent::generar_input_ef($ef);
		if ($ef == 'imagen') {
			$origen = $this->ef('imagen_recurso_origen')->get_estado();
			$img = $this->ef($ef)->get_estado();
			seleccion_imagenes::generar_input_ef($origen, $img, $this->objeto_js);
			echo '</div>';
		} 
	}
	
	protected function generar_html_ef($ef, $ancho_etiqueta=null)
	{
		if ($ef != 'imagen_recurso_origen') {
			parent::generar_html_ef($ef);
		}	
	}
}

class utileria_identificador_nuevo implements toba_ef_icono_utileria 
{
	function get_html(toba_ef $ef) 
	{
		$escapador = toba::escaper();
		$editable = toba_recurso::imagen_toba('objetos/editar.gif', false);		
		$no_editable = toba_recurso::imagen_toba('limpiar.png', false);
		$objeto_js = $ef->objeto_js();
		echo "<script>
			function toggle_editable() {
				var ef = ". $escapador->escapeJs($objeto_js) ."
				if (!ef.input().disabled) {
					ef.input().disabled = true;
					\$\$('utileria_identificador').src = '". $escapador->escapeJs($editable). "';
					\$\$('utileria_identificador').title = 'Editar Identificador';
					ef.set_estado('".id_temporal."');					
				} else {
					ef.input().disabled = false;				
					\$\$('utileria_identificador').src = '". $escapador->escapeJs($no_editable). "';
					\$\$('utileria_identificador').title = 'Resetar Identificador';
					ef.set_estado('');
					ef.seleccionar();					
				}
			}
		</script>";
		$salida = "<a class='icono-utileria' href='#' onclick=\"toggle_editable(); return false\">";
		$salida .= "<img id='utileria_identificador' src='". $escapador->escapeHtmlAttr($editable)."' title='Editar Identificador'>";
		$salida .= '</a>';
		return $salida;		
	}
}

class utileria_identificador_actual implements toba_ef_icono_utileria 
{
	function get_html(toba_ef $ef) 
	{
		$editable = toba_recurso::imagen_toba('objetos/editar.gif', false);		
		$objeto_js = toba::escaper()->escapeJs($ef->objeto_js());
		echo "<script>
			function toggle_editable() {
				var ef = $objeto_js
				if (!ef.input().disabled) {
					ef.input().disabled = true;
					\$\$('utileria_identificador').src = '$editable';
					\$\$('utileria_identificador').title = 'Editar Identificador';
				} else {
					ef.input().disabled = false;				
					\$\$('utileria_identificador').src = '$editable';
					\$\$('utileria_identificador').title = 'Resetar Identificador';
					ef.seleccionar();					
				}
			}
		</script>";
		$salida = "<a class='icono-utileria' href='#' onclick=\"toggle_editable(); return false\">";
		$salida .= "<img id='utileria_identificador' src='$editable' title='Editar Identificador'>";
		$salida .= '</a>';
		return $salida;		
	}
}

?>