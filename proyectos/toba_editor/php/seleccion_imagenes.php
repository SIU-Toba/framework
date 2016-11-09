<?php
/**
 * Clase estatica que contiene utilerias para extender los formularios y generar el listado
 * de imagenes a elegir en el editor
 */
class seleccion_imagenes
{

	static function generar_input_ef($origen, $img, $objeto_js, $fila='')
	{
		$escapador = toba::escaper();
		$predeterminada = toba_recurso::imagen_toba('image-missing-16.png', false);
		if ($img != '') {
			$actual = admin_util::url_imagen_de_origen($img, $origen);
		} else {
			$actual = $predeterminada;	
		}
		echo '<img nohack=\'1\' title=\'Elegir la imagen desde un listado\' onclick="'. $escapador->escapeHtmlAttr($objeto_js).'.elegir_imagen('. $escapador->escapeHtmlAttr($fila).')"
					id=\''. $escapador->escapeHtmlAttr("editor_imagen_src$fila").'\' src=\'$actual\' onError=\'this.src="'. $escapador->escapeHtmlAttr($predeterminada).'"\' />';		
	}
	
	static function generar_js($objeto_js, $con_fila=false)
	{
		$ir_a_fila = ($con_fila) ? '.ir_a_fila(fila)' : '';
		$mas_fila = ($con_fila) ? '+ fila' : '';
		$id_js =  toba::escaper()->escapeJs($objeto_js);
		echo "
			$id_js.evt__imagen_recurso_origen__procesar = function(inicial, fila) {
				if (! inicial) {
					this.evt__imagen__procesar(inicial, fila);
				}
			}		
		
			$id_js.evt__imagen__procesar = function(inicial, fila) {
				var imagen = this.ef('imagen')$ir_a_fila;
				if (inicial) {
					imagen.input().onkeyup = imagen.input().onblur;
				} else {
					var prefijo = '';
					var origen = this.ef('imagen_recurso_origen')$ir_a_fila.get_estado();
					if (origen == 'apex') {
						prefijo = toba_alias + '/img';
					} else if (origen == 'skin') {
						prefijo = '".admin_util::url_imagen_de_origen('', 'skin')."';
					} else {
						if (toba_proyecto_editado_alias != '') {
							prefijo = toba_proyecto_editado_alias + '/img';
						} else {
							prefijo = 'img';
						}
					}
					var imagen_src = prefijo + '/' + imagen.get_estado();
					$$('editor_imagen_src'$mas_fila).src= imagen_src;
				}
			}
			
			
			$id_js.elegir_imagen = function(fila, recursivo) {
				var callback =
				{
				  success: this.respuesta_listado ,
				  failure: toba.error_comunicacion,
				  scope: this
				}
				this.fila_con_imagen = fila;
				if (! isset(recursivo)) {
					recursivo = 0;
				}
				var parametros = {'imagen': this.ef('imagen')$ir_a_fila.get_estado(),
								  'imagen_recurso_origen': this.ef('imagen_recurso_origen')$ir_a_fila.get_estado(),
								  'recursivo' : recursivo
								};
				var vinculo = vinculador.get_url(null, null, 'ejecutar', parametros);
				conexion.asyncRequest('GET', vinculo, callback, null);
				return true;
			}
			
			$id_js.respuesta_listado = function(resp) {
				notificacion.mostrar_ventana_modal('Seleccione la imagen',
								 resp.responseText, '400px', 'overlay(true)');
				$$('editor_imagen_filtro').focus();
			}
			
			function filtrar_imagenes(actual)
			{
				var tds = $$('editor_imagen_listado').getElementsByTagName('td');
				if (tds) {
					for (var i =0 ; i < tds.length ; i++) {
						if (tds[i].getAttribute('imagen').toLowerCase().indexOf(actual.toLowerCase()) == -1 ) {
							tds[i].style.display = 'none';
						} else {
							tds[i].style.display = '';
						}
					}
				}
			}
			
			function seleccionar_imagen(path) {
				overlay(true);			
				var fila = $id_js.fila_con_imagen;
				$id_js.ef('imagen')$ir_a_fila.set_estado(path);
				$id_js.evt__imagen__procesar(false, fila);
			}
			
			function recargar(recursivo) {
				overlay(true);
				$id_js.elegir_imagen($id_js.fila_con_imagen, recursivo)
			}
		";
	}
	
	static function generar_html_listado()
	{
		toba::memoria()->desactivar_reciclado();
		$escapador = toba::escaper();
		$src = toba::memoria()->get_parametro('imagen');
		$recursivo = toba::memoria()->get_parametro('recursivo');
		$origen = toba::memoria()->get_parametro('imagen_recurso_origen');
		
		$url = admin_util::url_imagen_de_origen('', $origen);
		$dir = admin_util::dir_imagen_de_origen('', $origen);

		echo "<div id='editor_imagen_opciones'>";
		echo "Filtro: <input id='editor_imagen_filtro' onkeyup='filtrar_imagenes(this.value)' type='text' /> ";	
		$checkeado = $recursivo ? 'checked' : '';
		echo "<label><input type='checkbox'  onclick='recargar(this.checked ? 1 : 0)' $checkeado /> Recursivo</label>";
		echo '</div><hr />';
		echo "<div id='editor_imagen_listado'>";
		echo '<table>';
		$temp = toba_manejador_archivos::get_archivos_directorio($dir, '/(.)png|(.)gif|(.)jpg|(.)jpeg/', $recursivo);
		$archivos = array();
		foreach ($temp as $archivo) {
			if (strpos($archivo, '/tabs/') === false) {
				$archivos[] = $archivo;	
			}
		}
		sort($archivos);
		$columnas = 3;
		$cant = 1;
		$total = count($archivos);
		foreach ($archivos as $archivo) {
			if ($cant % $columnas == 1) {
				echo '<tr>';
			}
			$relativo = substr($archivo, strlen($dir) + 1);
			$archivo = basename($relativo);
			echo "<td title='Seleccionar imagen' imagen='". $escapador->escapeHtmlAttr($relativo)."' onclick='seleccionar_imagen(this.getAttribute(\"imagen\"))'>
					<img nohack='1' src='". $escapador->escapeHtmlAttr($url.'/'.$relativo)."' />
					<div>". $escapador->escapeHtml($archivo)."</div>
				</td>\n";
			
			if ($cant % $columnas == 0) {
				echo "</tr>\n";
			}			
			$cant++;
		}
		if ($cant % $columnas != 0) {
			echo "</tr>\n";
		}
		echo '</table></div>';
	}	

	
}

?>