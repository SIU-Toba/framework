<?php
/**
 * Clase estatica que contiene utilerias para extender los formularios y generar el listado
 * de imagenes a elegir en el editor
 */
class seleccion_imagenes
{

	static function generar_input_ef($origen, $img, $objeto_js, $fila ='')
	{
		$predeterminada = toba_recurso::imagen_toba('image-missing-16.png', false);
		if ($img != '') {
			if ($origen == 'apex') {
				$actual = toba_recurso::imagen_toba($img);
			} else {
				$actual = toba_recurso::url_proyecto(toba_editor::get_proyecto_cargado());
				if ($actual != '') {
					$actual .= '/';
				}
				$actual .= "img/$img";
			}
		} else {
			$actual = $predeterminada;	
		}
			echo "<img title='Elegir la imagen desde un listado' onclick='$objeto_js.elegir_imagen($fila)'
					 id='editor_imagen_src$fila' src='$actual' onError='this.src=\"$predeterminada\"'/>";		
	}
	
	static function generar_js($objeto_js, $con_fila=false)
	{
		$ir_a_fila = ($con_fila) ? '.ir_a_fila(fila)' : '';
		$mas_fila = ($con_fila) ? '+ fila' : '';
		echo "
			$objeto_js.evt__imagen_recurso_origen__procesar = function(inicial, fila) {
				if (! inicial) {
					this.evt__imagen__procesar(inicial, fila);
				}
			}		
		
			$objeto_js.evt__imagen__procesar = function(inicial, fila) {
				var imagen = this.ef('imagen')$ir_a_fila;
				if (inicial) {
					imagen.input().onkeyup = imagen.input().onblur;
				} else {
					var prefijo = '';
					if (this.ef('imagen_recurso_origen')$ir_a_fila.get_estado() == 'apex') {
						prefijo = toba_alias + '/';
					} else {
						if (toba_proyecto_editado_alias != '') {
							prefijo = toba_proyecto_editado_alias + '/';
						}
					}
					var imagen_src = prefijo + 'img/' + imagen.get_estado();
					$('editor_imagen_src'$mas_fila).src= imagen_src;
				}
			}
			
			
			$objeto_js.elegir_imagen = function(fila) {
				var callback =
				{
				  success: this.respuesta_listado ,
				  failure: toba.error_comunicacion,
				  scope: this
				}
				this.fila_con_imagen = fila;
				var parametros = {'imagen': this.ef('imagen')$ir_a_fila.get_estado(),
								  'imagen_recurso_origen': this.ef('imagen_recurso_origen')$ir_a_fila.get_estado()  };
				var vinculo = vinculador.crear_autovinculo('ejecutar', parametros);
				conexion.asyncRequest('GET', vinculo, callback, null);
				return true;
			}
			
			$objeto_js.respuesta_listado = function(resp) {
				notificacion.mostrar_ventana_modal('Seleccione la imagen',
								 resp.responseText, '400px', 'overlay(true)');
				$('editor_imagen_filtro').focus();
			}
			
			function filtrar_imagenes(actual)
			{
				var tds = $('editor_imagen_listado').getElementsByTagName('td');
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
				var fila = $objeto_js.fila_con_imagen;
				$objeto_js.ef('imagen')$ir_a_fila.set_estado(path);
				$objeto_js.evt__imagen__procesar(false, fila);
			}
		";
	}
	
	static function generar_html_listado()
	{
		toba::memoria()->desactivar_reciclado();
		$src = toba::memoria()->get_parametro('imagen');
		$origen = toba::memoria()->get_parametro('imagen_recurso_origen');
		
		if ($origen == 'apex') {
			$dir = toba::instalacion()->get_path().'/www/img';	
			$url = toba_recurso::url_toba();
		} else {
			$cargado = toba_editor::get_proyecto_cargado();
			$dir = toba::instancia()->get_path_proyecto($cargado)."/www/img";
			$url = toba_recurso::url_proyecto($cargado);
		}
		echo "<div id='editor_imagen_opciones'>";
		echo "Filtro: <input id='editor_imagen_filtro' onkeyup='filtrar_imagenes(this.value)' type='text' /> ";	
		echo "<label><input type='checkbox' /> Recursivo</label>";
		echo "</div><hr>";
		echo "<div id='editor_imagen_listado'>";
		echo "<table>";
		$temp = toba_manejador_archivos::get_archivos_directorio($dir, '/(.)png|(.)gif|(.)jpg|(.)jpeg/', true);
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
				echo "<tr>";
			}
			$relativo = substr($archivo, strlen($dir)+1);
			$archivo = basename($relativo);
			echo "<td title='Seleccionar imagen' imagen='$relativo' onclick='seleccionar_imagen(this.getAttribute(\"imagen\"))'>
					<img  src='".$url."/img/".$relativo."' />
					<div>$archivo</div>
				</td>\n";
			
			if ($cant % $columnas == 0) {
				echo "</tr>\n";
			}			
			$cant++;
		}
		if ($cant % $columnas != 0) {
			echo "</tr>\n";
		}
		echo "</table></div>";
	}	

	
}

?>