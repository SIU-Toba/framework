<?php 
require_once("tutorial/pant_tutorial.php");

class pant_introduccion extends pant_tutorial
{
	function generar_layout()
	{
		$ciclo = toba_recurso::imagen_proyecto('tutorial/cuadro-ciclo.png');
		$intro = toba_recurso::imagen_proyecto('tutorial/cuadro-intro.png');
		
		echo "
			<div style='float:right;border: 1px solid gray;margin: 10px;background-color:white;'>
				<img src='$intro'>
			</div>			
			<p>
				El cuadro es un elemento de interface (ei) que permite
				visualizar un conjunto de registros en forma de grilla. La grilla esta formada por una serie de filas o registros
				cada uno de ellos dividido en columnas.</p>
			<p>
				Durante la configuraci�n el cuadro se carga con un conjunto de datos y luego cuando vuelve al servidor informa
				si el usuario ha realizado alguna actividad sobre alguno de sus registros.
			</p>
			<img style='clear:both' src='$ciclo' />			
		";
	}
}

class pant_conf_eventos extends pant_tutorial 
{
	function generar_layout()
	{
		$intro = toba_recurso::imagen_proyecto('tutorial/cuadro-intro.png');		
		echo "
			<p>
				En la etapa de configuraci�n es donde el cuadro necesita ser cargado con datos.
				Para esto requiere una estructura del tipo <em>recordset</em> que no es m�s que 
				una matriz filas por columnas, el mismo formato
				que utiliza SQL en las respuestas de las consultas.
				
				un arreglo asociativo id_ef=>estado, se le dice <em>estado</em>
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
function conf__cuadro(toba_ei_cuadro $cuadro)
{
	$datos = array(
		array( "fecha" => "2004-05-20", "importe" => 12500), 
		array( "fecha" => "2004-05-21", "importe" => 22200), 
		array( "fecha" => "2004-05-22", "importe" => 4500),
		array( "fecha" => "2005-05-20", "importe" => 12500),
		array( "fecha" => "2005-05-21", "importe" => 22200),
		array( "fecha" => "2005-05-22", "importe" => 4500)
	);
	$cuadro->set_datos($datos);
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
function evt__cuadro__seleccion($seleccion)
{
	print_r($seleccion);
}
...
Array ( [fecha] => 2005-05-21)
?>';
		echo "
		<p>
			El cuadro tiene la capacidad de enviar eventos relacionados con una fila espec�fica de la grilla,
			por ejemplo la selecci�n con la <em>lupa</em>. En este caso el evento informa la clave de la fila seleccionada:
		</p>
		";
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
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

class pant_filtros extends pant_tutorial 
{
	function generar_layout()
	{
		$filtro = toba_recurso::imagen_proyecto('tutorial/cuadro-filtro.png');
		echo "
			<p>
			Existen situaciones que requieren que el contenido de un cuadro var�e en base a criterios definidos por el usuario.
			Estos criterios se pueden indicar a partir de un componente llamado  <strong>ei_filtro</strong>, cuyo aspecto y comportamiento
			es similar a un formulario. Este filtro ser� el responsable de recolectar los criterios solicitados y dar al <strong>ci</strong> esta informaci�n
			para poder reducir el conjunto de datos a mostrar en el cuadro.
			</p>
			
			<img style='clear:both' src='$filtro' />			
			
			<p>
			Cabe notar que el filtro s�lo es un componente gr�fico y no reduce por s� mismo los datos. En sus eventos indica los datos seleccionados por el cuadro
			y es en el control en donde estos criterios necesitan ser aplicados a los datos. Por ejemplo
			</p>
		";
		
		$codigo = '
<?php
...
	function evt__filtro__filtrar($datos)
	{
		//--- Guarda los criterios del filtro en sesi�n
		$this->s__filtro = $datos;
	}		
		
	function conf__cuadro()
	{
		if (isset($this->s__filtro)) {
			//--- Filtra los datos en base a $this->s__filtro y los retorna
		} else {
			//--- Retorna los datos sin filtrar
		}
	}
...
?>
		';	
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
		
		$vinculo = toba::vinculador()->get_url(null, 1000214, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
			<a target='_blank' href='$vinculo'>Ver Ejemplo</a></p>";			
	}
}

class pant_paginado extends pant_tutorial 
{
	function generar_layout()
	{
		$paginado = toba_recurso::imagen_proyecto('tutorial/cuadro-paginado.png');
		echo "
			<div style='float:right;border: 1px solid gray;margin: 10px;background-color:white;'>
				<img src='$paginado'>
			</div>				
			<h3>Paginado</h3>
			<p>
			Cuando la cantidad de registros a mostrar en el cuadro es muy grande existe la posibilidad de 
			dividir la visualizaci�n en distintasp�ginas. Este paginado permite al usuario avanzar y retroceder entre conjuntos
			de estos registros.
			</p>
			<p>
			La divisi�n en p�ginas la puede hacer el mismo cuadro o hacerla manualmente el programador:
			</p>
				<ul>
					<li>A cargo del cuadro: La divisi�n en p�ginas se produce cuando el cuadro recibe los datos en la configuraci�n.
						En el editor este modo de paginado se lo conoce como <em>Propio</em>.
						Es la opci�n m�s adecuada cuando el conjunto de registros a mostrar es peque�o.
					<li>Manualmente: La divisi�n en p�ginas la realiza el programador desde el CI contenedor. Al
					cuadro s�lo llega los datos de la p�gina actual y la cantidad total de registros. Esta forma es la m�s eficiente
					ya que s�lo se consultan los registros a mostrar.
				</ul>
				
			<h3>Ordenamiento</h3>
			<p>
			El cuadro tambi�n ofrece al usuario la posibilidad de ordenar el conjunto de datos por sus columnas. 
			Al igual que el paginado existen dos posibilidades de ordenamiento:
			</p>
				<ul>
					<li>El CI contenedor. Si as� se decide se debe escuchar el evento evt__idcuadro__ordenar que recibe como parametro un arreglo conteniendo el sentido y la columna del orden. Por ejemplo: array('sentido' => 'asc', 'columna' => 'importe');. Estas opciones deber�an incidir en el mecanismo de recepci�n de datos (t�picamente el ORDER BY de una consulta SQL).
					<li>El mismo cuadro: En caso que el evento no se escuche, el cuadro tomar� la iniciativa de ordenar por s� mismo el set de datos. Para esto debe tener el conjunto completo de datos. Si por ejemplo el cuadro est� p�ginado y s�lo se carga la p�gina actual, el cuadro s�lo podr� ordenar esa p�gina
				</ul>
		";
		
		$vinculo = toba::vinculador()->get_url(null, 1000213, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
			<a target='_blank' href='$vinculo'>Ver Ejemplo</a></p>";			
	}
}

class pant_cortes extends pant_tutorial 
{
	function generar_layout()
	{
		$cortes = toba_recurso::imagen_proyecto('tutorial/cuadro-cortes.png');		
		echo "
			<div style='float:right;border: 1px solid gray;margin: 10px;background-color:white;'>
				<img src='$cortes'>
			</div>
			<p>
			Los <strong>Cortes de Control</strong> en un cuadro permiten agrupar las filas a partir de campos con valores comunes. 
			Su finalidad es parcializar los datos para poder visualizarlos en un modo m�s comprensible, generalmente en un reporte.
			Por ejemplo en un listado de localidades se podr�an agrupar las mismas seg�n a la zona donde pertenecen.
			</p>
			<p>
			Los cortes se definen en el <strong>editor web</strong> seleccionando las columnas que agrupan y la columna que forma 
			la descripci�n de la agrupaci�n.
			</p>
		";
		$vinculo = toba::vinculador()->get_url(null, '1240', array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
			<a target='_blank' href='$vinculo'>Ver Ejemplo</a></p>";		
	}
}

?>