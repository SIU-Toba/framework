<?php 
require_once("tutorial/pant_tutorial.php");

class pant_definicion extends pant_tutorial
{
	function generar_layout()
	{
		echo "
			<p>
				SIU-Toba es un Ambiente de Desarrollo Web que utiliza a PHP como lenguaje de programaci�n.
				Lo llamamos <strong>Ambiente</strong> porque es una suite de distintas utilidades:
				<ul>
					<li>Un conjunto de <strong>librer�as</strong> que son consumidas en ejecuci�n. Tambi�n se lo puede llamar <em>framework</em> o <em>runtime</em>.
					<li>Un <strong>editor web</strong> destinado a la definici�n/configuraci�n del proyecto, creaci�n de operaciones y definici�n de su comportamiento.
					<li>Un conjunto de <strong>comandos de consola</strong> destinados a administrar los proyectos creados con la herramienta.
				</ul>
			</p>
			<p>
				Las definiciones realizadas en el editor se las denomina <strong>metadatos</strong> , y junto a las definiciones en c�digo conforman el
				comportamiento del proyecto creado.
			</p>
		";
		$img = toba_recurso::imagen_proyecto('tutorial/esquema_general.png');
		echo "
			<h2>Flujo de trabajo</h2>
			<img src='$img' style='float:right; padding:10px;'>
			<p>
				El flujo de desarrollo con la herramienta podr�a definirse as�:
				<ol>
					<li>Se utiliza el <strong>editor web</strong> para definir una operaci�n, sus pantallas, sus componentes gr�ficos, tablas que se consumen, etc.
					<li>Se define en un editor PHP a elecci�n el c�digo para cubrir l�gica particular de la operaci�n.
					<li>Durante este proceso se va probando la operaci�n desde el mismo <strong>editor web</strong> haciendo ajustes contextuales.
					<li>Una vez terminada se utilizan los <strong>comandos administrativos</strong> para exportar la operaci�n desde el puesto de desarrollo e 
						importarla en el sistema en producci�n
					<li>En el sistema en producci�n el proyecto s�lo necesita las <strong>librer�as</strong> o <em>runtime</em> para ejecutar.
				</ol>
			</p>
		
		";
	}
}

?>