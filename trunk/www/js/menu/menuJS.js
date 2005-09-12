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
	var mv_pos = 0;//-1;
}

if (QueryString("pos2")!= null && QueryString("pos2")!= ""){
	var mv_pos2 = QueryString("pos2");
} else {
	var mv_pos2 = 0;//-1;
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
	
		var chemin = "<font class=mvstyle><b><a href='index.htm' class=mvstyle>Accueil</a></b> > "+mv_menu[mv_pos]+" > "+titre+" > "+sstitre+"</font><br><br>";
	}else{
		var chemin = "<font class=mvstyle><b><a href='index.htm' class=mvstyle>Accueil</a></b> > "+mv_menu[mv_pos]+" > "+titre+"</font><br><br>";
	}
	
} else {
	var chemin = "<font class=mvstyle><b><a href='index.htm' class=mvstyle>Accueil</a></b></font><br><br>";
}

// fonction menu gauche hierarchique (3 niveaux)
function menu_draw() {
	mv_aff = "<table width='155' border='0' cellspacing='0' cellpadding='0'><tr><td height='15'><img src='./img/menu/shim.gif' width='1' height='15'></td></tr>";

	for(a=0;a<mv_menu.length;a++){
		if(a == 0){
			cartouche = "1";
		}else{
			cartouche = "2";
		}
		if(mv_pos == a || !document.getElementById){
			// menu niveau 1 ouvert

			mv_aff += "<tr><td background='./img/menu/cartouche"+cartouche+".gif' height='15' class='navigation'>&nbsp;&nbsp;"+mv_menu[a]+"</td></tr>";
			
			mv_aff += "<tr><td background='./img/menu/fond_navig.gif' height='15' class='navigation' align='right'><table width='145' border='0' cellspacing='0' cellpadding='0'>";

			for (x=0; x<mv_niv2[a].length; x++){
				var temp = mv_niv2[a][x].toString();
				var temparray=temp.split(";");
				lien = temparray[0];
				titre = temparray[1];
				popup = temparray[2];
				popup_width  = temparray[3];
				popup_height = temparray[4];
				// menu niveau 2 (avec lien)
				// modified by ben 11/05/2004
				//mv_aff += "<tr><td><a href='"+lien+"?pos="+a+"&pos2="+x+"' CLASS=mvstyle>"+titre+"</a></td></tr>";
				regExp = lien.search( 'module=' );		
				if ( regExp == -1 ){
               if ( temparray[2] == 2) {
                  // Affichage en popup
   					mv_aff += "<tr><td align=\"left\"><a href='#' onclick=\"window.open('"+lien+"', 'popup', 'toolbar=no,scrollbars=yes,height="+popup_height+",width="+popup_width+"');\" CLASS=mvstyle >"+titre+"</a></td></tr>";
					}else{
                  if ( temparray[2] == 1 ) {
                     //Affichage en nouvelle fenêtre
      					//mv_aff += "<tr><td align=\"left\"><a target=\"_blank\" href='"+lien+"' CLASS=mvstyle>"+titre+"</a></td></tr>";
      					mv_aff += "<tr><td align=\"left\"><a href='#' onclick=\"window.open('"+lien+"');\" CLASS=mvstyle>"+titre+"</a></td></tr>";
                  }else{
                     //Affichage normal
      					mv_aff += "<tr><td align=\"left\"><a href='"+lien+"' CLASS=mvstyle>"+titre+"</a></td></tr>";
                  }
					}
				}else{
               if ( temparray[2] == 2) {
                  // Affichage en popup
   					 mv_aff += "<tr><td align=\"left\"><a href='#' onclick=\"window.open('"+lien+"&pos="+a+"&pos2="+x+"', 'popup', 'toolbar=no,scrollbars=yes,height="+popup_height+",width="+popup_width+"');\" CLASS=mvstyle >"+titre+"</a></td></tr>";
               }else{
                  if ( temparray[2] == 1 ) {
                     //Affichage en nouvelle fenêtre
      					mv_aff += "<tr><td align=\"left\"><a href='#' onclick=\"window.open('"+lien+"&pos="+a+"&pos2="+x+"');\" CLASS=mvstyle>"+titre+"</a></td></tr>";
   					   //mv_aff += "<tr><td align=\"left\"><a target=\"_blank\" href='"+lien+"&pos="+a+"&pos2="+x+"' CLASS=mvstyle>"+titre+"</a></td></tr>";
                  }else{
                     //Affichage normal
	     				    mv_aff += "<tr><td align=\"left\"><a href='"+lien+"&pos="+a+"&pos2="+x+"' CLASS=mvstyle>"+titre+"</a></td></tr>";
                  }
               }
				}
				
				//end modif
			}
			mv_aff += "</table></td></tr>";
			if (a==(mv_menu.length-1)) {
				mv_aff += "<tr><td height='1' class='navigation' bgcolor='#1461B3'><img src='./img/menu/shim.gif' height='1' width=155></td></tr>";
			}
		} else {
			// menu niveau 1 fermé
			mv_aff += "<tr><td background='./img/menu/cartouche"+cartouche+".gif' height='15' class='navigation'>&nbsp;&nbsp;<A HREF='#' onClick='mv_pos="+a+";menu_draw()' CLASS=navigation>"+mv_menu[a]+"</a></td></tr>";

			if (a!=(mv_menu.length-1)) {
				mv_aff += "<tr><td height='8' background='./img/menu/fond_navig.gif' class='navigation'><img src='./img/menu/shim.gif' height='8'></td></tr>";
			}
		}
	}

	mv_aff += "</table>";

	if(document.getElementById){
		document.getElementById("mv").innerHTML = mv_aff;
	}else{
		document.write(mv_aff);
	}
}
