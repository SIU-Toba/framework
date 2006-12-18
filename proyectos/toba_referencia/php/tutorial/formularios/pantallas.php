<?php
require_once("tutorial/pant_tutorial.php");

//--------------------------------------------------------
class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		$intro = toba_recurso::imagen_proyecto('tutorial/form-intro.png');
		
		echo "
			<div style='float:right;border: 1px solid gray;margin: 10px;background-color:white;'>
				<img src='$intro'>
			</div>			
			<p>
				El formulario es un elemento de interface (ei) que permite
				incluir grillas de campos o elementos de formularios (efs).
			</p>
			<p>
			</p>
				<ul>
					<li>Durante la configuración, el CI le carga un conjunto de datos con 
						el cual se grafica en el navegador.
					<li>El usuario edita el formulario, interactuando con la clase formulario y ef en javascript
					<li>Cuando vuelve al server se notifica los nuevos datos del formulario a partir de un evento.
				</ul>

		";
	}
}

//--------------------------------------------------------
class pant_tipos extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			<h3>El usuario selecciona un elemento</h3>
			<table class='listado-efs'>			
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/checkbox.png',true)."</td>
					<td><strong>ef_checkbox</strong>: Selección entre dos opciones, generalmente 0-1
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/combo.png',true)."</td>
					<td><strong>ef_combo</strong>: Selección entre varias opciones, pensado para conjuntos 
						medianos de datos cuyos elementos son fáciles de encontrar por nombre.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/radio.png',true)."</td>
					<td><strong>ef_radio</strong>: Selección entre varias opciones, pensado para conjuntos 
								pequeños de datos, la elección es más explícita que en el combo, aunque
								ocupa mucho espacio como para poner muchas opciones.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/popup.png',true)."</td>
					<td><strong>ef_popup</strong>: Al presionarlo, la elección entre las distintas opciones
						se realiza en una ventana aparte, en una operación separada. Pensado para conjuntos grandes 
						con métodos de búsqueda complejos. La recomendación es usarlo sólo en casos
						justificados, ya que el combo o el radio brindan en general una mejor experiencia al usuario.
					</td>
				</tr>				
			</table>
			
			
			<h3>El usuario selecciona varios elementos</h3>
			<table class='listado-efs'>			
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/multi_select.png',true)."</td>
					<td><strong>ef_multi_seleccion_lista</strong>: Selección usando el componente clásico HTML, 
							difícil de entender para usuarios novatos ya que requiere presionar la tecla control o shift 
							para hacer selecciones personalizadas.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/multi_doble.png',true)."</td>
					<td><strong>ef_multi_seleccion_doble</strong>: Selecciona los elementos cruzandolo de un lado al otro.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/multi_checkbox.png',true)."</td>
					<td><strong>ef_multi_seleccion_check</strong>: Selecciona los elementos tildando checkboxes
					</td>
				</tr>				
			</table>			
			
			
			<h3>El usuario edita</h3>
			<table class='listado-efs'>			
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/editable.png',true)."</td>
					<td><strong>ef_editable</strong>: El usuario edita texto libremente, respentando máximos.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/numero.png',true)."</td>
					<td><strong>ef_editable_numero</strong>: El usuario edita un número, respetando límites mínimos y máximos.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/moneda.png',true)."</td>
					<td><strong>ef_editable_moneda</strong>: Igual al número, sólo que tiene una máscara que pone la moneda y tiene 
											límites predefinidos.
					</td>
				</tr>				
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/porcentaje.png',true)."</td>
					<td><strong>ef_editable_numero_porcentaje</strong>: Número que representa un porcentaje.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/fecha.png',true)."</td>
					<td><strong>ef_editable_fecha</strong>: El usuario ingresa una fecha, ayudado con un calendario.
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/textarea.png',true)."</td>
					<td><strong>ef_editable_textarea</strong>: El usuario edita múltiples líneas de texto libremente, sin formato.
					</td>
				</tr>
			</table>
		";
	}
}

//--------------------------------------------------------

class pant_definicion extends pant_tutorial 
{
	function generar_layout()
	{

	}
}

//--------------------------------------------------------
class pant_opciones extends pant_tutorial 
{
	function generar_layout()
	{

	}
}

//--------------------------------------------------------
class pant_popup extends pant_tutorial 
{
	function generar_layout()
	{

	}
}


//--------------------------------------------------------

class pant_masinfo extends pant_tutorial 
{
	function generar_layout()
	{
		$wiki1 = toba_parser_ayuda::parsear_wiki('Referencia/Objetos/ei_formulario', 
													'Formulario simple',
													'toba_editor');
		$wiki2 = toba_parser_ayuda::parsear_wiki('Referencia/Objetos/ei_formulario_ml', 
													'Formulario multilínea (ml)',
													'toba_editor');													
		$wiki3 = toba_parser_ayuda::parsear_wiki('Referencia/efs', 
													'Elementos de formularios (efs)',
													'toba_editor');													
		$api1 = toba_parser_ayuda::parsear_api('Componentes/Eis/toba_ei_formulario',
												 'Primitivas del ei_formulario', 'toba_editor');
		$api2 = toba_parser_ayuda::parsear_api('Componentes/Eis/toba_ei_formulario_ml',
												 'Primitivas del ei_formulario_ml', 'toba_editor');
		$api3 = toba_parser_ayuda::parsear_api('Componentes/Efs/toba_ef',
												 'Primitivas de los efs', 'toba_editor');

		echo "
			<ul>
				<li>$wiki1
				<li>$wiki2
				<li>$wiki3
				<li style='padding-top: 10px'>$api1
				<li>$api2	
				<li>$api3
			</ul>
		";
	}
}

?>