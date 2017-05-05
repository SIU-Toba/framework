<?php
class pant_armado extends toba_ei_pantalla
{
	function generar_layout()
	{
		$this->enviar_estilos();
		echo '<table><tr><td>';
		$this->dep('form_armado')->generar_html();
		echo '</td><td><div style=\'height:400px;overflow:auto\'>';
		$this->dep('arbol_origen')->generar_html();		
		echo '</div></td></tr></table>';
	}
	
	function get_consumo_javascript()
	{
		$consumo_js = parent::get_consumo_javascript();
		$consumo_js[] = 'utilidades/jquery-ui.min';
		return $consumo_js;
	}
	
	function extender_objeto_js()
	{			
		echo '$(function() {';
		$this->codigo_generador_subitem();				 
		$this->codigo_generador_item_carpeta();
		echo "	
			//Seteo los elementos del ei_arbol como draggeables
			$('.menu-origen').draggable({
				helper: 'original',
				revert: true});\n";
			
		$this->codigo_accion_zona_dropeo();
		if ($this->controlador()->es_edicion()) {
			$this->generar_codigo_arbol_cliente();
		} 
		
		echo "});\n";
	}
	
	protected function codigo_accion_zona_dropeo()
	{
		//Define las acciones que efectua la dropzone cuando cae un elemento
		echo "
			$('div.menu').droppable({ 
				greedy: true, 
				accept: '.menu-origen', 
				drop: function (event, ui) {
					var	es_carpeta = false,
						\$ui = ui.helper,						
						id_elem = \$ui.attr('id_nodo'),
						nomb_cont = id_elem + '__contenedor';		//Este sera el contenedor del nuevo menu

					var \$ul = $(this).find('ul');						
					if (\$ul.find('#' + nomb_cont).length) {			//Verifica que no exista ya.
							return;
					}	
					
					if (\$ui.find('ul').attr('carpeta')  == 'true') {
						es_carpeta = true;
					}

					//Creo el contenedor para el submenu si llega a existir
					var contenedor = $('<li/>', { class: 'menu-contenedor', 
												  id: nomb_cont
									}).appendTo(\$ul);

					guardar_primer_nivel(id_elem);

					//Elimino el texto de los hijos para obtener solo el primer nivel
					var texto = \$ui.text();
					if (\$ui.find('ul.menu-origen').length) {		//Revisar				
						var texto_hijos = \$ui.find('ul.menu-origen').first().text(),
								  fin = texto.indexOf(texto_hijos);								  
						texto = texto.slice(0, fin);
					}
					
					//Hago un DL para el menu de primer nivel
					var nuevo = newdl(id_elem, texto, es_carpeta);
					//Creo un span que contenga el icono de eliminacion y su accion.
					var spn_ctrl = $('<span/>', {class: 'control'}).append($('<span/>', {class: 'close-btn', text: 'X'})
																.on('click', function() { 
																			var \$this = $(this);
																			var id_elem = \$this.closest('dl').attr('id');
																			eliminar_primer_nivel(id_elem);
																			\$this.closest('li').remove();})
															);					
					nuevo.append(spn_ctrl).appendTo(contenedor);
				}
			}); \n";
	}
	
	
	protected function codigo_generador_item_carpeta()
	{
		//Crea efectivamente el elemento visual que representa a un item de nivel cero o una carpeta
		echo "
			newdl = function (id_elem, texto, es_carpeta) {
				var clase = (es_carpeta)? 'menu-item carpeta' : 'menu-item';
				var nuevo = $('<dl/>', { class: clase, 
								id: id_elem,
								carpeta: es_carpeta});
								
				$('<span/>', {class: 'titulo', text: texto}).appendTo(nuevo);
				
				nuevo.draggable({ helper:'original', revert: 'invalid'});
				nuevo.droppable({							//Se agrega el comportamiento para cuando dropean otro elemento en este.
						greedy:true,
						accept: 'dl.menu-item',
						drop: function (event, ui) {
							var \$ui = ui.helper,
								\$this = $(this);

							if (\$this.attr('carpeta') != 'true') {
								\$ui.draggable({revert: true});
								return;
							} else {
								\$ui.draggable({revert: 'invalid'});
							}

							if (\$ui.find('dt.menu-subitem').length) { 
								\$ui.closest('li')
								   .remove()
								   .end()
								   .remove();
								return;
							}
							//Creo el submenu
							var id_padre = \$this.attr('id'),		//<dl> destino
								id_elem = \$ui.attr('id'),		//<dl> origen														
								dt = newdt(id_elem, \$ui.find('span.titulo').text(), id_padre);	
								
							\$this.append(dt);
							\$ui.closest('li').remove();									
						}
				});
				return nuevo;
			};\n";
	}
	
	protected function codigo_generador_subitem()
	{	//Creo el subitem del elemento que representa una carpeta
		echo "
			newdt = function(id_elem, texto, id_padre) {				
				var dt = $('<dt/>', {
							class: 'menu-subitem',
							id: id_elem});

				$('<span/>', {class: 'titulo', text:texto}).appendTo(dt);
				
				var spn_ctrl = $('<span/>', {class: 'control'});

				$('<span/>', {class: 'close-btn', text:'X'})					//Elemento con icono de eliminacion +  accion
					.on('click', function() { 
						var \$spn = $(this),												
							id_padre = \$spn.closest('dl').attr('id'),		//<dl> padre												
							\$dt = \$spn.closest('dt'),
							id_subitem = \$dt.attr('id');				//<dt> submenu
						\$spn.remove();
						\$dt.remove();
						quitar_subnivel(id_padre, id_subitem);
						})
					.appendTo(spn_ctrl);
				
				dt.append(spn_ctrl);
				agregar_subnivel(id_padre, id_elem);	
				return dt;
			};\n";
	}
	
	protected function generar_codigo_arbol_cliente()
	{
		//Genero el evt para el drop y las funciones que se encargan de ejecutarlo
		echo "	var evt = jQuery.Event('drop');
				var cont = $('div.menu');
				var pos = {left:'-500', top:'-70'}, offs= cont.position();
				var dropfn = cont.droppable('option', 'drop');
				var elem, ui;\n 
				
				simular_drop_item = function(item) {
					var elem = $('li[id_nodo=\"'+item+'\"]');
					var ui = {draggable: elem, helper: elem, position: pos, offset: offs};
				         dropfn.call(cont,evt, ui);\n
				};
				
				simular_drop_carpeta = function(item, padre) {
					var carpeta = $('dl[id=\"' + padre + '\"]');
					var offs= carpeta.position();
					var dropsmfn = carpeta.droppable('option', 'drop');
						   
					var elem = $('dl[id=\"' + item + '\"]');
					var ui = {draggable: elem, helper: elem, position: pos, offset: offs};
					dropsmfn.call(carpeta,evt, ui);\n
				};\n";
		
		//Genero el codigo que dispara los drops y crea la parte visual
		$datos = $this->controlador()->buscar_datos_persistidos();
		$aux_arbol = $this->controlador()->get_arreglo_js();
		$escapador = toba::escaper();
		foreach ($datos as $fila) {
			echo  "simular_drop_item('". $escapador->escapeJs($fila['item']) . "');\n";				
			if ($fila['carpeta'] != 1 && isset($aux_arbol[$fila['padre']])) {
				echo "simular_drop_carpeta('". $escapador->escapeJs($fila['item']) . "', '". $escapador->escapeJs($fila['padre']) . "');\n";
			}
		}			
	}
	
	protected function enviar_estilos()
	{	//TODO:Esto quizas podria estar en el css del proyecto toba_usuarios... analizar!!
		echo '<style>
				.menu {
						position: relative;
						background-color: #FFF;
						float: right;
						padding: 3px;
						border: 1px solid #CCC;
						width: 750px;
						height: 400px;
				}		
				.menu-origen {						
						padding: 2px;
				}		
				.menu-contenedor {
						/*float: left;
						clear: both;*/
						position: relative;
						margin-right: 5px;
				}
				.menu-item {
						border: 1px solid #dfdfdf;
						/*position: relative;*/
						padding: 0 25px 0 15px;
						width:400px;
						height: auto;
						line-height: 30px;
						overflow: hidden;
						word-wrap: break-word;
						background-color: #fafafa;
						-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);
						box-shadow: 0 1px 1px rgba(0,0,0,.04);
						/*float: left;*/						
				}		
				.menu-item.carpeta {
						background-color: #FBEDBF;
				}
				.menu-subitem {
						background-color: #fff;
						padding: 4px;
						border: 1px dashed #dfdfdf;
						font-weight: 600;
						font-size: 13px;
						width: 300px;
						margin-bottom: 10px;
						line-height: 20px;
						position: relative;
				}
				.menu-subitem .titulo {
						display: block;
						margin-right: 22px;
						padding: 0 5px;
				}
				.control {
						font-size: 12px;
						position: absolute;
						right: 5px;
						top: 0;
						display: block;
				}
				.close-btn{
						cursor: pointer;
						position: absolute;
						right: 5px;
						top: 1px;
				}			
			</style>';
	}
}
?>