<?php

//On demarre les sessions pour ODYSSEE
session_start();

//  PHP Version 7.2.24-0ubuntu0.18.04.9
// Gardez-moi une place sur le vaisseau-mère 👽
/*
				MMM"""AMV `7MM"""YMM    .g8"""bgd `7MM"""Mq.`7MMF'   `7MF'.M"""bgd `7MMF'  `7MMF'`7MM"""YMM  `7MM"""Mq.  
				M'   AMV    MM    `7  .dP'     `M   MM   `MM. MM       M ,MI    "Y   MM      MM    MM    `7    MM   `MM. 
				'   AMV     MM   d    dM'       `   MM   ,M9  MM       M `MMb.       MM      MM    MM   d      MM   ,M9  
				   AMV      MMmmMM    MM            MMmmdM9   MM       M   `YMMNq.   MMmmmmmmMM    MMmmMM      MMmmdM9   
				  AMV   ,   MM   Y  , MM.           MM  YM.   MM       M .     `MM   MM      MM    MM   Y  ,   MM  YM.   
				 AMV   ,M   MM     ,M `Mb.     ,'   MM   `Mb. YM.     ,M Mb     dM   MM      MM    MM     ,M   MM   `Mb. 
				AMVmmmmMM .JMMmmmmMMM   `"bmmmd'  .JMML. .JMM. `bmmmmd"' P"Ybmmd"  .JMML.  .JMML..JMMmmmmMMM .JMML. .JMM.
					******************************************* 
						Developed by:  ZeCrusher <zecrusher@gmail.com>  																							  _                        

*/
	if (!file_exists("fonctions/fonctions_API.php")) 	{	
		header ('Location: 404.php&fonctions');
		exit();	
	} 
	else { 	
		include "fonctions/fonctions_API.php";
	}


// *****************************************************************************
// *****                       Historique des versions                     *****
// *****************************************************************************
// Copyright (c) 2022 - Serge Tsakiropoulos - Office de Tourisme de Martigues


?>

<head>
	<meta charset="utf-8">
	<meta content="width=device-width,initial-scale=1,shrink-to-fit=no" name="viewport">
	<link href="css/jost-v4-latin-regular.woff2" rel="preload" type="font/woff2">
	<link href="css/jost-v4-latin-700.woff2" rel="preload" type="font/woff2">
	<link href="css/aides.css" rel="stylesheet">
	
	<title>APIDAE > APICMD > OPENAGENDA</title>
</head>

/*
	Extranet / <- Cette arborescence fait partie d'un plus vaste projet qui est notre Extranet */ 

      ├── ajax 								-> Liste des fichiers .PHP pour calendar, notes, like, theme, rechercher
      ├── css								-> css de l'extranet
	  │    ├── style.css  					-> mise à jour le 02/08/2020 - star2 star1 - étoile dans le fond du site 
	  │    ├── stylewhite.css  				-> mise à jour le 22/10/2018 - béta - version white avec modif de beaucoup de css - voir $_SESSION['theme']	  
      ├── fichiers							-> Dossier du module "explorateur de fichier" dans l'extranet
      ├── fonts								-> Toutes les polices 
      ├── img/								
      │   ├── body							-> fond du site - image des thèmes
      │   ├── carousel/
      │   ├── color-picker
      │   ├── editor
      │   ├── filemanager
      │   ├── gallery	  
      │   ├── icon
      │   └── toolkit.scss
      ├── js/
      │   ├── bootstrap/				
      │   └── custom/
      ├── pages/
      │   └── blog 
      │    
      │   ├── navbar.php <- menu horizontal				
      │   └── sidebar.php <- menu vertical 
      ├── log/	  
      ├── media/	  
      │   ├── bootstrap-entypo.eot
      │   ├── bootstrap-entypo.svg
      │   ├── bootstrap-entypo.ttf
      │   ├── bootstrap-entypo.woff
      │   └── bootstrap-entypo.woff2
      └── php

*/	


<?php
	
	$_SESSION['last_version'] ="V2025-07-02-V4-TSK"; 


/* Nous avons ici l'identification d'un membre de l'extranet. Et cette page n'est autorisée qu'au membre du groupe */
/*	
	if (!isset($_SESSION['login']) || ($_SESSION['login']=="")) {
		header ('Location: authentication/sign-up/index.php?mode='.$_GET['mode']);
		exit();
	}
*/



/* Chargement des fichiers API - Si vous avez un fichier config.php qui contiendrait les clefs */ 
/*		$file = $_SERVER['DOCUMENT_ROOT']."/odyssee/apps/api/config.php";
		if (file_exists($file)) { include $file; } else {	echo "Le fichier config est introuvable.";	}		
*/



	$keys = array( /* Les clés API permettent de lire et écrire des données sur OpenAgenda via l'API. */
 	  "public"=>$_GET['public'], /* Pour OpenAgenda en lecture */
 	  "secret"=>$_GET['secret']  /* Pour OpenAgenda en mode �criture et autres*/
	);
	
	
	$agendaUid=14275573; /* <uid:65630513> */
	$territoireIds=array("5693912"); /* Conseil de territoire : Pays de Martigues */ 
 
	$selectionIds=array("130723","133484");
	
	$data_openagenda =array(); /* tableau qui va temporairement sauvegarder les données lu sur OpenAgenda 	*/
	$data_apidae	 =array(); /* tableau qui va également sauvegarder les données d'Apidae 				*/


	$apiDomain = "https://api.apidae-tourisme.com/api/";
	
	$apiKey=$_GET['apiKey']; 	/* QTfpNkyX <- OK | PhrnH4Dd */
	$projetId=$_GET['projetId']; 	/*  	6556 Martigues - OpenAgenda */ 
	// $nbResult = '200';
	// $dureemax = "50";

	$requete = array();

	$requete['territoireIds'] = $territoireIds;
	$requete['selectionIds'] = $selectionIds;
	$requete['identifiants'] = $identifiants;
	$requete['apiKey'] = $apiKey;
	$requete['projetId'] = $projetId;
	// $requete['dateDebut'] = date("Y-m-d");   // $requete['dateDebut'] = "2022-09-10";

//	$requete["responseFields"] = array("@all");
	// $requete["responseFields"] = array("@default");

 
	 $requete["responseFields"] = array("id",
										"nom",
										"theme",
										"localisation",
										"descriptionTarif",
										"presentation",
										"reservation",
										"prestations",
										"illustrations",
										"aspects",
										"informations",
										"datesOuverture",
										"ouverture",
										"@informationsObjetTouristique");

	
	$url_Apidae = $apiDomain."v002/agenda/detaille/list-objets-touristiques/";

	$url_Apidae .= '?';
	$url_Apidae .= 'query='.urlencode(json_encode($requete));

	$url_OpenAgenda="https://openagenda.com/agendas/".$agendaUid."/events.v2.json?key=".$keys['public'];
	
	$department		= 	"Bouches-du-Rhône";
	$timezone		=	"Europe/Paris";
	$countryCode 	= 	"FR";


/* Variables pré-définies */
$_SESSION['MODE_TEST']="test_"; // test_
$_SESSION['NB_BOUCLE']=1000; /* Une seule boucle à faire */
$nb_boucle=0;

/****************************************************************************
*****                       Composition du code  Index.php               *****
*****************************************************************************

Note dans la lecture des commentaires : 
// INFO ...> 		=> Informations supplémentaire
// WARNING ...> 	=> Cette ligne/bloc nécéssite un peu de maintenance et/ou d'optimisation
// ATTENTON ...> 	=> Le code est potentiellement dangereux et doit être corrigé au plus vite ! 
// TODO ...> 		=> Modifications à apporter ici ou idée suggérée à faire pour une futur version du code
 
			TODO ...>  	* Syncro l'agenda APIDAE avec l'agenda de l'extranet, sans pour cela sauvegarder les manif dans la base Mysql !
						* Faire un mode de TEST avec une variable $_SESSION

*/

	write_to_console(" Start API - 2025/2026 - TSK OT - Martigues");
	write_to_console("La requête : ");
	write_to_console(json_encode($requete));

	echo '<h2> API -> APIDAE vers OPENAGENDA </h2>';
	echo '<a href="'.$url_Apidae.'"> Liste sur APIDAE </a> | <a href="'.$url_OpenAgenda.'"> Liste sur OpenAgenda </a> | <a href="index.php"> Aide </a>';

	/*
	*---------------------------------------------------------------
	* Utilisation en écriture
	*---------------------------------------------------------------
	*	Note : A quoi servent-elle et comment trouver les clefs public et secret ?
	*
	* Pour commencer à utiliser l'API en écriture, envoyez une demande a support@openagenda.com 
	* pour activer un accès à une clé privée qui vous permettra ensuite de suivre une 
	* procédure d'authentification de type OAuth pour récupérer un token d'accès valide 
	* nécessaire aux opérations d'écriture.
	*  
	*  Vous pouvez consulter la documentation ici : 
	*	INFO ...>  : https://developers.openagenda.com/00-introduction/
	*/

	$accessToken = access_token_get($keys['secret']);
	write_to_console("Retour du AccessToken : ".$accessToken);
	
	if ($accessToken=="") {
		echo "<br><br>Erreur sur l'Obtention du Token d'accès.<br>";	
	}	

	// echo "<br /> <b>Lancement de l'API vers Apidae pour avoir un retour des données > </b><br />";
	// echo "URL API LECTURE : ".$url_Apidae."</br>";

	$results_API = API_Resource($url_Apidae);
	$results = json_decode($results_API,false);
	
	/* ******************************************************************************************************************************************************************************* */
	
	$results_OpenAgenda = API_Resource($url_OpenAgenda);
	$results_OA = json_decode($results_OpenAgenda,false);
	
	$route = "https://openagenda.com/agendas/".$agendaUid."/events.v2.json?key=".$keys['public'];
	write_to_console("Route : ".$route);
	$nbmanif=$results->numFound; 
	write_to_console('Nombre Occurence > '.$nbmanif);
	$retobjetsTouristiques = $results->objetsTouristiques;


	$obj = json_decode(file_get_contents($route), false);
	$openagenda_nb_event=$obj->total;

	$sizeapidae=0;

	foreach($retobjetsTouristiques as $fiche=>$lesdates)	{
		foreach ($lesdates as $retourfiche) 	{
			$data_apidae[$sizeapidae]=$retourfiche->nom->libelleFr;
			// echo $data_apidae[$sizeapidae]." <br>";
			$sizeapidae++;
			
		}
	}

	$unique_data_apidae=array_unique($data_apidae);

	echo "<hr>";

	$sizeopenagenda=0;
	do 	{
		$data_openagenda[$sizeopenagenda]=$obj->events[$sizeopenagenda]->title->fr;
		// echo $data_openagenda[$i]." <br>";
		
	} while ($obj->events[++$sizeopenagenda]->title->fr!="");
	
	$unique_data_openagenda=array_unique($data_openagenda);

	// echo "NB apidae=".$sizeapidae." | NB OpenAgenda ".$sizeopenagenda." <hr> ";	

	$data_update_apid_open = array_intersect($unique_data_openagenda, $unique_data_apidae);
	$data_creat_apid_open = array_diff($unique_data_openagenda, $unique_data_apidae);
	

	for($i = 0;$i<$sizeapidae;$i++) 
	{
		if ($unique_data_apidae[$i]!="") {
			$liste_apidae[$i]=$unique_data_apidae[$i];
			// echo "Sur APIDAE ->".$liste_apidae[$i]."<br>";
		}
	}
	

	$nb_maj=0;$nb_create=0;
	for($i = 0;$i<$sizeopenagenda;$i++) 
	{
		if ($unique_data_openagenda[$i]!="") { 
			$liste_openagenda[$i]=$unique_data_openagenda[$i];
			// echo "Sur OPENAGENDA ->".$liste_openagenda[$i]."<br>";
		}
	}
	
	$j=0;$i=0;$nb_maj=0;$nb_create=0;
	
    for($i = 0;$i<$sizeapidae;$i++) 
		for($j=0;$j<$sizeopenagenda;$j++) 
            if (($unique_data_apidae[$i] == $unique_data_openagenda[$j]) AND ($unique_data_apidae[$i]!=""))	
               $liste_maj_apidae_vers_open[$nb_maj++]=$unique_data_openagenda[$j]; 

	
	// echo "A MAJ >".$nb_maj."<br>";
	// for ($i=0;$i<$nb_maj;$i++) echo "A MAJ : ".$liste_maj_apidae_vers_open[$i]."<br>";
	// echo "<hr>";
	// echo "A CREER >".$nb_create;
	// echo "<hr>";

	// for ($i=0;$i<$nb_create;$i++) echo "A CREER : ".$liste_create_apidae_vers_open[$i]."<br>";
	

// INFO...
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
			write_to_console("Aucune erreur dans json_decode > JSON_ERROR_NONE " );
        break;
        case JSON_ERROR_DEPTH:
            write_to_console("Profondeur maximale atteinte > JSON_ERROR_DEPTH");
        break;
        case JSON_ERROR_STATE_MISMATCH:
            write_to_console("Inadéquation des modes ou underflow >JSON_ERROR_STATE_MISMATCH");
        break;
        case JSON_ERROR_CTRL_CHAR:
            write_to_console("Erreur lors du contrôle des caractères > JSON_ERROR_CTRL_CHAR");
        break;
        case JSON_ERROR_SYNTAX:
            write_to_console("Erreur de syntaxe ; JSON malformé > JSON_ERROR_SYNTAX");
        break;
        case JSON_ERROR_UTF8:
            write_to_console("Caractères UTF-8 malformés, probablement une erreur d\'encodage > JSON_ERROR_UTF8");
        break;
        default:
            write_to_console("Erreur inconnue");
        break;
    }


	/*
	*---------------------------------------------------------------
	* Début de la moulinette ^^'
	*---------------------------------------------------------------
	*/

	foreach($retobjetsTouristiques as $fiche=>$lesdates)
	{
		// echo 'Les Dates des événements : <b>"'. $fiche.'"</b> <br/>';
					
		foreach ($lesdates as $retourfiche) 
		{
				$json_event_date_ouverture = $retourfiche->ouverture;
				$event_heure_ouverture = array();
				
				$event_tarif=array();
				$nb_tarif=0;
				$nb_periode=0;


				$event_illustrations=array();
				$nb_illustrations=0;
			
				$modele_commercial=$retourfiche->descriptionTarif->indicationTarif;		
				$tarifsEnClair=$retourfiche->descriptionTarif->tarifsEnClair->libelleFr;	

				$event_adresse = $retourfiche->localisation;
				$event_adresse = ""; // init l'adresse
						
			/*
			 *---------------------------------------------------------------
			 * Création de l'adresse pour Openagenda
			 *---------------------------------------------------------------
			 *
			 *  Je sépare ici les différents champs par une virgule. Ce qui vous donnera
			 *  avec l'exemple ci-joint :
			 * 
			 * if (isset($retourfiche->localisation->adresse->adresse1)) {
			 *    $event_adresse.= $retourfiche->localisation->adresse->adresse1.", "; <- la virgule est ici
			 * }
			 * Quai d'honneur de l'Hôtel de Ville, Avenue Louis Sammut, Derrière l'hôtel de ville, 13500 Martigues, France 
			 *
			 *
			 * Les champs sont entre des guillemets "
			 *
			 * NOTE: exemple de la partie du JSON à analyser : 
			 *
			 *	"localisation" : {
			 *		"adresse" : {
			 *		"nomDuLieu" : "Quai d'honneur de l'Hôtel de Ville",
			 *		"adresse1" : "Avenue Louis Sammut",
			 *		"adresse2" : "Derrière l'hôtel de ville",
			 *		"codePostal" : "13500",
			 *		"commune" : {
			 *			"id" : 4467,
			 *			"code" : "13056",
			 *			"nom" : "Martigues",
			 *			"pays" : 
			 *			{
			 *				"elementReferenceType" : "Pays",
			 *				"id" : 532,
			 *				"libelleFr" : "France",
			 *				"ordre" : 78
			 *			},
			 *			"codePostal" : "13500"
			 *		}
			 *	},
			*/
		
			/*
			 *---------------------------------------------------------------
			 * tester l'existence du pays
			 *---------------------------------------------------------------
			 *			localisation->adresse->commune ->
						"pays" : {
			 *				"elementReferenceType" : "Pays",
			 *				"id" : 532,
			 *				"libelleFr" : "France",
			 *				"ordre" : 78
			 *			},
			 * Vous pouvez désactiver la partie Pays si toutes vos annonces sont en France.
			 */


		
/* GESTION DES ADRESSES */	

				if (isset($retourfiche->localisation->adresse->nomDuLieu)) 		{	$event_adresse.= $retourfiche->localisation->adresse->nomDuLieu.", ";	}
				if (isset($retourfiche->localisation->adresse->adresse1)) 		{	$event_adresse.= $retourfiche->localisation->adresse->adresse1.", ";	}
				if (isset($retourfiche->localisation->adresse->adresse2)) 		{	$event_adresse.= $retourfiche->localisation->adresse->adresse2.", ";	}
				if (isset($retourfiche->localisation->adresse->codePostal)) 	{	$event_adresse.= $retourfiche->localisation->adresse->codePostal." ";	}
				if (isset($retourfiche->localisation->adresse->commune->nom))	{	$event_adresse.= $retourfiche->localisation->adresse->commune->nom;		}
				
				if (isset($retourfiche->localisation->adresse->commune->pays->libelleFr)) 		{
					$event_adresse.=", ".$retourfiche->localisation->adresse->commune->pays->libelleFr." ";
				}
			 
				if ($retourfiche->localisation->geolocalisation->valide=="true") 				{
					$geolocalisation_long		=$retourfiche->localisation->geolocalisation->geoJson->coordinates['0'];
					$geolocalisation_lat		=$retourfiche->localisation->geolocalisation->geoJson->coordinates['1'];
					$complement_geolocalisation	=$retourfiche->localisation->geolocalisation->complement->libelleFr;
				}
				
				$nomDuLieu 	= $retourfiche->localisation->adresse->nomDuLieu;
				$codePostal = $retourfiche->localisation->adresse->codePostal;
				$ville		= $retourfiche->localisation->adresse->commune->nom;
				
				if ($_SESSION['MODE_TEST']=="test_") 		{ 
					$nomDuLieu="API_TEST.".$nomDuLieu;
					$titre_test="API_TEST.";
					$etat_test=true;
				}
				else {
					$etat_test=false;				
				}
				
/* CREATION DE L'ADRESSE */
				
				$Openagenda_event_adresse = array(
					'name' 			=> 	$nomDuLieu,
					'address' 		=> 	$event_adresse,
					'postalCode'	=> 	$codePostal,
					'city'			=> 	$ville,
					'department'	=>	$department,				/* Dans config.php car non présent dans le json d'APIDAE */
					'timezone'		=>	$timezone,					/* config.php */
					'countryCode' 	=>	$countryCode,				/* config.php */
					'latitude'		=> 	$geolocalisation_lat,
					'longitude'		=> 	$geolocalisation_long,
					'test'			=> 	$etat_test
				);

/*---------------------------------------------------------------
* Création du lieu dans OPENAGENDA
*---------------------------------------------------------------*/
				// enlever les // pour publier l'adresse de l'événement.
				 // $received_content_id_adresse = creation_localisation_event($accessToken,$Openagenda_event_adresse,$agendaUid);
			
				$result_loc=json_decode($received_content_id_adresse,false);
				// if ($result_loc->error!="") { /* En cas d'erreur, arrêt du script avec die !  */
					// die("Il existe une erreur dans la création de la Localisation.  ['".$result_loc->error."']");
				// }
				$result_uid_location=$result_loc->location->uid;
				write_to_console('uid de la création de la location > '.$result_loc->location->uid);

/* GESTION DES DATES */			
			$nb_date_ouverture=0;
				do 
				{
					/* Recherche des horaires - Ouvertures et fermetures ainsi que la date en cours  */
					$begin			=$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->dateDebut."T".$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->horaireOuverture;
					$end			=$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->dateFin  ."T".$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->horaireFermeture;
					$date_ouverture	=$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->dateDebut;
					$event_heure_ouverture[] = array('begin' => $begin, 'end' => $end);
					
				} 
				while ($json_event_date_ouverture->periodesOuvertures[++$nb_date_ouverture]->dateDebut!="");

/* GESTION DU HANDICAP  */		


/* GESTION DU PUBLIC  */		

	// echo "Type de Public :"				.$retourfiche->prestations->typesClientele[0]->libelleFr."<hr>";
	// echo "Type de animauxAcceptes :	"	.$retourfiche->prestations->animauxAcceptes."<hr>";
	// echo "Type de complementAccueil :"	.$retourfiche->prestations->complementAccueil->libelleFr."<hr>";
	// echo "Type de modesPaiement :"		.$retourfiche->descriptionTarif->modesPaiement[0]->libelleFr."<hr>";
	
/* GESTION DES TARIFS  */			
			$nb_periode=0;
				do 	{	
					do 	{
						$tarifs_minimum		=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->minimum;					
						$tarifs_maximum		=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->maximum;	
						$tarif_cible		=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->type->libelleFr;	
						$tarif_description	=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->type->description;	
						
						
						$event_tarif[$nb_tarif]= array('tarifs_minimum'=>$tarifs_minimum, 'tarifs_maximum'=>$tarifs_maximum, 'tarif_cible'=>$tarif_cible, 'description'=>$tarif_description);
					} 
					while ($retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[++$nb_tarif]->minimum!="");		
				} 
				while ($retourfiche->descriptionTarif->periodes[++$nb_periode]->tarifs[$nb_tarif]->minimum!="");				

/* GESTION DES PHOTOS */		
			$nb_illustrations=0;
				do 
				{
					/* Taille d'origine de la photo avec traductionFichiers[0]->url*/ 
					//	$photo_url	= $retourfiche->illustrations[$nb_illustrations]->traductionFichiers[0]->url;

					/* Taille de la photo réduite avec traductionFichiers[0]->urlDiaporama */ 
					$photo_url			= $retourfiche->illustrations[$nb_illustrations]->traductionFichiers[0]->urlDiaporama;
					$photo_copyright 	= $retourfiche->illustrations[$nb_illustrations]->traductionFichiers[0]->copyright->libelleFr;
					$photo_fileName		= $retourfiche->illustrations[$nb_illustrations]->traductionFichiers[0]->fileName;
					$photo_libelleFr	= $retourfiche->illustrations[$nb_illustrations]->traductionFichiers[0]->nom->libelleFr;
					
					// 	Il existe plusieurs méthodes pour extraire le nom d'un fichier d'un chemin complet.
					// $file = basename($photo_url);
					// echo "<hr>".$file."<br>";
					
				$event_illustrations[$nb_illustrations] = array('photo_url'	=> $photo_url, 'photo_copyright' => $photo_copyright,'photo_fileName'=> $photo_fileName,'photo_libelleFr'=> $photo_libelleFr);
					
				} 
				while ($retourfiche->illustrations[++$nb_illustrations]->traductionFichiers[0]->url!="");
				
/* GESTION DES DONNEES DE BASE */	

				if (isset($retourfiche->nom->libelleFr) && !empty($retourfiche->nom->libelleFr))	{
					$titre = $retourfiche->nom->libelleFr;
				} 
				else	{
					continue;
				}

				$descriptifCourt	= $retourfiche->presentation->descriptifCourt->libelleFr;
				$descriptifDetaille	= $retourfiche->presentation->descriptifDetaille->libelleFr;


				$letterslug = array("'", " ");	 /* 1 - Construction de slug ... la menace de Namek ? */ 
				$search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
				//Préférez str_replace à strtr car strtr travaille directement sur les octets, ce qui pose problème en UTF-8
				$replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
				$titre_replace = str_replace($search, $replace, $titre);
				$slug = str_replace($letterslug, "-", strtolower($titre_replace));
				
				/* Après analyse du retour du fichier Json OPENAGENDA MARTIGUES - Il doit sans doute en manquer ! 
					type public >
						20 = Familles , 
						25 = Tout public,
						17 = Adultes,
						18 = Enfants, 
						24 = Adolescents
				*/
				$Openagenda_event_data = array(
					  'title' => array(
						'fr' => $titre_test.$titre
					  ),
					  'state' => 0, /* 0: événement non publié, à contrôler - 1: événement non publié, controlé - 2: événement publié (valeur par défaut) */
					  'image' 			=> 	array('url' => $event_illustrations[0]['photo_url']),
					  'imageCredits'	=>	$event_illustrations[0]['photo_copyright'],
					  'locationUid' 	=> 	$result_uid_location,
					  'longDescription' => 	array('fr'=> $descriptifDetaille),
					  'description' 	=> 	array('fr'=> substr($descriptifCourt, 0, 190).'...'), /* Champ obligatoire ne pouvant excéder 200 caractères par langue */
					  'public' 			=> 	25,	
					  'nature' 			=> 	57, /* NON DOCUMENTÉ */
					  'thematique' 		=>	2,  /* NON DOCUMENTÉ */
					  'fadas' 			=>	46, /* NON DOCUMENTÉ */
					  'featured'		=> false, /* true quand l'événement doit apparaître en tête de liste ( en une ) */
					  'keywords' 		=> 	array('fr' => "Visite, Photographie, Nature"),
					  'timings' 		=> 	$event_heure_ouverture,
					  'slug' 			=>	$slug					  
					);
					
					
				$ds=json_encode($Openagenda_event_data);

			
/* CREATION DE L'EVENEMENT ------------------------------------------------------------------------------------------------------- */

				/*****************************************************************************************/
				 // $received_content_id_event = creation_event($accessToken,$Openagenda_event_data,$agendaUid);
				/*****************************************************************************************/
				
				$result_event=json_decode($received_content_id_event,false);
				// if ($result_event->error!="") { /* En cas d'erreur, arrêt du script !  */
					// die("Il existe une erreur dans la création de la Localisation.  ['".$result_event->error."']");
				// }

				// write_to_console('uid de la création EVENEMENT > '.$result_event);

/* ----------------------------------------------------------------------------------------------------------------------------- */
				
				echo "<br/><br/>";
				echo '<img src="'.$event_illustrations[0]['photo_url'].'" width="300px" height="240px" style="border-radius:8px;float:right;">';
				echo "<br>";
				echo "├state > [<b>". $retourfiche->state."</b>] <br />";		
				echo "├titre/nom > [<b>".$titre."</b>] <br />";	
				echo "├descriptifCourt > [<b>". substr($retourfiche->presentation->descriptifCourt->libelleFr, 0, 80)."...</b>] <br />";	
				echo "├descriptifDetaille > [<b>".	substr($retourfiche->presentation->descriptifDetaille->libelleFr, 0, 80)."...</b>] <br />";	
				echo "├Note > [<b>".				substr($retourfiche->presentation->typologiesPromoSitra[0]->libelleFr, 0, 80)."...</b>] <br />";	
				echo "├Communication > [<b>". $retourfiche->informations->moyensCommunication[0]->type->libelleFr."</b> │ <b>". $retourfiche->informations->moyensCommunication[0]->coordonnees->fr."</b>] <br />";	
				echo "├Site > [<b>".$retourfiche->gestion->membreProprietaire->siteWeb."</b>] <br />";		
				echo "├date > [<b>". $retourfiche->ouverture->periodeEnClair->libelleFr."</b>] <br />";		
				echo "├Adresse > [<b>". $event_adresse."</b>] <br />";
				echo "├reservation > [<b>". $retourfiche->reservation->organismes[0]->moyensCommunication[0]->coordonnees->fr."</b>] <br />";
				for  ($j=0;$j<$nb_date_ouverture;$j++) 
				echo "├ Période : ".$event_heure_ouverture[$j]['date']." -> Debut <b>".$event_heure_ouverture[$j]['begin']." </b>│ Fin :".$event_heure_ouverture[$j]['end']."</br>";
				echo "</pre>";	
				echo "├Nombre de tarif : [<b>". $nb_tarif."</b>] - TarifsEnClair : <b>".$tarifsEnClair."</b><br />";					
				for  ($j=0;$j<$nb_tarif;$j++) 
					echo "├ tarifs_minimum : ".$event_tarif[$j]['tarifs_minimum']." -> tarifs_maximum <b>".$event_tarif[$j]['tarifs_maximum']." </b>│ cible_tarif :".$event_tarif[$j]['tarif_cible']."</br>";
				echo "</pre>";	
				echo "├Adresse > [<b>". $event_adresse."</b>] <br />";
				echo "└geolocalisation > [<b>". $geolocalisation_lat.",". $geolocalisation_long."</b>] <br /><hr>";						
				
/* ----------------------------------------------------------------------------------------------------------------------------- */
			
		}  // de foreach ($lesdates as $retourfiche) 

	} // de	foreach($retobjetsTouristiques as $fiche=>$lesdates)

?>
