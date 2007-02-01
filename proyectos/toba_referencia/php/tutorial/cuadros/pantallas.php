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
				Durante la configuración el cuadro se carga con un conjunto de datos y luego cuando vuelve al servidor informa
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
				En la etapa de configuración es donde el cuadro necesita ser cargado con datos.
				Para esto requiere una estructura del tipo <em>recordset</em> que no es más que 
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
			El cuadro tiene la capacidad de enviar eventos relacionados con una fila específica de la grilla,
			por ejemplo la selección con la <em>lupa</em>. En este caso el evento informa la clave de la fila seleccionada:
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
			Existen situaciones que requieren que el contenido de un cuadro varíe en base a criterios definidos por el usuario.
			Estos criterios se pueden indicar a partir de un componente llamado  <strong>ei_filtro</strong>, cuyo aspecto y comportamiento
			es similar a un formulario. Este filtro será el responsable de recolectar los criterios solicitados y dar al <strong>ci</strong> esta información
			para poder reducir el conjunto de datos a mostrar en el cuadro.
			</p>
			
			<img style='clear:both' src='$filtro' />			
			
			<p>
			Cabe notar que el filtro sólo es un componente gráfico y no reduce por sí mismo los datos. En sus eventos indica los datos seleccionados por el cuadro
			y es en el control en donde estos criterios necesitan ser aplicados a los datos. Por ejemplo
			</p>
		";
		
		$codigo = '
<?php
...
	function evt__filtro__filtrar($datos)
	{
		//--- Guarda los criterios del filtro en sesión
		$this->s__filtro = $datos;
	}		
		
	function conf__cuadro()
	{
		if (! isset($this->s__filtro) ) {
			//--- Retorna los datos sin filtrar
		} else {
			//--- Filtra los datos en base a $this->s__filtro y los retorna
		}
	}
...
?>
		';	
		echo "<div class='codigo'>";
		highlight_string($codigo);
		echo "</div>";	
		
		$vinculo = toba::vinculador()->crear_vinculo(null, '/objetos/ei_filtro', array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
			<a target='_blank' href='$vinculo'>Ver Ejemplo</a></p>";			
	}
}

class pant_paginado extends pant_tutorial 
{
	function generar_layout()
	{
		echo "
			Cuando la cantidad de registros a mostrar en el cuadro es muy grande está la posibilidad de 
			dividir la visualización en páginas. Este paginado permite al usuario avanzar y retroceder entre conjuntos
			de estos registros.
		";
		
		$vinculo = toba::vinculador()->crear_vinculo(null, '/objetos/ei_cuadro', array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
			<a target='_blank' href='$vinculo'>Ver Ejemplo</a></p>";			
	}
}

?>