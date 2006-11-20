<? 
	$proyecto = toba::proyecto()->get_parametro('descripcion');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><? echo $proyecto ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<frameset rows="*" cols="380,*" frameborder="YES" border="1" bordercolor="#553DA1" framespacing="0" id='frameset_admin'>
  <frame src="<? echo toba::vinculador()->generar_solicitud('toba_instancia','3321',null,false,false,null,true,'lateral')?>" name="lateral" scrolling="auto">
  <frame src="<? echo toba::vinculador()->generar_solicitud('toba_instancia','3340')?>" name="central" scrolling="auto">
</frameset>

</html>