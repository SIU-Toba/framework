function ValidaLogin(form)
{
	if (form.usuario.value == "")
	{
		alert("Debe ingresar un nombre de usuario");
		form.usuario.focus();
		return false;
	}
	if (form.clave.value == "")
	{
		alert("Debe ingresar una contrase�a");
		form.clave.focus();
		return false;
	}
	
	return true;
}