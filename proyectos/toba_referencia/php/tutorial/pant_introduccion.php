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
					<li>Un conjunto de <strong>librerías</strong> que son consumidas en ejecución.
					<li>Un <strong>editor web</strong> destinado a la definición/configuración del proyecto, creación de operaciones y definición de sus comportamiento.
					<li>Un conjunto de <strong>comandos de consola</strong> destinados a administrar los proyectos creados con la herramienta.
				</ul>
			</p>
			<p>
				Las definiciones realizadas en el editor web se las denomina <strong>metadatos</strong>, y junto a las definiciones en código conforman el
				comportamiento del proyecto creado. Durante el desarrollo estos metadatos son almacenados en una base de datos relacional denominada <strong>instancia</strong>.
			</p>
		";
		$img = toba_recurso::imagen_proyecto('tutorial/esquema_general.png');
		echo "
			<h3>Flujo de trabajo</h3>
			<img src='$img' style='float:right; padding:10px;'>
			<p>
				El flujo de desarrollo con la herramienta podría definirse así:
				<ol>
					<li>Se utiliza el <strong>editor web</strong> de toba para definir una operación, sus pantallas, 
							sus componentes gráficos, tablas que se consumen, etc. Todo esto se almacena en metadatos en una base de datos.
					<li>Se utiliza un <strong>editor PHP</strong> para crear el código necesario para cubrir lógica particular de la operación.
					<li>Durante este proceso se va probando la operación desde el mismo <strong>editor web</strong> haciendo ajustes contextuales.
					<li>Una vez terminada se utilizan los <strong>comandos administrativos</strong> para exportar el proyecto 
						desde el puesto de desarrollo e importarlo en el sistema en producción.
					<li>En el sistema en producción sólo necesita las <strong>librerías</strong> o <em>runtime</em>
						 para ejecutar el proyecto (código + metadatos).
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
			La siguiente es una lista de los directorios más importantes de Toba y sus funcionalidades a alto nivel:
			<ul class='lista-separada'>
				<li><strong>bin</strong>: Contiene la puerta de entrada a los $comandos.
						Para poder ejecutarlos desde cualquier terminal/consola, a este directorio es necesario incluirlo en el PATH del sistema operativo.
						
				<li><strong>doc</strong>: Contiene documentación interna del proyecto. Para el desarrollador, la mejor documentación se encuentra en el $wiki y en este tutorial.
				
				<li><strong>instalacion</strong>: Contiene toda la configuración local (base que se utiliza, proyectos que se editan, alias de apache, etc.)
													y los metadatos locales (logs, usuarios, etc.).
													Generalmente es un directorio que no se versiona ya que solo contiene información local
													de esta instalación.
				
				<li><strong>php</strong>
					<ul>
						<li><strong>3ros</strong>: Librerías externas utilizadas en el proyecto.
						
						<li><strong>consola</strong>: Código fuente de los comandos administrativos de consola.
						
						<li><strong>contrib</strong>: Código contribuido por los proyectos, que aún no pertenecen al núcleo 
													pero que esta bueno	compartir.
						
						<li><strong>lib</strong>: Clases sueltas propias comunes a todo el ambiente
						
						<li><strong>modelo</strong>: Contiene una serie de clases que utilizan el editor y los comandos
								para editar metadatos y código. Forman una base útil para armar otras herramientas consumiendo una API de alto nivel. 
								Por ejemplo si el proyecto determina que es necesario desarrollar un instalador con
								prestaciones extras, es un buen comienzo consumir estas clases.
						
						<li><strong>nucleo</strong>: <em>Runtime</em> o conjunto de clases que se utilizan en la ejecución de un proyecto.
										La documentación de estas clases $api.
					</ul>
				<li><strong>proyectos</strong>: Este directorio contiene los $proyectos del ambiente y es el lugar sugerido 
												para nuevos proyectos. Aunque pueden situarlos en cualquier directorio, 
												si están aquí es más fácil configurarlos.
					<div class='proyectos'>
					<ul>
						<li>...
						<li><strong>mi_proyecto</strong>:
							<ul>
								<li><strong>metadatos</strong>: Contiene la última exportación de metadatos del proyecto.
								<li><strong>php</strong>: Directorio que será parte del <em>include_path</em> de PHP, 
															se asume que el proyecto almacenará aquí sus extensiones y demás código.
								<li><strong>temp</strong>: Directorio temporal no-navegable propio del proyecto
								<li><strong>www</strong>: Directorio navegable que contiene los $puntos_de_acceso a la aplicación.
									<ul>
										<li><strong>css</strong>: Plantillas de estilos CSS del proyecto.									
										<li><strong>img</strong>: Imagenes propias del proyecto.
										<li><strong>temp</strong>:  Directorio temporal navegable del proyecto.
									</ul>
							</ul>
						<li>...
					</ul>		
					</div>		
				<li><strong>temp</strong>: Directorio temporal no-navegable común.
				<li><strong>var</strong>: Recursos internos a Toba.
				<li><strong>www</strong>: Directorio navegable que contiene recursos web que consumen el <em>runtime</em> y los proyectos.
					<ul>
						<li><strong>css</strong>: Plantillas de estilos CSS disponibles.
						<li><strong>img</strong>: Imagenes comunes que pueden utilizar los proyectos.
						<li><strong>js</strong>: Clases javascript propias de toba y externas.
						<li><strong>temp</strong>: Directorio temporal navegable común.
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
			En la introducción vimos que existe un <em>núcleo</em> o <em>Runtime</em> encargado de la ejecución.
			A diferencia de una librería clásica, no existe el concepto de procedimiento principal o <em>main</em>
			en el cual el programador incluye las librerías y las consume. En Toba la situación es distinta:			
			<ul>
				<li>El proyecto brinda un <em>punto de acceso</em> en donde se incluye al núcleo de toba (generalmente es el archivo www/aplicacion.php).
				<li>A partir de allí el núcleo analiza los <strong>metadatos</strong> del proyecto y de la operación
					puntual que se ejecuta, activando los componentes acordes.
				<li>Si alguna clase del runtime o algún componente se encuentra extendido por el proyecto,
						recién allí el programador puede incluir código propio, siempre enmarcado en un 'plan
						maestro' ya delineado.
			</ul>
			
			<h3 style='clear:both'></h3>
			Lo más interesante para mostrar en este tutorial es cómo el proyecto puede variar el comportamiento en ejecución.
			En el siguiente gráfico se muestra un mayor detalle de la ejecución resaltando en gris los puntos
			donde el proyecto tiene el control de la ejecución, ya sea con <strong>metadatos</strong> o 
			con <strong>extensión</strong> de código. 
			
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
				en una base de datos definida durante la instalación.
			</p>
			<p>
				Lo positivo de esto es que, al estar centralizada, es posible que un grupo de desarrollo localizado en 
				la misma red pueda desarrollar sobre esta base en forma simultánea. Además se puede utilizar SQL tanto para manipular 
				como para obtener los metadatos.
			</p>
			<p>
				Lo negativo es que mientras estos metadatos no sean exportados al sistema de archivos no podrán ser compartidos
				con otros grupos de trabajo o dentro de un mismo grupo geográficamente distante. Esta necesidad de 
				importar - exportar metadatos se cubre usando los <strong>comandos de consola</strong>.
				Como introducción a estos comandos necesitamos presentar dos:
				<ul>
				 <li><em>toba instancia exportar</em>: Exporta desde la base hacia el sistema de archivos
				 <li><em>toba instancia regenerar</em>: Importa desde el sistema de archivos hacia la base
				</ul>
			</p>
			<div style='text-align:center; margin-top:10px;'>
			<img src='$img'>		
			</div>			
			<p>
				Para analizar en más profundidad estas y otras situaciones puede ver los <strong>$wiki</strong>
			</p>			
		";
	}
}


?>