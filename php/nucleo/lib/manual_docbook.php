<?
include_once("nucleo/lib/elemento_toba.php");

class manual_docbook
{
	private $items;
	private $arbol;
	
	function __construct($proyecto, $tipo_manual)
	{
		global $db, $ADODB_FETCH_MODE, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = "	SELECT 	i.padre as 		padre,
							i.carpeta as 	carpeta, 
							i.proyecto as	proyecto,
							i.item as 		item,
							i.nombre as 	nombre
					FROM 	apex_item i
					WHERE 	(i.proyecto = '$proyecto')
					ORDER BY i.padre,i.orden;";
		//echo $sql;
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		$this->items = $rs->getArray();
	}
	//-----------------------------------------------------------
	
	
	//-----------------------------------------------------------
	//----- Generacion de XML  ----------------------------------
	//-----------------------------------------------------------

	function obtener_xml()
	{
		$manual = $this->obtener_cabecera();
		$manual .= $this->obtener_cuerpo();
		return $manual;
	}

	function obtener_cuerpo()
	{
		$elemento = new elemento_toba_item();
		$elemento->cargar_db("toba","/admin/apex/elementos/ef");
		return $elemento->obtener_docbook();
	}

	function obtener_cabecera()
	{
		return "<?xml version='1.0'?>
<?altova_xslfo C:\Documentacion\xml\XSL\docbook-xsl-1.67.2\fo\docbook.xsl?>
<book xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
	 xsi:noNamespaceSchemaLocation='C:\Documentacion\xml\SCHEMA\docbook-xsd-4.4CR4\docbook.xsd'>

	<bookinfo>
		<title>TOBA</title>
		<subtitle>Manual de usuario</subtitle>
		<titleabbrev>book-ex ???????</titleabbrev>
		<revhistory>
			<revision>
				<revnumber>1</revnumber>
				<date>Jan 15, 2001 ???????</date>
			</revision>
		</revhistory>
		<corpauthor>S.I.U. (Sistemas de Información Universitaria)</corpauthor>
		<publisher>
			<publishername>???????</publishername>
			<address>
(The content here is shown in the Address appendix.)
     </address>
		</publisher>
		<copyright>
			<year>2005</year>
			<holder>Sistemas de Información Universitaria</holder>
		</copyright>
		<date>Jan 15, 2001 ???????</date>
		<legalnotice>
			<title>Noticia Legal</title>
			<para>This
    paragraph could be a copyright notice, license agreement,
    etc.</para>
		</legalnotice>
	</bookinfo>";	
	}

	//-----------------------------------------------------------
	//-----------------------------------------------------------
}
?>