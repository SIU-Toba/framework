<?php
require_once("tutorial/pant_tutorial.php");

//--------------------------------------------------------
class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		$intro = toba_recurso::imagen_proyecto('tutorial/form-intro.png');
		$ciclo = toba_recurso::imagen_proyecto('tutorial/form-ciclo.png');
		
		echo "
			<p>
				El formulario es un elemento de interface (ei) que permite
				incluir grillas de campos o elementos de formularios (efs). Durante la configuración
				se lo carga con un conjunto de datos y luego cuando vuelve al servidor informa
				a través de sus eventos el nuevo conjunto de datos editado por el usuario.
			</p>
			<img style='clear:both' src='$ciclo' />			
			<p>
				La forma de carga del formulario es un arreglo asociativo id_ef=>estado, se le dice <em>estado</em>
				al valor que toma el ef actualmente, independientemente de su formato.
				Por ejemplo para cargar el formulario de la imagen:
				
			</p>
			<div style='float:right;border: 1px solid gray;margin: 10px;background-color:white;'>
				<img src='$intro'>
			</div>						
		";
		$codigo = '
<?php
...
function conf__form(toba_ei_formulario $form)
{
	$datos = array(
		"fecha" => "2006-12-11",
		"editable" => "Texto",
		"moneda" => "234.23",
		"cuit" => "202806293",
		....
	);
	$form->set_datos($datos);
}
...
?>
		';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";
		$codigo = '
<?php
...
function evt__form__modificacion($datos)
{
	print_r($datos);
}
...
Array ( [fecha] => 2006-12-11 [editable] => Texto [moneda] => 234.23 [cuit] => 202806293 )
?>';
		echo "
		<p>
			Los datos tienen el mismo formato cuando se disparan los eventos:
		</p>
		";
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
	}
}

//--------------------------------------------------------
class pant_tipos extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			Los distintos tipos de elementos de formularios se pueden clasificar según la acción que 
			el usuario realiza sobre ellos:
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
						<a target='_blank' href='".toba::vinculador()->get_url(null, 1000096, array(), array('celda_memoria'=>'ejemplo'))."
						'>Ver Ejemplo.</a>
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/popup.png',true)."</td>
					<td><strong>ef_popup</strong>: Al presionarlo, la elección entre las distintas opciones
						se realiza en una ventana aparte, en una operación separada. Pensado para conjuntos grandes 
						con métodos de búsqueda complejos. La recomendación es usarlo sólo en casos
						justificados, ya que el combo o el radio brindan en general una mejor experiencia al usuario.
						<a target='_blank' href='".toba::vinculador()->get_url(null, 1000048, array(), array('celda_memoria'=>'ejemplo'))."
						'>Ver Ejemplo.</a>
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
					<td><strong>ef_multi_seleccion_check</strong>: Selecciona los elementos tildando checkboxes.
						<a target='_blank' href='".toba::vinculador()->get_url(null, 1000096, array(), array('celda_memoria'=>'ejemplo'))."
						'>Ver Ejemplo.</a>
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
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/cuit.png',true)."</td>
					<td><strong>ef_cuit</strong>: El usuario ingresa un número de CUIT/CUIL
					</td>
				</tr>				
			</table>
			
			<h3>Otras acciones</h3>
			<table class='listado-efs'>			
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/upload.png',true)."</td>
					<td><strong>ef_upload</strong>: El usuario selecciona un archivo de su sistema para que esté disponible en el servidor.
						<a target='_blank' href='".toba::vinculador()->get_url(null, 1000202, array(), array('celda_memoria'=>'ejemplo'))."
						'>Ver Ejemplo.</a>
					</td>
				</tr>
				<tr>
					<td class='img-ef'>".toba_recurso::imagen_proyecto('tutorial/efs/fijo.png',true)."</td>
					<td><strong>ef_fijo</strong>: El usuario observa un contenido estático
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
		$opciones = toba_recurso::imagen_proyecto('tutorial/form-opciones.png');
		echo "
			<p>
				De los distintos tipos de efs disponibles existen los llamados de <strong>selección</strong>,
				estos permiten seleccionar su <strong>estado</strong> a partir de un conjunto de <strong>opciones</strong>.
			<p>
			<p>
				La carga de los estados se vio anteriormente, se da durante la configuración del componente. La
				carga de opciones se puede realizar a partir distintos mecanismos, dependiendo de cada tipo de ef. Por ejemplo el ef_combo
				posee los siguientes mecanismos:
			</p>
			<ul>
				<li>Lista de opciones: Las opciones son estáticas y se definen en el mismo editor.
				<li>Consulta SQL: Las opciones provienen de una consulta que se especifica en el mismo editor.
				<li>Método PHP: Las opciones surgen de la respuesta de un método de una clase PHP.
			</ul>
			<p>
				La forma recomendada es utilizar la carga por Método PHP, para esto se necesita:
                    <ul>
                    <li>Definir un método que retorne el conjunto de opciones disponibles en el combo</li>
                    <li>Indicar en el editor-web que las opciones del combo se cargan con este método</li>
                    </ul>
			</p>
		";
		$codigo = 
"<?php 
function alumnos_disponibles()
{
    return array(
        array('id' => 100, 'nombre' => 'Juan Perez'),
        array('id' => 142, 'nombre' => 'Cristian Fernandez'),
        .....
    );
?>";	
		echo "
			<div style='float:right;border: 1px solid gray;margin: 10px;background-color:white;'>
				<img src='$opciones'>
			</div>";
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
		
	}
}

//--------------------------------------------------------
class pant_ml extends pant_tutorial 
{
	function generar_layout()
	{
		$ml = toba_recurso::imagen_proyecto('tutorial/form-ml.png');
		echo "
			<div style='float: right' class='screenshot'>
			<img src='$ml' />
			</div>
			<p>
			Un formulario ML (multilínea)  permite editar varios registros con la misma estructura.
			La definición y uso de la grilla de campos es similar al  formulario simple con el agregado de lógica para manejar un número arbitrario de filas.
			</p>
			<p>
			Este formulario puede tener un número fijo de filas o puede permitir que el usuario cree nuevas. La creación 
            puede ser dinámica (utilizando javascript) o haciendo un pedido de página completo al server.
			</p>
			<p>
				La forma que toman los datos, tanto en la carga como en el disparo de eventos es similar al del formulario simple
				sólo que se encuentra dentro de un arreglo que representa las distintas filas:
			</p>
		";
		$codigo ='
<?php
    function conf__form($form_ml)
    {
		$datos = array(
			array("id" => 1, "importe" => 100),
			array("id" => 3, "importe" => 412),
			...
		);
		$form_ml->set_datos($datos);
    }
    
    function evt__form__modificacion($datos)
    {
        //$datos es un arreglo de filas, donde cada una contiene arreglo ef=>valor
    }    
?>
';
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";
		
		echo "
			<p>
			Al contener registros, los formularios ML  heredan funcionalidades que son propias de un cuadro.
			Una de ellas es la capacidad de definir eventos a nivel de fila, como
			por ejemplo la selección particular de una fila. En este caso al método que escucha
			el evento se le reporta el número de fila seleccionada.
			</p>
		";	
		$codigo = '
<?php
    function evt__form__seleccion($fila)
    {
        var_dump($fila);
    }
?>
int(1534)		
';		
		
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";
		
		$vinculo = toba::vinculador()->get_url(null, 1000216, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ver Ejemplo de Formulario ML</a></p>";
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
