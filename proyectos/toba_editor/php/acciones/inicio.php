<?php

function controlar_usuario()
{
	$usuario_actual = toba_manejador_archivos::get_usuario_actual();
	if (isset($usuario_actual)) {
		$escapador = toba::escaper();
		$usuarios_defecto = array('system', 'www-data', 'wwwrun', 'nobody', 'nobody');
		if (in_array($usuario_actual, $usuarios_defecto)) {

			$html = "<div style='margin-top: 100px; background-color:white; padding: 10px;'>
					<strong>Recomendado cambiar usuario APACHE</strong><br><br>
					<div style='text-align:left'><p>Actualmente el servidor web (incluyendo a PHP y Toba) se está ejecutando con el usuario <strong>". $escapador->escapeHtml($usuario_actual)."</strong> del sistema.
							Por seguridad esta configuración es la recomendada para sistemas en <strong>producción</strong>.</p>
						<p>En cambio para ambientes de <strong>desarrollo</strong>, este toba_editor necesita abrir y guardar archivos, ejecutar comandos svn, etc,
							necesita correr con el <strong>usuario de escritorio</strong> actualmente logueado al sistema operativo. Por ello recomendamos seguir los siguentes pasos:</p>
			";
			if (toba_manejador_archivos::es_windows()) {
				$html .= "
						<ol style='background-color: #EEEEEE; border: 1px inset gray;'>
							<li>Primero es necesario que el usuario actualmente logueado posea una contraseña. Si no la tiene o desconoce:
								<ol style='background-color: #E9E8E8; border: 1px inset gray; margin: 10px;'>
									<li>Ir a <em>Inicio > Ejecutar</em>, ingresar
										<pre>lusrmgr.msc</pre>
									<li>Hacer click derecho sobre el usuario actual y seleccionar <em>Establecer contraseña</em>
									<li>Luego de pasar todas las advertencias, ingresar la contraseña y aceptar
								</ol>
							<li>Ir a <em>Inicio > Ejecutar</em>, ingresar
								<pre>services.msc</pre>
							<li>Seleccionar servicio Apache 2.x, hacer doble click sobre el mismo
							<li>Ir a solapa <em>Iniciar Sesión</em>
							<li>Seleccionar <em>Esta cuenta</em> e ingresar el nombre y contraseña de la cuenta de usuario actual.
				";
			} else {
				$html .= "
						<ol style='background-color: #EEEEEE; border: 1px inset gray;'>
							<li>Configurar que apache ejecute con el usuario actualmente logueado al sistema de ventanas. Editar el archivo
								<em>/etc/apache2/apache2.conf</em> o <em>/etc/apache2/uid.conf</em> si está presente y cambiar el usuario de la siguiente directiva
								por el usuario actual: <pre>User ". $escapador->escapeHtml($usuario_actual)."</pre>
							<li>Para que apache pueda crear sesiones PHP, hay que cambiar el owner de la carpeta de sesiones (si no encuentra la carpeta de sesiones de php, está en la
								directiva <em>session.save_path</em> en el php.ini
								<pre>sudo chown mi_usuario /var/lib/php5 -R</pre>
							</li>
				";
			}
			$html .= '
						<li>Luego de aceptar, reiniciar el servicio apache. En caso de que se siga mostrando esta advertencia al inicio del editor, por favor
							contactarse con el soporte de toba ya que es muy importante para nosotros que estos pasos se sigan y funcionen bien.
					</ol>
					</div></div>
			';
			echo $html;
		}
	}		
}

//ei_arbol($_SESSION, 'SESION', null, true);

//--- VERSION
echo "<style type='text/css'>
	#overlay_contenido {
		width: 90%;
	}
	.overlay-mensaje {
		max-height: 100%;
		overflow: visible;
	}
	li {
		padding-top: 5px;
	}
</style>";

$url_trac = get_url_desarrollos();
//$url_login = $url_trac.'/trac/toba/login';

if (isset($_GET['phpinfo'])) {
	phpinfo();
//} elseif (isset($_POST['chequeo'])) { 
	
} else {
		/*echo toba_form::abrir('toba', toba::vinculador()->get_url());
		echo toba_form::submit('chequeo', "Chequear compatibilidad extensiones");
		echo toba_form::cerrar();*/		
		

	$version = toba_modelo_instalacion::get_version_actual();
	$cambios = "$url_trac/trac/toba/wiki/Versiones/".($version->get_release('.').'.0');
	echo "<div class='logo-inicio'>";
	echo toba_recurso::imagen_proyecto('logo.gif', true);
	echo '<br><br>Editando proyecto <strong>' . toba_editor::get_proyecto_cargado()	.'</strong> en la instancia <strong>' . toba_editor::get_id_instancia_activa() .'</strong>.<br>';
	$ayuda = toba_recurso::ayuda(null, 'Ver log de cambios introducidos en esta versión');
	echo "<a target='wiki' style='text-decoration:none; font-size: 16px; font-weight: bold;margin-top: 25px;float:left' href='". toba::escaper()->escapeHtmlAttr($cambios)."' $ayuda>Versión ";
	echo $version->__toString().'</a>';
		
		
	$vinc = toba::vinculador()->get_url(null, null, array('phpinfo' =>1));
	echo "<a style='text-decoration:none; float:right; text-align: center; ' href='$vinc' title='Ver información acerca de la instalación PHP de este servidor'>";
	echo toba_recurso::imagen_proyecto('php-med-trans.png', true);
	echo '<br>'.phpversion();
	echo '</a>';

	if (! toba_manejador_archivos::es_windows()) {
		//Por ahora este mécanismo sólo funciona en linux
		controlar_usuario();
	}
	echo '</div>';
}


?>
