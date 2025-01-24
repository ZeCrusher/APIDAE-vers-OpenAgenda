function GetUserDetails(id) { //,nom, objet, designation) {
	
	$("#hidden_user_id").val(id);
    $("#nom").val(nom);
	var info="";
    $.get("ajax/readuserdetails.php", {
            id: id
        },
        function (data, status) {
            // PARSE json data
            var chrono = JSON.parse(data);
            // Assing existing values to the modal popup fields
            var nom=chrono[0].nom;
            var designation=chrono[0].designation;
			var objet=chrono[0].objet;
			var nom_chrono = $("#nom").val();
			var hidden_user_id = id;
			
			info ="<div class=\"form-group\">"+
                    "<label for=\"nom\">Nom du Chrono ("+id+")</label>"+
                    "<input type=\"text\" id=\"nom\" name=\"nom\" placeholder=\"Nom\" class=\"form-control\"/ value=\""+nom+"\" >"+
					"</div>"+
					"<br>"+
					
                "<div class=\"form-group\">"+
                    "<label for=\"designation\">Désignation</label>"+
                    "<input type=\"text\" id=\"designation\" name=\"designation\" placeholder=\"Désignation de votre chrono\" value=\""+designation+"\" class=\"form-control\"/>"+
                "</div>"+

                "<div class=\"form-group\">"+
                    "<label for=\"objet\">Objet de votre chrono</label>"+
                    "<input type=\"text\" id=\"objet\" name=\"objet\" placeholder=\"Objet de votre chrono\" value=\""+objet+"\" class=\"form-control\"/>"+
					"<input type=\"hidden\" name=\"hidden_user_id\" id=\"hidden_user_id\" value=\""+hidden_user_id+"\" />"+
                "</div>";
			
			$('#eventInfo').html(info);
        }
    ); 
    // Open modal popup
    $("#update_user_modal").modal("show");  
}


/*	┌───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
	│	Fonction GetsupportDetails																																										│
	├───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
	│	Les Paramètres :	id -> onclick="GetsupportDetails(\''.$data['id'].'\')"																													 	│
	│																																																	│
	│																																																	│
 	└───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
*/
function GetsupportDetails(id) { 
	
	$("#hidden_maj_support_id").val(id);
    // $("#titresupport").val(titresupport);
	var info="";
    $.get("ajax/ajax-support.php", {
            id: id /* paramètre envoyé dans le fichier php */
        },
        function (data, status) {
            var jsonsupport = JSON.parse(data);
            var id=jsonsupport[0].id;            
			var login=jsonsupport[0].login;
            var service=jsonsupport[0].service;
			var niveau_urgent=jsonsupport[0].niveau_urgent; 
			var datesupport=jsonsupport[0].datesupport;
			var titresupport=jsonsupport[0].titresupport;
			
			var supportpour=jsonsupport[0].supportpour;
			var supportpar=jsonsupport[0].supportpar;
			var supportsur=jsonsupport[0].supportsur;
			var supporturgent=jsonsupport[0].supporturgent;
			
			var support_check=jsonsupport[0].support_check;
		
			var support_via_agent1=jsonsupport[0].support_via_agent1;
			var support_via_agent2=jsonsupport[0].support_via_agent2;
			var support_via_agent3=jsonsupport[0].support_via_agent3;
			var support_via_agent4=jsonsupport[0].support_via_agent4;
			var support_via_agent5=jsonsupport[0].support_via_agent5;
			var support_via_agent6=jsonsupport[0].support_via_agent6;
			var support_via_autre=jsonsupport[0].support_via_autre;
			var notesupport=jsonsupport[0].notesupport;
			var avancement=jsonsupport[0].avancement;
			var objet=jsonsupport[0].objet; //
			
			var jssupport_check_modification="";
			var jssupport_check_amelioration="";
			var jssupport_check_bug="";
			var jssupport_check_creat="";
			var jssupport_check_bureautique="";
			
			
			
			var hidden_maj_support_id = id;
			var check_agent1, check_agent2, check_agent3, check_agent4, check_agent5, check_agent6;
			
			if (support_via_agent1==="on") check_agent1="checked";
			if (support_via_agent2==="on") check_agent2="checked";
			if (support_via_agent3==="on") check_agent3="checked";
			if (support_via_agent4==="on") check_agent4="checked";
			if (support_via_agent5==="on") check_agent5="checked";
			if (support_via_agent6==="on") check_agent6="checked";

			if (support_check==="support_check_modification") 	jssupport_check_modification="checked";
			if (support_check==="support_check_amelioration") 	jssupport_check_amelioration="checked";
			if (support_check==="support_check_bug") 			jssupport_check_bug="checked";
			if (support_check==="support_check_creat") 			jssupport_check_creat="checked";
			if (support_check==="support_check_bureautique") 	jssupport_check_bureautique="checked";	
				
			info ="<center><h5>Sélection de la demande (Fiche "+id+" par "+login+")</h5></center>"+
				"<p>"+
					"<div class=\"col-md-3 m-b-15\">"+
					"<p>État</p>"+
						"<select class=\"form-control input-sm\" id=\"edit_avancement\" name=\"avancement\" \">"+
							"<option>"+avancement+"</option>"+
							"<option>A faire</option>"+
							"<option>Fait</option>"+
							"<option>En cours</option>"+
							"<option>A l'étude</option>"+
							"<option data-divider=\"true\">&nbsp;</option>"+
							"<option>Annulée</option>"+
						"</select>"+
					"</div>"+
					"<div class=\"col-md-6 m-b-10\">"+
						"<p>Support Office</p> "+
						"<input type=\"text\" class=\"form-control input-sm\"  name=\"titresupport\" id=\"edit_titresupport\" placeholder=\"Titre de la demande...\" value=\""+titresupport+"\">"+
					"</div>"+
					"<div class=\"col-md-3 m-b-10\">"+
					"<p>Date de la demande</p>"+
						"<div class=\"input-icon datetime-pick date-only\">"+
							"<input data-format=\"dd/MM/yyyy\" name=\"datesupport\" id=\"edit_datesupport\" type=\"text\" class=\"form-control input-sm\"  value=\""+datesupport+"\" />"+
							"<span class=\"add-on\">"+
								"<i class=\"sa-plus\"></i>"+
							"</span>"+
						"</div>"+
					"</div>"+
				"</p>"+
				
				"<div class=\"clearfix\"></div>"+
					"<p>"+
						"<div class=\"col-md-3 m-b-10\">"+
							"<select class=\"form-control input-sm\" id=\"edit_supportpour\" name=\"supportpour\"  >"+
								"<optgroup label=\"Services\">"+
									"<option>"+supportpour+"</option>"+								
									"<option>le Service Accueil</option>"+
									"<option>le Service Groupe</option>"+
									"<option>l'Administration</option>"+
									"<option>la Direction</option>"+
									"<option>B.I.T</option>"+
									"<option>Informatique</option>"+
								"</optgroup>"+
									"<optgroup label=\"Autres\">"+
									"<option>Mairie</option>"+
									"<option>Prestataire</option>"+
								"</optgroup>"+
							"</select>"+
						"</div>"+
											
				"<div class=\"col-md-3 m-b-15\">"+
					"<select class=\"form-control input-sm\"  id=\"edit_supportpar\" name=\"supportpar\">"+
						"<option>"+supportpar+"</option>"+							
						"<option>Par téléphone</option>"+
						"<option>Par e-mail</option>"+
						"<option>Par l'Agent</option>"+
						"<option>Observation</option>"+
						"<option data-divider=\"true\">&nbsp;</option>"+
						"<option>Autres</option>"+
					"</select>"+
				"</div>"+
				"<div class=\"col-md-3 m-b-15\">"+
					"<select class=\"form-control input-sm\"  id=\"edit_supportsur\" name=\"supportsur\">"+
						"<option>"+supportsur+"</option>"+						
						"<option>Ingenie</option>"+
						"<option>Patio</option>"+									
						"<option>APIDAE</option>"+											
						"<option>Matériel</option>"+
						"<option>Réseaux Sociaux</option>"+
						"<option>Pack Office</option>"+	
						"<option>Site Internet</option>"+
						"<option data-divider=\"true\">&nbsp;</option>"+
						"<option>Autres</option>"+
					"</select>"+
				"</div>"+
				"<div class=\"col-md-3 m-b-15\">"+
					"<select class=\"form-control input-sm\"  id=\"edit_supporturgent\" name=\"supporturgent\">"+
						"<option>"+supporturgent+"</option>"+
						"<option>Fait immédiatement</option>"+
						"<option>Non Urgent</option>"+
						"<option>Urgent</option>"+
						"<option>Très Urgent/Critique</option>"+
						"<option data-divider=\"true\">&nbsp;</option>"+
						"<option>Observation</option>"+
					"</select>"+
				"</div>"+
				"</p>"+
				"<div class=\"clearfix\"></div>"+
				"<h3 class=\"block-title\">Type de la demande :</h3>"+
				"<p>"+
					"<div class=\"clearfix\"></div>"+
					"<div class=\"checkbox m-b-5\"><label><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"radio\" 	id=\"edit_support_check_modification\"		value=\"support_check_modification\" 	name=\"support_check\" "+jssupport_check_modification+">Il s'agit d'une mise à jour / Modif</label></div>"+
					"<div class=\"clearfix\"></div>"+
					"<div class=\"checkbox m-b-5\"><label><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"radio\" 	id=\"edit_support_check_amelioration\"		value=\"support_check_amelioration\" 	name=\"support_check\" "+jssupport_check_amelioration+">Il s'agit d'une amélioration / Paramétrage</label></div>"+
					"<div class=\"clearfix\"></div>"+
					"<div class=\"checkbox m-b-5\"><label><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"radio\"  id=\"edit_support_check_bug\"				value=\"support_check_bug\" 			name=\"support_check\" "+jssupport_check_bug+">Il s'agit d'un bug / d'une panne</label></div>"+
					"<div class=\"clearfix\"></div>"+
					"<div class=\"checkbox m-b-5\"><label><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"radio\"  id=\"edit_support_check_creat\"				value=\"support_check_creat\" 			name=\"support_check\" "+jssupport_check_creat+">Il s'agit d'une création</label></div>"+
					"<div class=\"clearfix\"></div>"+
					"<div class=\"checkbox m-b-5\"><label><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"radio\"  id=\"edit_support_check_bureautique\"		value=\"support_check_bureautique\" 	name=\"support_check\" "+jssupport_check_bureautique+">Assistance Bureautique</label></div>"+
					"<div class=\"clearfix\"></div>"+										
					"<div class=\"clearfix\"></div>"+
										
					"<h3 class=\"block-title\">Les intervenants</h3>"+
						"<div class=\"clearfix\"></div>"+
							"<label class=\"checkbox-inline\"><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"checkbox\" id=\"edit_support_via_agent1\" name=\"support_via_agent1\" "+check_agent1+"> Myriam </label>"+
							"<label class=\"checkbox-inline\"><input style=\"opacity: 1; margin: 0px 8x 4px;\" type=\"checkbox\" id=\"edit_support_via_agent2\" name=\"support_via_agent2\" "+check_agent2+"> Serge </label>"+
							"<label class=\"checkbox-inline\"><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"checkbox\" id=\"edit_support_via_agent3\" name=\"support_via_agent3\" "+check_agent3+"> Adm</label>"+
							"<label class=\"checkbox-inline\"><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"checkbox\" id=\"edit_support_via_agent4\" name=\"support_via_agent4\" "+check_agent4+"> Direction</label>"+
							"<label class=\"checkbox-inline\"><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"checkbox\" id=\"edit_support_via_agent5\" name=\"support_via_agent5\" "+check_agent5+"> Les Services</label><br><br>"+
							"<label class=\"checkbox-inline\"><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"checkbox\" id=\"edit_support_via_agent6\" name=\"support_via_agent6\" "+check_agent6+"> Autres : </label>	"+
							"<label class=\"checkbox-inline\"><input style=\"opacity: 1; margin: 0px 8px 4px;\" type=\"text\" class=\"form-control input-sm\" value=\""+support_via_autre+"\" id=\"edit_support_via_autre\" name=\"support_via_autre\" placeholder=\"...\" ></label>"+
							"<div class=\"clearfix\"></div>"+
							"<h3 class=\"block-title\">Note sur la demande</h3>"+
							"<input type=\"hidden\" name=\"hidden_maj_support_id\" id=\"hidden_maj_support_id\" value=\""+id+"\" />"+
							"<textarea class=\"input-sm form-control auto-size\" placeholder=\"...\" name=\"notesupport\" id=\"edit_notesupport\">"+notesupport+"</textarea>"+
				"</p>";

			
			$('#SupportInfo').html(info);
        }
    ); 
    $("#update_support_modal").modal("show");  
	
}


/*	┌───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
	│	Fonction GetsupportDetails																																										│
	├───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
	│	Les Paramètres :	id -> onclick="GetsupportDetails(\''.$data['id'].'\')"																													 	│
	│																																																	│
	│																																																	│
 	└───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
*/
function GetMaterielDetails(id) { 
	
	$("#hidden_maj_materiel_id").val(id);

	var info="";
    $.get("ajax/ajax-edit-materiel.php", {
            id: id /* paramètre envoyé dans le fichier php */
        },
        function (data, status) {
            var jsonmateriel = JSON.parse(data);
            var id=jsonmateriel[0].id;   
            var nbpiece=jsonmateriel[0].nbpiece;   
            var service=jsonmateriel[0].service;   
            var title=jsonmateriel[0].title;   
            var disponibilite=jsonmateriel[0].disponibilite;   
            var numerodeserie=jsonmateriel[0].numerodeserie;   
            var note=jsonmateriel[0].note;   
            var informationsgenerales=jsonmateriel[0].informationsgenerales;   
            var fichetechnique=jsonmateriel[0].fichetechnique;   
            var etat=jsonmateriel[0].etat;   
			
            var photo=jsonmateriel[0].photo;   
            var referenceInterne=jsonmateriel[0].referenceInterne;   			
			
			var prix=jsonmateriel[0].prix;
            var dateachat=jsonmateriel[0].dateachat;
			var findegarantie=jsonmateriel[0].findegarantie; 
			var integredans=jsonmateriel[0].integredans;
			var motclef=jsonmateriel[0].motclef;
			
			var supportpour=jsonmateriel[0].supportpour;
			var poids=jsonmateriel[0].poids;
			var OS=jsonmateriel[0].OS;
			var RAM=jsonmateriel[0].RAM;
			var url=jsonmateriel[0].url;			
			
			var localisation=jsonmateriel[0].localisation;
		
			var utilisateur=jsonmateriel[0].utilisateur;
			var service=jsonmateriel[0].service;
			var numinventaire=jsonmateriel[0].numinventaire;
			var referencecompta=jsonmateriel[0].referencecompta;
			var referenceconstructeur=jsonmateriel[0].referenceconstructeur;
			var entretienfrequencemois=jsonmateriel[0].entretienfrequencemois;
			var entretienparqui=jsonmateriel[0].entretienparqui;
			var entretiendetail=jsonmateriel[0].entretiendetail;
			var entretiensuivant=jsonmateriel[0].entretiensuivant;
			var categorie=jsonmateriel[0].categorie; //
			
			info =				"<form  id=\"SupportForm\"  method=\"POST\" role=\"form\" action=\"hardware.php\"  class=\"form-validation-1\">"+ 
								"	<h3 class=\"block-title\">Modification en cours... </h3>"+
								"		<p>"+
								"			<div class=\"col-md-2 m-b-15\">"+
								"				<p>Disponibilité</p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"disponibilite\" id=\"disponibilite\"  value=\""+disponibilite+"\" placeholder=\"Nom du service\" >"+
								"			</div>"+
								"			<div class=\"col-md-6 m-b-10\">"+
								"				<p>Nom : </p> "+
								"				<input type=\"text\" class=\"form-control input-sm validate[required]\" name=\"title\" id=\"title\" value=\""+title+"\" placeholder=\"Nom ou référence\" >"+
								"			</div>"+
								"			<div class=\"col-md-4 m-b-10\">"+
								"				<p>Date d'achat </p>"+
								"				<div class=\"input-icon datetime-pick date-only\">"+
								"					<input name=\"dateachat\" id=\"dateachat\" type=\"text\" value=\""+dateachat+"\"  class=\"form-control input-sm\" />"+
								"					<span class=\"add-on\"><i class=\"sa-plus\"></i></span>"+
								"				</div>"+
								"			</div>"+
								"		</p>"+
								"		<div class=\"clearfix\"></div>"+
								"		<p>"+
								"				<div class=\"col-md-3 m-b-10\">"+
								"				<p>Utilisé par :</p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"service\" id=\"service\" value=\""+service+"\" placeholder=\"Nom du service\" >"+
								"				</div>"+
								"				<div class=\"col-md-3 m-b-15\">"+
								"				<p>Agent </p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"utilisateur\" id=\"utilisateur\" value=\""+utilisateur+"\" placeholder=\"Nom de l'Agent \" >"+										
								"			</div>"+
								"			<div class=\"col-md-3 m-b-10\">"+
								"			<p>Catégorie  </p>"+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"categorie\" id=\"categorie\" value=\""+categorie+"\" placeholder=\"Categorie \" >"+		
								
								"			</div>"+
								"			<div class=\"col-md-3 m-b-15\">"+
								"				<p>Autres cat. : </p>"+
								"				<input type=\"text\" class=\"form-control input-sm\" name=\"motclef\" id=\"motclef\" value=\""+motclef+"\" placeholder=\"Nom ou référence\" >"+
								"			</div>"+
								"		</p>"+
								"		<h3 class=\"block-title\">Fiche technique</h3>"+
								"		<div class=\"clearfix\"></div>"+
								"		<p>"+
								"			<div class=\"col-md-6 m-b-15\">"+
								"				<p>Num. série</p> "+
								"				<input type=\"text\" class=\"form-control input-sm validate[required]\" name=\"numerodeserie\" id=\"numerodeserie\" value=\""+numerodeserie+"\" placeholder=\"Numéro de série\" >"+
								"			</div>"+
								"			<div class=\"col-md-6 m-b-15\">"+
								"				<p>Réference constructeur</p>"+
								"				<input type=\"text\" class=\"form-control input-sm\" name=\"referenceconstructeur\" id=\"referenceconstructeur\" value=\""+referenceconstructeur+"\" placeholder=\"Référence constructeur\" >"+
								"			</div>"+
								"		</p>"+
								"		<p>"+
								"			<div class=\"col-md-4 m-b-8\">"+
								"				<p>Lieu de stockage</p> "+
								"				<input type=\"text\" class=\"form-control input-sm validate[required]\" name=\"localisation\" id=\"localisation\" value=\""+localisation+"\" placeholder=\"Où se trouve le matériel\" >	 "+
								"			</div>"+
								"			<div class=\"col-md-4 m-b-8\">"+
								"				<p>Référence comptable </p> "+
								"				<input type=\"text\" class=\"form-control input-sm validate[required]\" name=\"referenceconstructeur\" id=\"referenceconstructeur\" value=\""+dateachat+"\" placeholder=\"Référence comptable\" >"+
								"			</div>"+
								"			<div class=\"col-md-4 m-b-8\">"+
								"				<p>Etat </p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"etat\" id=\"etat\" value=\""+etat+"\" placeholder=\"Etat ! \" >"+		
								"			</div>		"+													
								"		</p>"+
								"		<div class=\"clearfix\"></div>"+
								"		<p>"+
								"			<div class=\"col-md-2 m-b-8\">"+
								"				<p>Prix d'achat</p> "+
								"				<input type=\"text\" class=\"form-control input-sm\" name=\"prix\" id=\"prix\" value=\""+prix+"\" placeholder=\"Prix en €\" >	 "+
								"			</div>"+
								"			<div class=\"col-md-3 m-b-8\">"+
								"				<p>RAM</p> "+
								"				<input type=\"text\" class=\"form-control input-sm\" name=\"RAM\" id=\"RAM\" value=\""+RAM+"\" placeholder=\"En Go\" >"+
								"			</div>"+
								"			<div class=\"col-md-3 m-b-8\">"+
								"				<p>Poids</p>"+
								"				<input type=\"text\" class=\"form-control input-sm\" name=\"poids\" id=\"poid\" value=\""+poids+"\" placeholder=\"Kilos\" >"+
								"			</div>							"+								
								"			<div class=\"col-md-4 m-b-8\">"+
								"				<p>OS </p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"OS\" id=\"OS\" value=\""+OS+"\" placeholder=\"OS ... \" >"+								
								"			</div>"+
								"		</p>"+
								"		<div class=\"clearfix\"></div>"+
								"	<p>"+
								"		<div class=\"col-md-4 m-b-8\">"+
								"			<p>Entretien périodique </p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"entretienfrequencemois\" value=\""+entretienfrequencemois+"\" id=\"entretienfrequencemois\" placeholder=\"En mois\" >	 "+
								"		</div>"+
								"		<div class=\"col-md-4 m-b-8\">"+
								"			<p>Par qui</p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"entretienparqui\" id=\"entretienparqui\" value=\""+entretienparqui+"\" placeholder=\"Nom / Société\" >"+
								"		</div>"+
								"		<div class=\"col-md-4 m-b-8\">"+
								"			<p>Entretien suivant </p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"entretiensuivant\" id=\"entretiensuivant\" value=\""+entretiensuivant+"\" placeholder=\"Date\" >"+
								"		</div>"+
								"	</p>"+
								"	<div class=\"clearfix\"></div>	"+
								"	<p>"+
								"		<div class=\"col-md-12 m-b-8\">"+
								"			<p>Photo disponible sur le Serveur Extranet :</p> "+
								"			<input type=\"text\" class=\"form-control input-sm\" name=\"photo\" id=\"photo\" value=\""+photo+"\" placeholder=\"Photo\" >"+								
								"			<div class=\"clearfix\"></div>"+													
								"		</div>"+
								"	</p>"+							
								"	<div class=\"clearfix\"></div>"+
								"	<input type=\"hidden\" id=\"hidden_creation_materiel_id\" value=\"maj\" name=\"hidden_maj_materiel_id\">"+
								"	<input type=\"hidden\" id=\"id\" value=\""+id+"\" name=\"id\">"+				
								"	<h3 class=\"block-title\">NOTE OU REMARQUE SUR LE MATERIEL : </h3>"+
								"	<div class=\"clearfix\"></div>"+
								"	<textarea class=\"input-sm form-control auto-size\" placeholder=\"...\" name=\"note\" id=\"note\"  rows=\"6\" style=\"overflow: hidden; overflow-wrap: break-word; resize: none; height: 102px;\">"+note+"</textarea>"+
								"	<input type=\"hidden\" id=\"hidden_edition_materiel_id\" value=\"create\" name=\"hidden_creation_materiel_id\">"+
								"	<div class=\"clearfix\"></div>"+
								"	<br>"+
								"</form>";

			
			$('#MaterielInfo').html(info);
        }
    ); 
    $("#update_materiel_modal").modal("show");  
	
}



