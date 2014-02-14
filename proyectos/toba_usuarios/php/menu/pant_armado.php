<?php
class pant_armado extends toba_ei_pantalla
{
	function generar_layout()
	{
		$this->enviar_estilos();
		echo '<table><tr><td><div style=\'height:400px;overflow:auto\'>';
		$this->dep('arbol_origen')->generar_html();
		echo '</div></td><td width="80%">';
		$this->dep('form_armado')->generar_html();
		echo '</td></tr></table>';
	}
	
	function get_consumo_javascript()
	{
		$consumo_js = parent::get_consumo_javascript();
		$consumo_js[] = 'utilidades/jquery-ui.min';
		return $consumo_js;
	}
	
	protected function enviar_estilos()
	{
		echo '<style>
				.menu {
						position: relative;
						background-color: grey;
						float: right;
						padding: 3px;
						border: 1px solid;
						width: 750px;
						height: 400px;
				}		
				.menu-origen {						
						padding: 2px;
				}		
				.menu-contenedor {
						background-color: #11F247;
						border: 1px solid;
						width: 100px;
						float:left;
				}			
				.menu-item{
						padding: 5px;
						border: 1px solid;
						width: 90px;
						height: 100%;			
						position: relative;
						float: left;
				}		
				.menu-subitem{
						background-color: #FF4247;
						padding: 2px;
						border: 1px solid;
						width: 50px;			
				}
				.close-btn{
						float:right;
						cursor: pointer;
				}			
			</style>';
	}
	
	function extender_objeto_js()
	{			
		echo "
		$(function() {			
			newdt = function(id_elem, texto, id_padre) {				
				var dt = $('<dt/>', {
							class: 'menu-subitem',
							text: texto,
							id: id_elem, 
							top: '10px', 
							left: '50px'});

				$('<span/>', {class: 'close-btn', text:'X'})
					.on('click', function() { 
						var \$spn = $(this),												
							id_padre = \$spn.closest('dl').attr('id'),	//<dl> padre												
							\$dt = \$spn.closest('dt'),
							id_subitem = \$dt.attr('id');				//<dt> submenu
						\$dt.remove();
						\$spn.remove();
						quitar_subnivel(id_padre, id_subitem);
						})
					.appendTo(dt);
				
				agregar_subnivel(id_padre, id_elem);	
				return dt;
			}

			newdl = function (id_elem, texto, es_carpeta) {
				var nuevo = $('<dl/>', { class: 'menu-item', 
								text: texto,
								id: id_elem,
								top: '10px',
								left: '70px',
								carpeta: es_carpeta});

				nuevo.draggable({ helper:'original', revert: 'invalid'});
				nuevo.droppable({
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
							var id_padre = \$this.attr('id'),	//<dl> destino
								id_elem = \$ui.attr('id'),		//<dl> origen														
								dt = newdt(id_elem, \$ui.text(), id_padre);	
								
							\$this.append(dt);
							\$ui.closest('li').remove();									
						}
				});
				return nuevo;
			};

			$('.menu-origen').draggable({
				helper: 'original',
				revert: true});
				
			$('div.menu').droppable({ 
				greedy: true, 
				accept: '.menu-origen', 
				drop: function (event, ui) {
					var	es_carpeta = false,
						\$ui = ui.helper,						
						id_elem = \$ui.attr('id_nodo'),
						nomb_cont = id_elem + '__contenedor';		//Este sera el contenedor del nuevo menu

					var \$ul = $(this).find('ul');						
					if (\$ul.find('#' + nomb_cont).length) {			//No agrego uno nuevo del mismo
							return;
					}	

					//Tengo que ver si es una carpeta o un item final usar .data para recuperar los atributos, sino es medio de gusto
					if (\$ui.find('ul').attr('carpeta')  == 'true') {
						es_carpeta = true;
					}

					//Creo un contenedor para todos los submenues
					var contenedor = $('<li/>', { class: 'menu-contenedor', 
												  id: nomb_cont
									}).appendTo(\$ul);

					guardar_primer_nivel(id_elem);

					//Elimino el texto de los hijos para obtener solo el primer nivel
					var texto = \$ui.text();
					if (\$ui.find('ul.menu-origen').length) {				//Revisar
						var texto_hijos = \$ui.find('ul.menu-origen').text(),
								  fin = texto.indexOf(texto_hijos);								  
						texto = texto.slice(0, fin);
					}
					
					//Hago un DL para el menu de primer nivel
					var nuevo = newdl(id_elem, texto, es_carpeta);			
					contenedor
						.append($('<span/>', {class: 'close-btn', text: 'X'})
							.on('click', function() { 
										var \$this = $(this);
										var id_elem = \$this.next().attr('id');
										eliminar_primer_nivel(id_elem);
										\$this.closest('li').remove();}))
						.append(nuevo);
				}
			});				
		});";		
	}
}
?>