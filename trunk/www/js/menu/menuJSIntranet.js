//fonction pour redirection menu accès 1 clic
function openURL(){ 
	var selInd = document.formunclic.unclic.selectedIndex; 
	var goURL = document.formunclic.unclic.options[selInd].value;
	top.location.href = goURL; 
}

//fonctions de récupération des arguments passé en QueryString
function QueryString(key){
	var value = null;
	for (var i=0;i<QueryString.keys.length;i++){
		if (QueryString.keys[i]==key){
			value = QueryString.values[i];
			break;
		}
	}
	return value;
}

QueryString.keys = new Array();
QueryString.values = new Array();

function QueryString_Parse(){
	//var query = window.location.search.substring(1);
	var query = newUrlVars ;
	var pairs = query.split("&");
	
	for (var i=0;i<pairs.length;i++){
		var pos = pairs[i].indexOf('=');
		if (pos >= 0){
			var argname = pairs[i].substring(0,pos);
			var value = pairs[i].substring(pos+1);
			QueryString.keys[QueryString.keys.length] = argname;
			QueryString.values[QueryString.values.length] = value;		
		}
	}
}

// analyse des Querystrings passée en argument (position dans la navigation, et determination du chemin complet)
QueryString_Parse();

if (QueryString("pos")!= null && QueryString("pos")!= "" ){
	var mv_pos = QueryString("pos");
} else {
	var mv_pos = -1;
}

if (QueryString("pos2")!= null && QueryString("pos2")!= "" ){
	var mv_pos2 = QueryString("pos2");
} else {
	var mv_pos2 = -1;
}

if (QueryString("pos")!= null && QueryString("pos")!= "" ){
	var mv_pos1 = QueryString("pos");
	var mv_pos2 = QueryString("pos2");

	temp = mv_niv2[mv_pos1][mv_pos2].toString();
	temparray=temp.split(";");
	var lien = temparray[0];
	var titre = temparray[1];

	if (QueryString("pos3")!=null){
		mv_pos3 = QueryString("pos3");
		temp2 = mv_niv3[mv_pos1][mv_pos2][mv_pos3].toString();
		temparray2 = temp2.split(";");
		var sslien = temparray2[0];
		var sstitre = temparray2[1];
	
		var chemin = "<b><a href='index.htm'>Accueil</a></b> > "+mv_menu[mv_pos]+" > "+titre+" > "+sstitre+"<br><br>";
	}else{
		var chemin = "<b><a href='index.htm'>Accueil</a></b> > "+mv_menu[mv_pos]+" > "+titre+"<br><br>";
	}
} else {
	var chemin = "<b><a href='index.htm'>Accueil</a></b><br><br>";
}

// fonction menu gauche hierarchique (3 niveaux)
function menu_draw() {
	mv_aff = "<TABLE BORDER=0 BGCOLOR=#FFFFFF CELLPADDING=0 CELLSPACING=0 WIDTH=135>";
	mv_aff += "<TR><TD colspan=2><img src='img/tab_haut.gif' width='135' height='12'>";
	mv_aff += "<TR><TD><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=134>";

	for(a=0;a<mv_menu.length;a++){
		if(mv_pos == a || !document.getElementById){
			// menu niveau 1 ouvert
			mv_aff += "<TR><TD colspan=6><img src='img/pucemoins.gif' width='10' height='10' border='0' align=middle><font class=mvstyle> <b>"+mv_menu[a]+"</b></font></TD></TR>";
			for (x=0; x<mv_niv2[a].length; x++){
				var temp = mv_niv2[a][x].toString();
				var temparray=temp.split(";");
				lien = temparray[0];
				titre = temparray[1];
				if (typeof mv_niv3[a][x] != "undefined"){
					if(mv_pos2 == x || !document.getElementById){
						// menu niveau 2 ouvert
						mv_aff += "<TR><TD><img src='img/vide.gif' width='4' height='1'>";
						mv_aff += "<TD><img src='img/vide.gif' width='2' height='1'>";
						mv_aff += "<TD><img src='img/pucemoins.gif' width='10' height='10' border='0' align=middle>";
						mv_aff += "<TD width=100% colspan=3><font class=mvstyle> <b>"+titre+"</b></font></TD></TR>";
						temp2 = mv_niv3[a][x]
						for (y=0; y<temp2.length; y++){
							temp3 = temp2[y].toString();
							temparray2=temp3.split(";");
							lien2 = temparray2[0];
							titre2 = temparray2[1];
							// menu niveau 3 (avec lien)
							mv_aff += "<TR><TD><img src='img/vide.gif' width='4' height='1'>";
							mv_aff += "<TD><img src='img/vide.gif' width='2' height='1'>";
							mv_aff += "<TD><img src='img/vide.gif' width='10' height='1'>";
							mv_aff += "<TD><img src='img/vide.gif' width='2' height='1'>";
							mv_aff += "<TD><img src='img/vide.gif' width='10' height='1'>";
							mv_aff += "<TD width=100%><a href='"+lien2+"&pos="+a+"&pos2="+x+"&pos3="+y+"&'>"+titre2+"</a>";
							mv_aff += "</TD></TR>";
						}
					} else {
						// menu niveau 2 fermé
						mv_aff += "<TR><TD><img src='img/vide.gif' width='4' height='1'>";
						mv_aff += "<TD><img src='img/vide.gif' width='2' height='1'>";
						mv_aff += "<TD><img src='img/puce.gif' width='10' height='10' border='0' align=middle>";
						mv_aff += "<TD width=100% colspan=3><a href='#' onClick='mv_pos2="+x+";menu_draw()'><b>"+titre+"</b></a>";
						mv_aff += "</TD></TR>";
					}
				} else {
					// menu niveau 2 (avec lien)
					mv_aff += "<TR><TD><img src='img/vide.gif' width='4' height='1'>";
					mv_aff += "<TD><img src='img/vide.gif' width='2' height='1'>";
					mv_aff += "<TD><img src='img/vide.gif' width='10' height='1'>";
					mv_aff += "<TD width=100% colspan=3><a href='"+lien+"&pos="+a+"&pos2="+x+"&'>"+titre+"</a>";
					mv_aff += "</TD></TR>";
				}
			}
		} else {
			// menu niveau 1 fermé
			mv_aff += "<TR><TD colspan=6><A HREF='#' onClick='mv_pos="+a+";menu_draw()'><img src='img/puce.gif' width='10' height='10' border='0' align=middle> <b>"+mv_menu[a]+"</b></A>";
			mv_aff += "</TD></TR>";
		}
	}

	mv_aff += "</TABLE></TD>";
	mv_aff += "<TD bgcolor='#858182'><img src='img/vide.gif' width='1' height='1'>";
	mv_aff += "</TD></TR>";
	mv_aff += "<TR><TD colspan=2><img src='img/tab_bas.gif' width='135' height='3'>";
	mv_aff += "</TD></TR></TABLE>";

	if(document.getElementById){
		document.getElementById("mv").innerHTML = mv_aff;
	}else{
		document.write(mv_aff);
	}
}

// fonction pour afficher un cartouche avec graphisme et couleurs
function cartouche(titre,couleur1,couleur2,texte,liencartouche,largeur){
	sortie = "<table width="+largeur+" border='0' cellspacing='0' cellpadding='0'>";
	sortie += "<tr><td>";
	sortie += "<table width="+largeur+" border='0' cellspacing='0' cellpadding='0'>";
	sortie += "<tr><td bgcolor='#"+couleur1+"' width=6><img src='img/cart_gauche.gif' width='6' height='21'></td>";
	sortie += "<td bgcolor='#"+couleur2+"' background='img/cart_centre.gif' class=titre>&nbsp;"+titre+"</td>";
	sortie += "<td bgcolor='#"+couleur2+"' width=19><img src='img/cart_droit.gif' width='19' height='21'></td></tr></table>";
	sortie += "<tr><td><img src='img/vide.gif' width='2' height='1'></td>";
	sortie += "<tr><td>";
	sortie += "<table width="+largeur+" border='0' cellspacing='0' cellpadding='0'>";
	sortie += "<tr><td><img src='img/gris.gif' width='1' height='1'></td>";
	sortie += "<td width=5><img src='img/gris.gif' width='5' height='1'></td>";
	sortie += "<td width=7><img src='img/gris.gif' width='7' height='1'></td>";
	sortie += "<td bgcolor=#808080><img src='img/vide.gif' width='1' height='1'></td>";
	sortie += "<td><img src='img/gris.gif' width='1' height='1'></td></tr>";
	sortie += "<tr><td bgcolor=#808080><img src='img/gris.gif' width='1' height='1'></td>";
	sortie += "<td bgcolor=#"+couleur1+" width=5><img src='img/vide.gif' width='5' height='1'></td>";
	sortie += "<td width=7><img src='img/vide.gif' width='7' height='1'></td>";
	sortie += "<td class=texte width="+(largeur-14)+">"+texte+"</td>";
	sortie += "<td bgcolor=#808080><img src='img/gris.gif' width='1' height='1'></td></tr>";

	if(liencartouche != ""){
		sortie += "<tr><td bgcolor=#808080><img src='img/gris.gif' width='1' height='1'></td>";
		sortie += "<td bgcolor=#"+couleur1+" width=5><img src='img/vide.gif' width='5' height='1'></td>";
		sortie += "<td width=7><img src='img/vide.gif' width='7' height='1'></td>";
		sortie += "<td class=texte align=right><a href='"+liencartouche+"'>";
		sortie += "<img src='img/puce.gif' width='10' height='10' border='0' alt='en savoir +'></a></td>";
		sortie += "<td bgcolor=#808080><img src='img/gris.gif' width='1' height='1'></td></tr>";
	}

	sortie += "<tr><td><img src='img/gris.gif' width='1' height='1'></td>";
	sortie += "<td width=5><img src='img/gris.gif' width='5' height='1'></td>";
	sortie += "<td width=7><img src='img/gris.gif' width='7' height='1'></td>";
	sortie += "<td bgcolor=#808080><img src='img/vide.gif' width='1' height='1'></td>";
	sortie += "<td><img src='img/gris.gif' width='1' height='1'></td></tr></table></table>";
	sortie += "<br><br>";

	document.write(sortie);
}
