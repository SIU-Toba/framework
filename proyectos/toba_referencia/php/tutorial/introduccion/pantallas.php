<?php 
require_once("tutorial/pant_tutorial.php");

class pant_definicion extends pant_tutorial
{
	function generar_layout()
	{
		echo "
			<p>
				SIU-Toba es un Ambiente de Desarrollo Web que utiliza a PHP como lenguaje de programación.
				Lo llamamos <strong>Ambiente</strong> porque es una suite de distintas utilidades:
				<ul>
					<li>Un conjunto de <strong>librerías</strong> que son consumidas en ejecución. También se lo puede llamar <em>framework</em> o <em>runtime</em>.
					<li>Un <strong>editor web</strong> destinado a la definición/configuración del proyecto, creación de operaciones y definición de su comportamiento.
					<li>Un conjunto de <strong>comandos de consola</strong> destinados a administrar los proyectos creados con la herramienta.
				</ul>
			</p>
			<p>
				Las definiciones realizadas en el editor se las denomina <strong>metadatos</strong> , y junto a las definiciones en código conforman el
				comportamiento del proyecto creado.
			</p>
		";
		$img = toba_recurso::imagen_proyecto('tutorial/esquema_general.png');
		echo "
			<h2>Flujo de trabajo</h2>
			<img src='$img' style='float:right; padding:10px;'>
			<p>
				El flujo de desarrollo con la herramienta podría definirse así:
				<ol>
					<li>Se utiliza el <strong>editor web</strong> para definir una operación, sus pantallas, sus componentes gráficos, tablas que se consumen, etc.
					<li>Se define en un editor PHP a elección el código para cubrir lógica particular de la operación.
					<li>Durante este proceso se va probando la operación desde el mismo <strong>editor web</strong> haciendo ajustes contextuales.
					<li>Una vez terminada se utilizan los <strong>comandos administrativos</strong> para exportar la operación desde el puesto de desarrollo e 
						importarla en el sistema en producción
					<li>En el sistema en producción el proyecto sólo necesita las <strong>librerías</strong> o <em>runtime</em> para ejecutar.
				</ol>
			</p>
		
		";
	}
}

?>