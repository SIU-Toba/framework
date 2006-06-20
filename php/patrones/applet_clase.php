<? 
// La clase esta en el parametro A

	$path = recurso::path_pro() . "/applet";
	$class = $this->info["item_parametro_a"];
?>
<applet 
	codebase='<? echo "$path" ?>' 
	code='<? echo $class ?>' 
	width='100%' height='100%'>
Your browser is completely ignoring the &lt;APPLET&gt; tag!
</applet>
