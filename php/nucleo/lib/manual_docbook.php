<?
include_once("nucleo/lib/elemento_toba.php");

class manual_docbook
{
	private $items;
	private $docbook;
	
	function __construct($proyecto)
	{
		global $db, $ADODB_FETCH_MODE, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = "	SELECT 	i.padre as 		padre,
							i.carpeta as 	carpeta, 
							i.proyecto as	proyecto,
							i.item as 		item,
							i.nombre as 	nombre
					FROM 	apex_item i
					WHERE 	(i.menu = 1)
					AND		(i.proyecto = '$proyecto')
					ORDER BY i.padre,i.orden;";
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		$this->items = $rs->getArray();
	}
	
	//-----------------------------------------------------------
	//----- Generacion de XML  ----------------------------------
	//-----------------------------------------------------------

	function generar_xml()
	{
		$raices = NULL;
		$this->obtener_encabezado();
		echo "<book xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
xsi:noNamespaceSchemaLocation='C:\Documentacion\xml\SCHEMA\docbook-xsd-4.4CR4\docbook.xsd'>\n";	
		$this->obtener_info();
		for($i=0;$i<count($this->items);$i++)
		{
			if($this->items[ $i ]['padre'] == NULL)
			{
				$this->incorporar_hijos($i);
			}
		}
		echo "</book>\n";
	}
	//-----------------------------------------------------------

	function obtener_encabezado()
	{
		echo "<?xml version='1.0'?>
<?altova_xslfo C:\Documentacion\xml\XSL\docbook-xsl-1.67.2\fo\docbook.xsl?>
";
	}
	//-----------------------------------------------------------
	
	function obtener_info()
	{
		echo "<bookinfo>
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
	</bookinfo>
";	
	}
	//-----------------------------------------------------------

	function incorporar_hijos($nodo)
	{
		static $x=0;
		$inden = str_repeat("	",$x );
		//Transformar $this->item en ARBOL
		//ei_arbol($this->items, 'item');
		
		//echo  $inden . "<li><a href='' title='".$this->items[$nodo]['nombre']."' target=''>".$this->items[$nodo]['nombre']."</a>\n";
		
		if ($this->items[$nodo]['carpeta']!="1")
		{
			//Item COMUN
			echo  $inden . "<section>\n";
			echo  $inden . "<title>".$this->items[$nodo]['nombre']."</title>\n";
			//echo "item: " . $this->items[$nodo]['nombre'] . "\n";
			$elemento = new elemento_toba_item();
			$elemento->cargar_db($this->items[$nodo]['proyecto'],
									$this->items[$nodo]['item']);
			echo   $elemento->obtener_docbook();
			echo  $inden . "</section>\n";
		}else{
			//Carpeta
			//echo "carpeta: " . $this->items[$nodo]['nombre'] . "\n";
			echo  $inden . "<section>\n";
			echo  $inden . "<title>".$this->items[$nodo]['nombre']."</title>\n";
			$rs = $this->obtener_lista_hijos($nodo);
			for($i=0;$i<count($rs);$i++)
			{
				$x++;
				$this->incorporar_hijos($rs[ $i ]);
				$x--;
			}
			echo  $inden . "</section>\n";
		}
	}
	//-----------------------------------------------------------
	
	function obtener_lista_hijos($nodo)
	{
		$hijos = NULL;
		for($i=0;$i<count($this->items);$i++)
		{
			if($this->items[ $i ]['padre'] == $this->items[ $nodo ][ 'item' ])
			{
				$hijos[] = $i;
			}
		}
		return $hijos;
	}

	//-----------------------------------------------------------
	//-----------------------------------------------------------
	//-----------------------------------------------------------
}
?>