<?php

/* ///////////////////////////////////////////////////////////////////
 * @@desc: Clase para la impresion de documentos
 * @@pend: - Definir dir temporal de pdf's
 *         - Definir como se guardan los datos en la sesion
 * ///////////////////////////////////////////////////////////////////
 */

require_once('impresion/impr_includes.php');

class impresion
{
	/* 
	 * Definicion de variables y tipos
     */
	 
	public $documentos;
	public $impresion_final;
	
	function __construct()
	{
		$this->documentos = array();
		$this->impresion_final = array();
		abrir_fuente_datos('comechingones');
	//	$pdf =& new impr_documento('doc_cheque', toba::get_db('comechingones'));		
	}
	
	/*
	 * Agrega un documento al arreglo de impresion
	 */
	function agregar_documento($documento, $impresora)
	{
		$sql = "SELECT * from sau_np_comprobantes WHERE comprobante = '" . $documento .  "'";
		$resultado = recuperar_datos($sql, com_fuente_datos );


		// $this->documentos[] = $doc;
	}
	
	/*
	 * Elimina un documento del arreglo de impresion
	 */
	function borrar_documento($documento)
	{
	}
	
	function listar_documentos()
	{
		ei_arbol($this->documentos, "Documentos para imprimir");
	}
	
	function parseo_doc_a_imprimir($documento)
	{
	}
	
	function generar_parseo_completo()
	{
	}
	

	
	function send_pdf_headers()
	{
    	header("Content-type: application/pdf\n"); 
    	header("Content-transfer-encoding: binary\n"); 
	}
	

	/*
	 * Envia via curl a un frame oculto los datos a imprimir,
	 * en el cual se aloja un applet java que se encarga de la
	 * impresion propiamente dicha.
	 *
	 */
	function imprimir()
	{
		
	}
	

}
?>