<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_respuestas extends toba_ci
{

	/****************************************************
	 *** CASO 1: Comunicación de datos via AJAX
	 ****************************************************/
	
	/**
	 * Se configura el formulario con un set de datos básicos
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
		
		//-- Paso la información a JS
		$respuesta->set(array($cant_dias, $total));
	}

	/**
	 * Javascript necesario para el caso de preguntar/responder datos
	 */
	function js_caso_datos()
	{
		echo "		
			/**
			 * Acción del botón CALCULAR
			 */
			{$this->objeto_js}.evt__form_datos_resp__calcular = function() {
				//--- Construyo los parametros para el calculo, en este caso son los valores del form
				var parametros = this.dep('form_datos_param').get_datos();
				
				//--- Hago la peticion de datos al server, la respuesta vendra en el método this.actualizar_datos
				this.ajax_dato('calcular', parametros, this, this.actualizar_datos);
				
				//--- Evito que el mecanismo 'normal' de comunicacion cliente-servidor se ejecute
				return false;
			}
			
			/**
			 * Acción cuando vuelve la respuesta desde PHP
			 */
			{$this->objeto_js}.actualizar_datos = function(datos)
			{
				this.dep('form_datos_resp').ef('dias').set_estado(datos[0]);
				this.dep('form_datos_resp').ef('importe').set_estado(datos[1]);
			}			
		";
	}

	
	/****************************************************
	 *** CASO 2: Comunicación de HTML via AJAX
	 ****************************************************/
	
	/**
	 * Método indicado desde JS como responsable de retornar el html, en este caso utiliza una API de flickr y saca un conjunto de fotos
	 */
	function ajax__album_flickr($tag, toba_ajax_respuesta $respuesta)
	{
		if (!extension_loaded('curl')) {
		    $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
		    @dl($prefix . 'curl.' . PHP_SHLIB_SUFFIX);
		    if (!extension_loaded('curl')) {
		    	echo "Se necesita instalar la extensión <strong>curl</strong> para acceder al API de Flickr";
		    	return;
		    }
		}
		require_once('lib/flickr_api.php');
		$secrets = array('api_key' => 'e5ec32dadfbc7f48fa476a1d62a5c251', 'api_secret' => '579da1ad011ef233');
		$flickr = new Flickr($secrets);
		$photos = $flickr->photosSearch('',$tag);
		$html = '';
		if ($photos && $photos['total'] > 0) {
			$i = 0;
			$modulo = 4;
			$html .= "<table>";
			foreach($photos['photos'] as $photo) {
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
			$html .= "</table>";
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
			 * Acción del botón BUSCAR
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
	 *** CASO 3: Utilización Ad-Hoc de la API de bajo nivel
	 ****************************************************/	
	
	function extender_objeto_js()
	{
		$this->js_caso_datos();
		$this->js_caso_html();
		
	}
}

?>