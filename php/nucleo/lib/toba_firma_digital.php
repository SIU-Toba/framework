<?php


/**
 * Utilidades de firma digital en base a certificados de la ONTI
 * Links utiles:
 *    Cert ONTI: http://acraiz.gov.ar/certs/03.crt
 *	  CRL ONTI: http://pki.jgm.gov.ar/crl/FD.crl 
 *    Cert Raiz: http://acraiz.gov.ar/ca.crt
 *    CRL Raiz: http://acraiz.cdp1.gov.ar/ca.crl    
 */
class toba_firma_digital
{

	static function certificado_decodificar($certificado)
	{
		$resource = openssl_x509_read($certificado);
		$output = null;
		$result = openssl_x509_export($resource, $output);
		if($result !== false) {
			$output = str_replace('-----BEGIN CERTIFICATE-----', '', $output);
			$output = str_replace('-----END CERTIFICATE-----', '', $output);
			return base64_decode($output);
		} else {
			throw new toba_error("El certificado no es un certificado valido", "Detalles: $certificado");
		}
	}

	static function certificado_get_fingerprint($certificado)
	{
		return sha1(self::certificado_decodificar($certificado));
	}

	static function certificado_get_serial_number($certificado)
	{
		$data = openssl_x509_parse($certificado, false);
		return strtoupper(self::dec2hex($data['serialNumber']));
	}
	
	static function certificado_validar_revocacion($certificado, $crl) 
	{
		if (! file_exists($crl)) {
			throw new toba_error("La base de certificados revocados no existe o no es accesible.", "Archivo '$crl'");
		}
		$comando = "openssl crl -inform DER -text -noout -in $crl";
		if (toba_manejador_procesos::ejecutar($comando, $stdout, $stderr) !== 0) {
			throw new toba_error("No es posible acceder al detalle de certificados revocados", "Error al ejecutar comando '$comando': $stdout\n".$stderr);
		}
		$serial = self::certificado_get_serial_number($certificado);
		if (strpos($stdout, $serial) !== false) {
			throw new toba_error_firma_digital("El certificado con serial '$serial' se encuentra revocado en la CRL", "Path de la CRL: $crl");
		}
	}
	
	/**
	 * Dado un cadena de texto en formato PEM valida que haya sido expedido por una CA (en formato PEM tambien)
	 * @param string $certificado cadena PEM del certificado a validar
	 * @param string $pem_ca Path al archivo PEM de la CA
	 */
	static function certificado_validar_CA($certificado, $pem_ca)
	{
		if (! file_exists($pem_ca)) {
			throw new toba_error("El certificado raiz no existe o no es accesible.", "Archivo '$pem_ca'");
		}
		$archivo_temp = toba::proyecto()->get_path_temp().'/'.md5(uniqid(time()));
		file_put_contents($archivo_temp, $certificado);		
		$comando = "openssl verify -CAfile $pem_ca $archivo_temp";
		$output = toba_manejador_procesos::ejecutar($comando, $stdout, $stderr);
		unlink($archivo_temp);
		if ($output == 0) {
			throw new toba_error_firma_digital("El certificado no es válido", "Salida del comando '$comando': $stdout\n".$stderr);
		}	
	}
	
	static function certificado_validar_expiracion($certificado)
	{
		$data = openssl_x509_parse($certificado);
		if (self::compare_openssl_date($data['validFrom']) > 0) {
			throw new toba_error_firma_digital("El certificado no es válido tiene una fecha de inicio superior al día de hoy", "Certificado: $certificado");
		}
		if (self::compare_openssl_date($data['validTo']) < 0) {
			throw new toba_error_firma_digital("El certificado no es válido, tiene una fecha de finalización inferior al día de hoy", "Certificado: $certificado");
		}
	}


	/**
	 * Agrega attachments a un archivo .pdf existente. Si falla lanza una excepcion toba_error
	 * @param string $archivo_pdf  Path completo al archivo PDF de entrada 
	 * @param array $paths_attachments Paths completos de archivos a incluir 
	 * @param int $pagina Opcional. Página a la que se agregan los attachments (sino se agregan a nivel del documento)
	 */
	static function pdf_add_attachments($archivo_pdf, $paths_attachments, $pagina = null)
	{
		$archivo_temp = toba::proyecto()->get_path_temp().'/'.md5(uniqid(time()));
		$stdout = null;
		$stderr = null;
		$paths = implode(" ", $paths_attachments);
		$comando = "pdftk $archivo_pdf attach_files $paths output $archivo_temp";
		if (toba_manejador_procesos::ejecutar($comando, $stdout, $stderr) !== 0) {
			throw new toba_error("No fue posible agregar el XML al pdf", "Error al ejecutar comando '$comando': $stdout\n".$stderr);
		}
		if (! rename($archivo_pdf, $archivo_pdf.'.old')) {
			throw new toba_error("No fue posible agregar el XML al pdf", "Imposible renombrar '$archivo_pdf' a '$archivo_pdf.old'");
		}
		if (! rename($archivo_temp, $archivo_pdf)) {
			throw new toba_error("No fue posible agregar el XML al pdf", "Imposible renombrar '$archivo_temp' a '$archivo_pdf'");
		}		
		unlink($archivo_pdf.'.old');
	}
	
	/**
	 * Extrae los attachments PDFs de un documento. Requiere tener instalado en el path el ejecutable 'pdftk'. Si falla lanza una excepcion toba_error
	 * @param string $archivo_pdf Path completo al archivo PDF de entrada que contiene attachments 
	 * @param string $patron Patrón a extraer (formato preg_match), se asumen todas 
	 * @return array Paths completo a archivos extraidos. Es responsabilidad del que llama borrar la carpeta contenedora posterior a su uso
	 */
	static function pdf_get_attachments($archivo_pdf, $patron = null)
	{
		$carpeta_temp = toba::proyecto()->get_path_temp().'/'.md5(uniqid(time()));
		if (! file_exists($carpeta_temp)) {
			if (! mkdir($carpeta_temp)) {
				throw new toba_error("No fue posible extraer los XML del pdf", "Error al intentar crear carpeta temporal $carpeta_temp . Verifique permisos");
			}
		}
		$stdout = null;
		$stderr = null;
		$comando = "pdftk $archivo_pdf unpack_files output $carpeta_temp";
		if (toba_manejador_procesos::ejecutar($comando, $stdout, $stderr) !== 0) {
			throw new toba_error("No fue posible extraer los XML del pdf", "Error al ejecutar comando '$comando': $stdout\n".$stderr);
		}
		$archivos = toba_manejador_archivos::get_archivos_directorio($carpeta_temp, $patron);
		if (empty($archivos)) {
			toba_manejador_archivos::eliminar_directorio($carpeta_temp);
		} 
		return $archivos;
	}
	
	
	
	static private function dec2hex($number)
	{
		$hexvalues = array('0','1','2','3','4','5','6','7', '8','9','A','B','C','D','E','F');
		$hexval = '';
		while($number != '0') {
			$hexval = $hexvalues[bcmod($number,'16')].$hexval;
			$number = bcdiv($number,'16',0);
		}
		return $hexval;
	}
	
	static private function compare_openssl_date ($in) {
		if (strlen($in) == 15) {
			$year = substr($in, 0, 4);
			$in = substr($in, 2);
		} else {
        	$year  = '20'.substr($in, 0, 2); /* NOTE: Yes, this returns a two digit year */
		}
        $month = substr($in, 2, 2);
        $day   = substr($in, 4, 2);
        $hour  = substr($in, 6, 2);
        $min   = substr($in, 8, 2);
        $sec   = substr($in, 10, 2);
        $today = getdate();
        if ($today['year'] < $year) return 1;
        if ($today['year'] > $year) return -1;
        if ($today['mon'] < $month) return 1;
        if ($today['mon'] > $month) return -1;
        if ($today['mday'] < $day) return 1;
        if ($today['mday'] > $day) return -1;
        if ($today['hours'] < $hour) return 1;        
        if ($today['hours'] > $hour) return -1;
        if ($today['minutes'] < $min) return 1;
        if ($today['minutes'] > $min) return -1;
        if ($today['seconds'] < $hour) return 1;
        if ($today['seconds'] > $hour) return -1;
        return 0;
	}
	
	
}

?>