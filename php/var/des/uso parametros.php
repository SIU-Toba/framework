<?
				case "sexo":
					$parametros[] = new ParametroListaFija(	$parm,
															"Sexo",
															Array(	NO_SETEADO	=>	"Ambos",
																	"M" 		=>	"Masculino",
																	"F" 		=>	"Femenino"),
															NO_SETEADO );
					break;
				case "caracter":
					$parametros[] = new ParametroListaFija(	$parm,
															"Caracter",
															Array(	NO_SETEADO	=>	"Ambos",
																	"P" 		=>	"Permanente",
																	"T" 		=>	"Transitorio"),
															NO_SETEADO );
					break;
				case "licencia":
					$parametros[] = new ParametroListaFija(	$parm,
															"Licencia",
															Array(	NO_SETEADO	=>	"No Filtrar",
																	"S" 		=>	"En licencia",
																	"N" 		=>	"Sin licencia"),
															NO_SETEADO );
					break;
				case "estudio":
					$parametros[] = new ParametroListaDB( $parm, "Nivel de Estudio", "wt04", "nivelestud", "nombre" ,"C", "Todos" );
					break;
				case "incentivo":
					$parametros[] = new ParametroListaDB(	$parm, "Categoría de Incentivo", "wh01", "incentivo", "incentivo","C", "Todos" );
					break;
				case "edad":
					$parametros[] = new ParametroRango( $parm, "Edad", "edad", 0, 110 );
					break;
				case "antiguedad":
					$parametros[] = new ParametroRango( $parm, "Antigüedad","antiguedad", 0, 100 );
					break;
				case "mes":
					$parametros[] = new ParametroMes( $parm, "Mes");
					break;
				case "periodo":
					$parametros[] = new ParametroPeriodo( $parm );
					break;
				case "dep_sub":
					$parametros[] = new ParametroDepSubdep( $parm );
					break;
	// Parametros protegidos: (solo pueden setearse en en valores permitidos por el perfil)
				case "uacad":
					$parametros[] = new ParametroListaDBProtegido (	$parm, "Unidad Académica", "wt01", "unidadacad", "nombre","C", "Todas" );
					break;
				case "fuente":
					$parametros[] = new ParametroFuente( $parm );
					break;
	// Parametros que solo vienen por GET.
	// IMPORTANTE: se asume que el metodo 'getItem' de estos parametros no se va a llamar nunca.
				case "escalafon":
					$parametros[] = new ParametroListaFija(	$parm,
															"Escalafón",
															Array(	"D"		=>	"Docente",
																	"N" 	=>	"No docente",
																	"S" 	=>	"Superior"),"");
					break;
				case "dedicacion":
					$parametros[] = new ParametroListaDB(	$parm, "Dedicación", "wt03", "dedicacion", "nombre","C", "Todas");
					break;
				case "categoria":
					$parametros[] = new ParametroListaDB(	$parm, "Categoria", "wt02", "categoria", "nombre","C" , "Todas");
					break;
				case "g_edad";
					$parametros[] = new ParametroGet(	$parm, "edad", "Edad" );
					break;
				case "g_anti";
					$parametros[] = new ParametroGet(	$parm, "antiguedad", "Antiguedad" );
					break;
				case "g_neto";
					$parametros[] = new ParametroGetMoneda(	$parm, "round(imp_neto - 49.99, -2)", "Rango de Netos" );
					break;
				case "g_bruto";
					$parametros[] = new ParametroGetMoneda(	$parm, "round(imp_neto + imp_dctos - 49.99, -2)", "Rango de Brutos" );
					break;
				case "legajo":
					$parametros[] = new ParametroHueco($parm, "Apellido y Nombre");
					break;
			}
