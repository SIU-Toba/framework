# Busca definiciones de clases, propiedades y funciones

BEGIN {
	print "----------------------------------------------------------------"
#	print ""
#	print FILENAME
	s = 0	
	k_pro = 0
	k_fun = 0
	k_cla = 0
}

#************************* Elementos de codigo ************************

$1 ~ /class/ {
	if( s == 0 ) {
		if( k_cla > 0)		print "\n----------------------------------------------------------------"
		print "\n CLASE (" NR ") " $2
		k_cla ++
		k_fun = 0
		k_pro = 0
	}
}

$1 ~ /var/ {
	if( s == 0 ) {
		if( k_pro == 0)	print ""
		print "    PROPIEDAD (" NR ") " $2 ""
		k_pro ++
	}
}

$1 ~ /function/ {
	if( s == 0 ){
		if( k_fun == 0) print ""
		print "    FUNCION (" NR ") " $2 
		k_fun ++
	}
}

# Diferencia los contextos de JAVASCRIPT y PHP

/<script/ {
	s = 1
}

/<\/script/ {
	s = 0
}

#************************ Elementos didacticos ********************


/@@desc:/ {
	FS = ":"
	print "        Desc: " $2
}

#*******************************************************************

END {
#	print ""
#	print "   RESUTADOS"
#	print "   ---------"
#	print ""
#	if (k_cla > 0) print "      Clases: " k_cla
#	if (k_pro > 0) print "      Propiedades: " k_pro
#	if (k_fun > 0) print "      Funciones: " k_fun
#	print ""
	print "\n----------------------------------------------------------------"
}