<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_ajax extends toba_ci
{
	protected $s__cache_feriados = array();

	function extender_objeto_js()
	{
		$this->js_caso_datos();
		$this->js_caso_validacion();
		$this->js_caso_html();
		$this->js_caso_bajo_nivel();
	}	
	
	/****************************************************
	 *** CASO 1: Comunicaci�n de datos via AJAX
	 ****************************************************/
	
	/**
	 * Se configura el formulario con un set de datos b�sicos
	 */
	function conf__form_datos_param(toba_ei_formulario $form)
	{
		$inicio = new toba_fecha();
		$fin = new toba_fecha();
		$fin->set_timestamp($inicio->get_fecha_desplazada_meses(1));
		$datos = array(
				'valor_diario' => '15.25',
				'fecha_inicio' =>  $inicio->get_fecha_db(),
				'fecha_fin' => $fin->get_fecha_db()
		);
		$form->set_datos($datos);
	}
	
	/**
	 * Metodo invocado desde JS para 'calcular' el nuevo importe
	 */
	function ajax__calcular($parametros, toba_ajax_respuesta $respuesta)
	{
		//--- Calculo el valor total en base a las fechas y el valor diario
		$fecha1 = toba_fecha::desde_pantalla($parametros['fecha_inicio']);
		$fecha2 = toba_fecha::desde_pantalla($parametros['fecha_fin']);
		$cant_dias = $fecha1->diferencia_dias($fecha2);
		$total = $cant_dias * $parametros['valor_diario'];
		
		//-- Paso la informaci�n a JS
		$respuesta->set(array($cant_dias, $total));
	}

	/**
	 * Javascript necesario para el caso de preguntar/responder datos
	 */
	function js_caso_datos()
	{
		echo "		
			/**
			 * Acci�n del bot�n CALCULAR
			 */
			{$this->objeto_js}.evt__form_datos_resp__calcular = function() {
				//--- Construyo los parametros para el calculo, en este caso son los valores del form
				var parametros = this.dep('form_datos_param').get_datos();
				
				//--- Hago la peticion de datos al server, la respuesta vendra en el m�todo this.actualizar_datos
				this.ajax('calcular', parametros, this, this.actualizar_datos);
				
				//--- Evito que el mecanismo 'normal' de comunicacion cliente-servidor se ejecute
				return false;
			}
			
			/**
			 * Acci�n cuando vuelve la respuesta desde PHP
			 */
			{$this->objeto_js}.actualizar_datos = function(datos)
			{
				this.dep('form_datos_resp').ef('dias').set_estado(datos[0]);
				this.dep('form_datos_resp').ef('importe').set_estado(datos[1]);
			}			
		";
	}

	
	/******************************************************************************
	 *** CASO 2: Comunicaci�n de datos via AJAX utilizado en una validaci�n en JS
	 *******************************************************************************/	
	
	/**
	 * M�todo invocado desde JS para validar un d�a especifico contra un WebService de feriados
	 * del Ministerio del Interior (http://www.mininterior.gov.ar/servicios/wsferiados.asp)
	 */
	function ajax__validar_dia_habil($dia, toba_ajax_respuesta $respuesta)
	{
		$mensaje = '';
		$es_valido = $this->validar_dia($dia, $mensaje);
		$respuesta->set(array('es_valido' => $es_valido, 'mensaje' => $mensaje));
	}
	
	function ajax__validar_lista_dias($fechas, toba_ajax_respuesta $respuesta)
	{
		$salida = array();
		foreach ($fechas as $fecha) {
			$mensaje = '';
			if (! $this->validar_dia($fecha, $mensaje)) {
				$salida[] = $mensaje;	
			}
		}
		$respuesta->set($salida);
	}
	
	/**
	 * Funci�n de ayuda que comprueba si un dia es feriado
	 */
	function validar_dia($dia, & $mensaje)
	{
		if (trim($dia) == '') {
			return true;
		}
		$es_valido = true;
		$fecha = toba_fecha::desde_pantalla($dia);
		$anio = $fecha->get_parte('a�o');
		//--- Se forma un cache de feriados por a�o para evitar ir al WS en cada pedido, esto es un ejemplo de juguete!
		if (! isset($this->s__cache_feriados[$anio])) {
			$client = new SoapClient('http://webservices.mininterior.gov.ar/Feriados/Service.svc?wsdl');
			$d1 = mktime(0, 0, 0, 1, 1, $anio);
			$d2 = mktime(0, 0, 0, 12, 31, $anio);
			$feriados = $client->FeriadosEntreFechasAsXml(array('d1'=>$d1, 'd2'=>$d2));
			$this->s__cache_feriados[$anio] = $feriados->FeriadosEntreFechasAsXmlResult;
		}
		$feriados = simplexml_load_string($this->s__cache_feriados[$anio]);
		foreach ($feriados as $feriado) {
			$fecha_feriado = new toba_fecha((string) $feriado->FechaEfectiva);
			if ($fecha_feriado->es_igual_que($fecha)) {
				$es_valido = false;
				$mensaje = 'El '.$fecha->get_fecha_pantalla().'
								 es '. trim((string) utf8_decode($feriado->Descripcion)).
								' por '.trim((string) utf8_decode($feriado->TipoDescripcion));
				break;		
			}
		}
		return $es_valido;
	}
	
	/**
	 * Javascript necesario para el caso de preguntar/responder datos
	 */
	function js_caso_validacion()
	{
		echo "
			var confirmado = false;
			{$this->objeto_js}.evt__confirmar = function() {
				if (confirmado) {
					return true;
				}
				var datos = this.dep('form_validacion').get_datos();
				var parametros = [];
				for (i in datos) {
					parametros.push(datos[i]['dia']);
				}
				this.ajax('validar_lista_dias', parametros, this, this.respuesta_confirmacion);
				return false;			
			}
			
			/**
			 * Acci�n cuando vuelve la respuesta desde PHP
			 */
			{$this->objeto_js}.respuesta_confirmacion = function(errores)
			{
				if (errores.length > 0) {
					var error = 'Errores: <ul>';
					for (i in errores) {
						error = error + '<li>' + errores[i] + '</li>';
					}
					error = error + '</ul>';
					notificacion.limpiar();
					notificacion.agregar(error);
					notificacion.mostrar();					
				} else {
					confirmado = true;
					{$this->objeto_js}.set_evento(new evento_ei('confirmar', true, '' ));
				}
			}				
		";
	}	
	
	
	function evt__confirmar()
	{
		toba::notificacion()->agregar('Confirmado OK!', 'info');	
	}
	
	/****************************************************
	 *** CASO 3: Comunicaci�n de HTML via AJAX
	 ****************************************************/
	
	/**
	 * M�todo indicado desde JS como responsable de retornar el html, en este caso utiliza una API de flickr y saca un conjunto de fotos
	 */
	function ajax__album_flickr($tag, toba_ajax_respuesta $respuesta)
	{
		if (!extension_loaded('curl')) {
		    $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
		    @dl($prefix . 'curl.' . PHP_SHLIB_SUFFIX);
			if (!extension_loaded('curl')) {
		    	echo 'Se necesita instalar la extensi�n <strong>curl</strong> para acceder al API de Flickr';
		    	return;
			}
		}
		require_once('lib/flickr_api.php');
		$secrets = array('api_key' => 'e5ec32dadfbc7f48fa476a1d62a5c251', 'api_secret' => '579da1ad011ef233');
		$flickr = new Flickr($secrets);
		$photos = $flickr->photosSearch('', $tag);
		$html = '';
		if ($photos && $photos['total'] > 0) {
			$i = 0;
			$modulo = 4;
			$html .= '<table>';
			foreach ($photos['photos'] as $photo) {
				if ($i == 12) {
					break;
				}
				if ($i % $modulo == 0) {
	    			$html .= "<tr>\n";	
				}
		    	$url_chica = $flickr->getPhotoURL($photo, 's');
		    	$url_full = 'http://flickr.com/photos/'.$photo['owner'].'/'.$photo['id'];
				$html .= "<td><a title='Ver foto' href='$url_full' target='_blank'><img src='$url_chica' height=75 width=75/></a></td>";
				$i++;
				if ($i % $modulo == 0) {
	    			$html .= "</tr>\n";	
				}
			}		
			$html .= '</table>';
			$html .= "<div style='text-align:center'><em>Mostrando ".$i.' de '.$photos['total'].' fotos...</em></div>';
		} else {
			$html .= "No se encontraron fotos con el tag <strong>$tag</strong>.";
		}
		$respuesta->set($html);
	}
	
	/**
	 * Javascript necesario para el caso de una respuesta html
	 */
	function js_caso_html()
	{
		echo "		
			/**
			 * Acci�n del bot�n BUSCAR
			 */
			{$this->objeto_js}.evt__form_flickr__buscar = function() {
				//--- Le pasa como parametro el nombre del tag
				var parametro = this.dep('form_flickr').ef('tag').get_estado();
				
				//--- El resultado lo va a aplicar sobre el innerhtml de este nodo_html
				var nodo_html = this.dep('form_flickr').ef('album').input();
								
				//--- Hago la peticion de datos al server, la respuesta impactara sobre el nodo_html, en este caso el contenido de un ef_fijo
				this.ajax_html('album_flickr', parametro, nodo_html);
				
				//--- Evito que el mecanismo 'normal' de comunicacion cliente-servidor se ejecute
				return false;
			}
		";
	}	
	
	
	/****************************************************
	 *** CASO 4: Utilizaci�n Ad-Hoc de la API de bajo nivel
	 ****************************************************/	
	
	function ajax__api_bajo_nivel($parametros, toba_ajax_respuesta $respuesta)
	{
		$html_wikipedia = utf8_decode(file_get_contents(dirname(__FILE__).'/ejemplo_ajax.html'));
		$respuesta->agregar_cadena('html_puro', '<div style="height:400px;overflow:auto">'.$html_wikipedia.'</div>');
		$respuesta->agregar_cadena('javascript', 'alert("Transferido tambi�n este alert")');
	}
	
	/**
	 * Javascript necesario para el caso de una respuesta html
	 */
	function js_caso_bajo_nivel()
	{
		echo "		
			{$this->objeto_js}.evt__boton = function() {
				this.ajax_cadenas('api_bajo_nivel', null, this, this.metodo_callback);				
				//--- Evito que el mecanismo 'normal' de comunicacion cliente-servidor se ejecute
				return false;
			}
			
			/**
			 *	La respuesta llega 
			 */
			{$this->objeto_js}.metodo_callback = function(respuesta) {
				this.nodo_pie().innerHTML = respuesta.get_cadena('html_puro');		
				eval(respuesta.get_cadena('javascript'));
			}
		";
	}	
}

?>