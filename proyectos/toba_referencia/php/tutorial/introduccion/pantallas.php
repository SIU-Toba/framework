<?php 
require_once("tutorial/pant_tutorial.php");
require_once('nucleo/lib/toba_parser_ayuda.php');

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
			<h3>Flujo de trabajo</h3>
			<img src='$img' style='float:right; padding:10px;'>
			<p>
				El flujo de desarrollo con la herramienta podr�a definirse as�:
				<ol>
					<li>Se utiliza el <strong>editor web</strong> de toba para definir una operaci�n, sus pantallas, 
							sus componentes gr�ficos, tablas que se consumen, etc. Todo esto se almacena en metadatos en una base de datos.
					<li>Se utiliza un <strong>editor PHP</strong> a elecci�n el c�digo para cubrir l�gica particular de la operaci�n.
					<li>Durante este proceso se va probando la operaci�n desde el mismo <strong>editor web</strong> haciendo ajustes contextuales.
					<li>Una vez terminada se utilizan los <strong>comandos administrativos</strong> para exportar el proyecto 
						desde el puesto de desarrollo e importarlo en el sistema en producci�n.
					<li>En el sistema en producci�n s�lo necesita las <strong>librer�as</strong> o <em>runtime</em>
						 para ejecutar el proyecto (c�digo + metadatos).
				</ol>
			</p>
		
		";
	}
}

class pant_directorios extends pant_tutorial 
{
	function generar_layout()
	{
		$comandos = toba_parser_ayuda::parsear_wiki('Referencia/Consola', 'comandos de consola', 'toba_editor');
		$wiki = toba_parser_ayuda::parsear_wiki('WikiStart', 'wiki', 'toba_editor');
		$puntos_de_acceso = toba_parser_ayuda::parsear_wiki('Referencia/PuntosDeAcceso', 'puntos de acceso', 'toba_editor');
		$api = toba_parser_ayuda::parsear_api('index', 'se encuentra publicada', 'toba_editor');
		$proyectos = toba_parser_ayuda::parsear_wiki('Proyectos#Internos', 'proyectos propios', 'toba_editor');
		echo " 
			<p>
			Para familizar m�s con el ambiente, vamos a presentar su estructura de directorios y una breve descripci�n de sus funciones:
			<ul class='lista-separada'>
				<li><strong>bin</strong>: Contiene la puerta de entrada a los $comandos, 
						a este directorio es necesario incluirlo en el PATH del sistema operativo.
						
				<li><strong>doc</strong>: La mejor documentaci�n del proyecto se encuentra en el $wiki y en este tutorial.
				
				<li><strong>instalacion</strong>: Contiene toda la configuraci�n local (base que se utiliza, proyectos que se editan, alias de apache, etc.)
													y los metadatos locales (logs, usuarios, etc.).
				
				<li><strong>php</strong>
					<ul>
						<li><strong>3ros</strong>: Librer�as de 3ros utilizadas en el proyecto.
						
						<li><strong>consola</strong>: C�digo fuente de los comandos administrativos de consola.
						
						<li><strong>contrib</strong>: C�digo contribuido por los proyectos, que a�n no pertenecen al n�cleo 
													pero que esta bueno	compartir.
						
						<li><strong>lib</strong>: Clases sueltas propias comunes a todo el ambiente
						
						<li><strong>modelo</strong>: Contiene una serie de clases que utilizan el editor y los comandos
								para editar metadatos y c�digo. Forman una base �til para armar otras herramientas consumiendo una API de alto nivel. 
								Por ejemplo si el proyecto determina que es necesario desarrollar un instalador con
								prestaciones extras, es un buen comienzo consumir estas clases.
						
						<li><strong>nucleo</strong>: <em>Runtime</em> o conjunto de clases que se utilizan en la ejecuci�n de un proyecto.
										La documentaci�n de las clases $api.
					</ul>
				<li><strong>proyectos</strong>: Este directorio contiene los $proyectos del ambiente y es el lugar sugerido 
												para los nuevos proyectos, aunque pueden situarlos en cualquier directorio, 
												si est�n aqu� es m�s f�cil configurarlos.
					<div class='proyectos'>
					<ul>
						<li>...
						<li><strong>proyecto_x</strong>:
							<ul>
								<li><strong>metadatos</strong>: Contiene la �ltima exportaci�n de metadatos del proyecto.
								<li><strong>php</strong>: Directorio que ser� parte del <em>include_path</em> de PHP, 
															se asume que el proyecto pondra sus extensiones y dem�s c�digo aqu�.
								<li><strong>temp</strong>: Directorio temporal no-navegable propio del proyecto
								<li><strong>www</strong>: Directorio navegable que contiene los $puntos_de_acceso a la aplicaci�n.
									<ul>
										<li><strong>css</strong>: Plantillas de estilos CSS del proyecto.									
										<li><strong>img</strong>: Imagenes propias del proyecto.
										<li><strong>temp</strong>:  Directorio temporal navegable del proyecto.
									</ul>
							</ul>
						<li>...
					</ul>		
					</div>		
				<li><strong>temp</strong>: Directorio temporal no-navegable com�n.
				<li><strong>var</strong>: Recursos internos a Toba.
				<li><strong>www</strong>: Directorio navegable que contiene recursos web que consumen el <em>runtime</em> y los proyectos.
					<ul>
						<li><strong>css</strong>: Plantillas de estilos CSS disponibles.
						<li><strong>img</strong>: Imagenes comunes que pueden utilizar los proyectos.
						<li><strong>js</strong>: Clases javascript propias y de 3eros.
						<li><strong>temp</strong>: Directorio temporal navegable com�n.
					</ul>
			</ul>
		";	
		
	}
}

class pant_ejecucion extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/esquema_ejecucion_general.png');
		$img2 = toba_recurso::imagen_proyecto('tutorial/esquema_ejecucion_detalles.png');
		echo "
		<p>
			<img src='$img' style='float:right; padding:0px;'>		
			En la introducci�n vimos que existe un <em>n�cleo</em> o <em>Runtime</em> encargado de la ejecuci�n.
			A diferencia de una librer�a cl�sica, no existe el concepto de procedimiento principal o <em>main</em>
			en el cual el programador incluye las librer�as y las consume. En Toba la situaci�n es distinta:			
			<ul>
				<li>El proyecto brinda un <em>punto de acceso</em> en donde se incluye al n�cleo de toba (generalmente en aplicacion.php).
				<li>A partir de all� el n�cleo analiza los <strong>metadatos</strong> del proyecto y de la operaci�n
					puntual que se ejecuta, activando los componentes acordes.
				<li>Si alguna clase del runtime o alg�n componente se encuentra extendido por el proyecto,
						reci�n all� el programador puede incluir c�digo propio, siempre enmarcado en un <em>plan
						maestro</em> ya delineado.
			</ul>
			
			<h3 style='clear:both'></h3>
			Lo m�s interesante para mostrar en este tutorial es c�mo el proyecto puede variar el comportamiento en ejecuci�n.
			En el siguiente gr�fico se muestra un mayor detalle de la ejecuci�n resaltando en gris los puntos
			donde el proyecto tiene el control de la ejecuci�n, ya sea con <strong>metadatos</strong> o 
			con <strong>extensi�n</strong> de c�digo. 
			
			<div style='text-align:center; margin-top:10px;'>
			<img src='$img2'>		
			</div>
		</p>
		";
	}
}


class pant_administracion extends pant_tutorial 
{
	function generar_layout()
	{
		$img = toba_recurso::imagen_proyecto('tutorial/administracion.png');		
		$wiki = toba_parser_ayuda::parsear_wiki('Referencia/Deployment', 'Casos de Uso de Deployment', 'toba_editor');		
		echo "
			<p>
				Cuando utilizamos el <strong>editor web</strong> de Toba, estamos definiendo un proyecto en base a <strong>metadatos</strong>, almacenados
				en una base de datos definida durante la instalaci�n.
			</p>
			<p>
				Lo positivo de esto es que, al estar centralizada, es posible que un grupo de desarrollo localizado en 
				la misma red pueda utilizarlos en forma simult�nea. Adem�s se puede utilizar SQL tanto para manipular 
				como para obtener los metadatos.
			</p>
			<p>
				Lo negativo es que mientras estos metadatos no sean exportados al sistema de archivos no podr�n ser compartidos
				con otros grupos de trabajo o dentro de un mismo grupo geogr�ficamente distante. Esta necesidad de 
				importar - exportar metadatos se cubre usando los <strong>comandos de consola</strong>.
				Como introducci�n a estos comandos necesitamos presentar dos:
				<ul>
				 <li><em>toba instancia exportar</em>: Exporta desde la base hacia el sistema de archivos
				 <li><em>toba instancia regenerar</em>: Importa desde el sistema de archivos hacia la base
				</ul>
			</p>
			<div style='text-align:center; margin-top:10px;'>
			<img src='$img'>		
			</div>			
			<p>
				Para analizar en m�s profundidad estas y otras situaciones puede ver los <strong>$wiki</strong>
			</p>			
		";
	}
}


?>