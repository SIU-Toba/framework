<?php
require_once('objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_ei_formulario';	

	function ini()
	{
		parent::ini();
		$ef = toba::memoria()->get_parametro('ef');
		//¿Se selecciono un ef desde afuera?
		if (isset($ef)) {
			$this->set_pantalla(2);
			$this->dependencia('efs')->seleccionar_ef($ef);
		}
	}

	function evt__procesar()
	{
		if (! $this->get_dbr_eventos()->hay_evento_maneja_datos()) {
			toba::notificacion()->agregar('El formulario no posee evento que <strong>maneje datos</strong>,
				esto implica que los datos no viajaran del cliente al servidor.<br><br>
				Para que este comportamiento funcione debe generar algún 
				[wiki:Referencia/Eventos#Modelos modelo de eventos] en la solapa
				de Eventos', 'info');
		}
		parent::evt__procesar();		
	}
	
	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************
	//Para no cambiar la visual del editor los que hago es redireccionar
	//la columna posicion_botonera entre el formulario de propiedades
	//basicas y el datos-tabla de base
	//****************************************************************************
	function conf__prop_basicas()
	{
		$datos = $this->get_entidad()->tabla("prop_basicas")->get();
		$datos['posicion_botonera'] = $this->get_entidad()->tabla('base')->get_columna('posicion_botonera');
		return $datos;
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla('base')->set_columna_valor('posicion_botonera', $datos['posicion_botonera']);
		unset($datos['posicion_botonera']);
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
	}

	//*******************************************************************
	//** Dialogo con el CI de EFs  **************************************
	//*******************************************************************

	function evt__2__salida()
	{
		$this->dependencia('efs')->limpiar_seleccion();
	}

	function get_dbr_efs()
	{
		return $this->get_entidad()->tabla('efs');
	}


	//*******************************************************************
	//** Dialogo con el CI de EVENTOS  **********************************
	//*******************************************************************
	
	function get_eventos_estandar($modelo)
	{
		return toba_ei_formulario_info::get_lista_eventos_estandar($modelo);
	}

	function evt__3__salida()
	{
		$this->dependencia('eventos')->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}
	
	//*******************************************************************
	//** Tab de Layout                 **********************************
	//*******************************************************************
	
	function conf__form_layout(toba_ei_formulario $form)
	{
		$form->ef('template')->get_editor()->ToolbarSet = 'Layout';
		$form->ef('template')->get_editor()->Height = '400px';
		$vinculo = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'ejecutar'));
		$form->ef('template')->get_editor()->Config['TemplatesXmlPath'] = $vinculo;
		$datos = $this->get_entidad()->tabla('prop_basicas')->get();
		if (isset($datos['template']) && trim($datos['template']) != '') {
			$datos['tipo_layout'] = "L";
		}
		$form->set_datos($datos);
	}
	
	function evt__form_layout__modificacion($datos)
	{
		if (!isset($datos['tipo_layout'])) {
			$datos['template'] = null;
		}
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}
	
	function get_tipos_layout()
	{
		return array(
			array('clave' => 'L', 'valor' => 'Usando template')
		);
	}

	function servicio__ejecutar()
	{
		//Determina si el ejecutar es por este ci o por el del parent 
 		$imagen = toba::memoria()->get_parametro('imagen');
 		if (isset($imagen)) {
 			return parent::servicio__ejecutar();		
 		}
 		$url = toba::proyecto()->get_www('img/fck_templates/');
 		$salida = '<?xml version="1.0" encoding="utf-8" ?>
<Templates imagesBasePath="'.$url['url'].'">
	<Template title="Lineal" image="tabla_1_col.gif">
		<Description>Un campo debajo del otro, es el layout original</Description>
		<Html>
			<![CDATA[
';
		$salida .= $this->get_template_lineal();
		$salida .= '
			]]>
		</Html>
	</Template>
	<Template title="Tabla Linea" image="tabla_1_col.gif">
		<Description>Tabla con un campo debajo del otro</Description>
		<Html>
			<![CDATA[
';
		$salida .= $this->get_template_columnas(1);
		$salida .= '
			]]>
		</Html>
	</Template>	
	<Template title="Tabla Dos Columnas" image="tabla_2_col.gif">
		<Description>Se arma una tabla tomando los campos en el orden definido, incluyendo dos campos por fila</Description>
		<Html>
			<![CDATA[
';
		$salida .= $this->get_template_columnas(2);
		$salida .= '
			]]>
		</Html>
	</Template>
	<Template title="Tabla Tres Columnas" image="tabla_3_col.gif">
		<Description>Se arma una tabla tomando los campos en el orden definido, incluyendo tres campos por fila</Description>
		<Html>
			<![CDATA[
';
		$salida .= $this->get_template_columnas(3);
		$salida .= '
			]]>
		</Html>
	</Template>	
</Templates>';
		echo $salida;
	}
	
	protected function get_template_columnas($columnas)
	{
		$salida = '<table>';
		$efs = $this->get_dbr_efs()->get_filas();
		$efs = rs_ordenar_por_columna($efs, 'orden');
		$i = 0;
		$total = count($efs);
		foreach ($efs as $ef) {
			if (! $ef['desactivado']) {
				if ($i % $columnas == 0) {
					$salida .= '<tr>';
				}				
				$salida .= '<td>[ef id='.$ef['identificador'].']</td>';
				$i++;
				if ($i % $columnas == 0) {
					$salida .= '</tr>';
				}				
			}
		}
		$salida .= '</table>';
		return $salida;		
	}
	
	protected function get_template_lineal()
	{
		$salida = '';
		$efs = $this->get_dbr_efs()->get_filas();
		$efs = rs_ordenar_por_columna($efs, 'orden');
		$i = 0;
		$total = count($efs);
		foreach ($efs as $ef) {
			if (! $ef['desactivado']) {
				$salida .= '[ef id='.$ef['identificador'].'] ';
			}
		}
		return $salida;		
	} 	
	
}
?>