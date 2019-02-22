<?php	
function copiar_directorio( $origen, $destino,  $copiar_ocultos=true )
{
	if( ! is_dir( $origen ) ) {
		throw new Exception("COPIAR DIRECTORIO: El directorio de origen '$origen' es INVALIDO");
	}
	$ok = true;
	if( ! is_dir( $destino ) ) {
		$ok = @mkdir($destino, 0754, true) && $ok;
	} 
	//Busco los archivos del directorio
	$lista_archivos = array();
	$dir = opendir($origen);
	if ($dir !== false) {
		while (false !== ($a = readdir($dir))) {
			if ( $a != '.' && $a != '..' && $a != '.svn' ) {
				$lista_archivos[] = $a;
			}
		}
		closedir( $dir );
	}
	if ($ok) {
		//Copio los archivos
		foreach ( $lista_archivos as $archivo ) {
			$x_origen = $origen . '/' . $archivo;
			$x_destino = $destino . '/' . $archivo;
			//Evito excepciones			
			if (($copiar_ocultos || substr($archivo, 0, 1) != '.')) {			
				if ( is_dir( $x_origen )) {
					$ok = copiar_directorio( $x_origen, $x_destino, $copiar_ocultos) && $ok;
				} else {
					$ok = copy($x_origen, $x_destino) && $ok;
				}
			}
		}
	} else {
		echo " No se pudo escribir en el directorio $destino , verifique los permisos del mismo \n";
	}
	return $ok;
}	

$dir_origen = realpath(__DIR__ .'/../node_modules');
$path_destino = realpath(__DIR__ .'/../www/js/packages');

//echo $dir_origen . PHP_EOL;
//echo $path_destino . PHP_EOL ;
	
//Copiar el directorio a los respectivos lugares
echo "Copiando assets actualizados ..\n" ;
echo "------------------------------------------------------------------\n" ;
copiar_directorio($dir_origen , $path_destino );
/*copiar_directorio($dir_origen . '/jquery', $path_destino . '/jquery');
copiar_directorio($dir_origen . '/jquery-migrate', $path_destino . '/jquery-migrate');
copiar_directori*/


?>
