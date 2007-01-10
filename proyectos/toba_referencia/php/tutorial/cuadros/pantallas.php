<?php 

class pant_introduccion extends toba_ei_pantalla
{
	function generar_layout()
	{
		$intro = toba_recurso::imagen_proyecto('tutorial/form-intro.png');
		$ciclo = toba_recurso::imagen_proyecto('tutorial/cuadro-ciclo.png');
		
		echo "
			<p>
				El cuadro es un elemento de interface (ei) que permite
				visualizar un conjunto de registros en forma de grilla.
				Durante la configuración se lo carga con un conjunto de datos y luego cuando vuelve al servidor informa
				si el usuario ha realizado alguna selección sobre alguno de sus registros.
			</p>
			<img style='clear:both' src='$ciclo' />			
			<p>
				Para cargar al cuadro con datos durante la configuración es necesario formar una estructura
				llamada <em>recordset</em> que no es más que una matriz filas por columnas, el mismo formato
				que utiliza SQL en las respuestas de las consultas.
				
				un arreglo asociativo id_ef=>estado, se le dice <em>estado</em>
				al valor que toma el ef actualmente, independientemente de su formato.
				Por ejemplo para cargar el formulario de la imagen:
				
			</p>
			<div style='float:right;border: 1px solid gray;margin: 10px;background-color:white;'>
				<img src='$intro'>
			</div>						
		";
	}

}

?>