<?php //On demarre les sessions
session_start();

// *****************************************************************************
// *****                       Historique des versions                     *****
// *****************************************************************************
// Copyright (c) 2022/2025 - Serge Tsakiropoulos - Office de Tourisme de Martigues

/*
A FAIRE : Si il n'existe pas de période de fin dans les horaires, alors il faut soit dupliquer l'horaire, soir faire
un ajout de 2 heures à l'horaire d'ouverture
*/

	/* if (!file_exists("config/config.php")) {	
		header ('Location: 404.php?config');
		exit();	
	} 
	else { 	
		include "config/config.php";
	} */
	
	if (!file_exists("fonctions/fonctions_API.php")) 	{	
		header ('Location: 404.php?fonctions');
		exit();	
	} 
	else { 	
		include "fonctions/fonctions_API.php";
	}

	
	$_SESSION['last_version'] ="V2022-11-17-V1-TSK"; 
 
	$keys = array( 
 	  "public"=>$_GET['public'], 
 	  "secret"=>$_GET['secret'] 
	);
 
	$agendaUid=65630513; 
	$territoireIds=array("5693912"); /* Conseil de territoire : Pays de Martigues */ 
	$selectionIds=array($_GET['selectionIds']);
	
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
	// $requete['dateDebut'] = date("Y-m-d");   
	// $requete['dateDebut'] = "2022-09-10";

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

	
	$accessToken = access_token_get($keys['secret']);

	$results_API = API_Resource($url_Apidae);
	$results = json_decode($results_API,false);

	$results_OpenAgenda = API_Resource($url_OpenAgenda);
	$results_OA = json_decode($results_OpenAgenda,false);
	

	$route = "https://openagenda.com/agendas/".$agendaUid."/events.v2.json?key=".$keys['public'];
	
	$nbmanif=$results->numFound; 

	$retobjetsTouristiques = $results->objetsTouristiques;

	$obj = json_decode(file_get_contents($route), false);
	$openagenda_nb_event=$obj->total;
	
	$sizeapidae=0;

	foreach($retobjetsTouristiques as $fiche=>$lesdates)	
	{
		foreach ($lesdates as $retourfiche)	
		{
			$data_apidae[$sizeapidae]=$retourfiche->nom->libelleFr;
			$lecture_arr_id[$sizeapidae]=$retourfiche->id;
			$sizeapidae++;
		}
	}
	$arr_id=array_unique($lecture_arr_id);

	$sizeopenagenda=0;
	do 	{
		
		$data_openagenda	[$sizeopenagenda]=$obj->events[$sizeopenagenda]->title->fr;
		$data_openagenda_uid[$sizeopenagenda]=$obj->events[$sizeopenagenda]->uid;
		$data_openagenda_loc[$sizeopenagenda]=$obj->events[$sizeopenagenda]->location->uid;		
	// echo $data_openagenda_loc[$sizeopenagenda]." ** <br>";
	} 
	while ($obj->events[++$sizeopenagenda]->title->fr!="");
	
	$unique_data_openagenda				= array_unique($data_openagenda);
	$unique_data_openagenda_uid			= array_unique($data_openagenda_uid);	
	$unique_data_openagenda_loc			= array_unique($data_openagenda_loc);
		
	$data_update_apid_open 				= array_intersect($unique_data_openagenda, $titre_unique_data_apidae);
	$data_creat_apid_open 				= array_diff($unique_data_openagenda, $titre_unique_data_apidae);
	
	for($i = 0; $i<$sizeapidae; $i++) 	
	{
		if ($titre_unique_data_apidae[$i]!="") 		
		{
			$liste_apidae[$i]=$titre_unique_data_apidae[$i];
		}
	}

	$nb_maj=0;
	$nb_create=0;
	
	for($i = 0;$i<$sizeopenagenda;$i++) 
	{
		if ($unique_data_openagenda[$i]!="") 		
		{ 
			$liste_openagenda[$i]		=$unique_data_openagenda[$i];
			$liste_openagenda_uid[$i]	=$unique_data_openagenda_uid[$i];   /* IUD de l'annonce */
			$liste_openagenda_loc[$i]	=$unique_data_openagenda_loc[$i];	/* IUD de la localisation - IMPORTANT pour la mise à jour ! */
		}
			// echo "ID >".$unique_data_openagenda[$i]." | LOC >".$liste_openagenda_loc[$i]."<br>";
	}
	
?>

<head>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0" name="viewport">
	<meta content="telephone=no" name="format-detection">
	
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	
	<title>Extranet - Office de l'Office de Tourisme</title>
	<link rel="icon" href="img/favicon.ico" type="image/x-icon">  
	
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/form.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/icons.css" rel="stylesheet">

	<link href="css/summernote.min.css" rel="stylesheet">		
	<link href="css/lightbox.css" rel="stylesheet">

</head>
<style>  
	.headerAd 	{	margin-top: 30px;	}
	.footerAd 	{	margin-bottom: 30px;}
	.mainAd 	{	margin-top: 30px;	}
	.bottom-menu {	text-align: center;	margin-top: 10px;	font-size: 12px;}
	.bottom-menu li {	display: inline;	}
	.bottom-menu li a {		color: #7E7E7E;	}
	.paper-line {	background-image: url(../img/paper_line.png);	background-position: center bottom;	background-repeat: no-repeat;	padding-bottom: 11px;	clear: both;	width: 960px;	margin: 0 auto 70px auto;}
	/* Menu */
	.tab-menu {	float:left;	margin-left:-10px;	width: 960px;	background: #f6f7fb;	border-bottom: solid 3px #cdd0d9;}
	.tab-menu li {	float: left;	list-style-type: none;}
	.tab-menu .fav {		float: right;	list-style-type: none;	border-left: solid 1px #cdd0d9;}
	.tab-menu li.fav a {		border-right: 0;}
	.tab-menu li a {		color: #727272;	display: block;	font-size: 16px;	padding: 10px 20px;	text-decoration: none;	text-shadow: rgba(255, 255, 255, 0.08) 0 1px 0;	border-right: solid 1px #CCC;}
	.tab-menu li a:hover , .tab-menu li .active {	background: #D7D9E1;	color: #727272;}
	.cityname {	float: left;	background-image: url('../img/pin.png');	/* margin: 0; */	padding-left: 35px;	height: 38px;	background-repeat: no-repeat;	vertical-align: bottom;	padding-top: 5px;	text-shadow: 0 1px 0 #474747;}
	.info {	list-style: none;	float: left;	color: #FFF;}
	.info ul {	float: left;}
	.info ul li {	float: left;}
	.info .info-meta li.first {	width: 230px;	height: 321px;	padding: 30px 25px 30px 25px;	background-color: #EC5454;}
	.info .info-meta li.first .temperature {	overflow: hidden;	height: 85px;}
	.fl {	float: left;	height: 85px;}
	.sign-wrapper {	height: 100px;	text-align: center;	display: table-cell;	vertical-align: middle;}
	.info .info-meta li.first .daydatetime {	color: #FFF;	text-shadow: 1px 1px 0px #ba3636;}
	.info .info-meta li.first .temperature .sign {	float: left;}
	.info .info-tab .info-meta li {	float: left;}
	.fr {	float: right;	height: 85px;}
	.info .info-meta li.first .temperature .gradus {	height: 100px;	color: #FFF;	text-shadow: 1px 1px 0px #ba3636;	overflow: hidden;	display: table-cell;	vertical-align: middle;}
	.info .info-meta li.first .temperature .gradus .celsius {	float: right;	font-size: 30px;}
	.info .info-meta li.first .temperature .gradus .number {	float: right;	font-size: 55px;	font-weight: bold;}
	.info .info-meta li.first .discription {	color: #FFF;	text-shadow: 1px 1px 0px #ba3636;	margin: 15px 0;	font-size: 18px;	line-height: 18px;}
	.info .info-meta li.first .windchill {	color: #FFF;	text-shadow: 1px 1px 0px #ba3636;	font-size: 14px;}
	.info .info-meta li.first .other {	color: #FFF;	text-shadow: 1px 1px 0px #ba3636;	font-size: 18px;	margin-top: 15px;	overflow: hidden;	line-height: 24px;	font-weight: normal;}
	.info .info-meta li.first .other .humidity {	overflow: hidden;	display: block;	width: 100px;	float: left;	font-weight: bold;}
	.info .info-meta li.first .other .cloud {	overflow: hidden;	display: block;	width: 101px;	float: right;	font-weight: bold;}
	.info .info-meta li.first .other .humidity .icon {	margin-top: -3px;	display: block;	float: left;}
	.icon-js {	display: none;}
	.info .info-meta li.first .other .humidity .point, #weather .info .info .info-meta li.first .other .wind .point {	font-size: 20px;	line-height: 28px;	font-weight: bold;}
	.info .info-meta li.first .other .wind, .wind {	overflow: hidden;	display: block;	float: left;	width: 130px;	margin-top: 10px;}
	.slick-slider .info .info-meta li.first .other .wind, .wind {	width: 119px;}
	.info .info-meta li.first .other .rainpos, .rainpos {	overflow: hidden;	display: block;	float: right;	width: 100px;	margin-top: 10px;}
	.info .info-meta li.first .other .rainpos .point {	font-size: 18px;	line-height: 28px;	font-weight: bold;}
	.info .info-meta li.first .other .wind .point {	font-size: 18px;	line-height: 28px;	font-weight: bold;}
	.info .info-meta li.first .other .wind .icon {	margin-right: 5px;	display: block;	float: left;}
	.info .info-meta li.other {	width: 220px;	padding: 30px 0 0 0;	min-height: 351px;	position: relative;	background-color: #3c464e;	height: 311px}
	.info .info-meta li.other .meta-wrapper {	text-align: center;	border-right: 1px solid #636b71;	height: 333px;}
	.slider .info .info-meta li.other .meta-wrapper .title  {	height: 50px;	display: block;}
	.info .info-meta li.other .meta-wrapper .title, .slider .info .info-meta li.other .meta-wrapper .title-week {	height: 90px;	display: block;}
	.info .info-meta li.other .meta-wrapper .day {	color: #fff;	font-size: 18px;	margin-bottom: 5px;}
	.info .info-meta li.other .meta-wrapper .date {	color: #7b8f9e;	font-size: 12px;	margin-bottom: 5px;}
	.info .info-meta li.other .meta-wrapper .sign-wrapper {	width: 220px;	}
	.responsive {  width: 100%;  max-width: 200px;  max-height: 200px;    height: auto;	}
	
	#div2 {
		border: 1px solid rgb(0 0 0 / 20%);
		border-radius: 4px;
		/* width: 360px; */
		height: 300px;
		}
	
	#div2 {
	  overflow-y: scroll;
	  margin-bottom: 12px;
	}
	#div3 {
		border: 1px solid rgb(0 0 0 / 20%);
		border-radius: 4px;
		width: 870px;
		height: 300px;
		}
	
	#div3 {
	  overflow-y: scroll;
	  margin-bottom: 12px;
	}
	
</style>
<!-- <body style="background-image: url(img/skin13.jpg);background-position: center center; background-repeat: repeat; background-size: cover;"> -->
<body id="<?php echo $_SESSION['theme']; ?>" style="background-position: center;background-repeat: no-repeat;background-size: cover;">

	<header>

	</header>

 
<!-- ######################################################################################################################################################## -->  

	<section class="p-relative" id="main" role="main">
		<section class="" id="">

		<?php	
			echo '<ol class="breadcrumb hidden-xs" style="font-size: 11px;">
				<li class="active"><a href="#" onclick="refresh()">Actualiser</a></li>
				<li><a data-toggle="modal" href="#form-modal">Note</a></li>
				<li><a href="'.$url_Apidae.'"> Liste sur APIDAE </a></li>
				<li><a href="'.$url_OpenAgenda.'"> Liste sur OpenAgenda </a> </li>
				<li><a href="?test=oui">Mode Test</a></li>	
				<li>Martigues-Tourisme</li>		
				<li><a href="https://github.com/ZeCrusher/">SergeTSK</a></li>						
			</ol>';
		?>
			
            <div class="block-area" id="textarea">
			
			
		<?php
		
			$results_API = API_Resource($url_Apidae);
					
			write_to_console("URL de connexion sur APIDAE (cliquable) : ".$url_Apidae);
					
			$results = json_decode($results_API,false);
					
			/* ******************************************************************************************************************************************************************************* */
					
				$results_OpenAgenda = API_Resource($url_OpenAgenda);
				$results_OA = json_decode($results_OpenAgenda,false);
				$route = "https://openagenda.com/agendas/".$agendaUid."/events.v2.json?key=".$keys['public'];

				write_to_console("URL de connexion sur OpenAgenda (cliquable) : ".$route);
				
				$nbmanif=$results->numFound; 
					if ($nbmanif=="") { 
						echo '<div class="alert alert-danger alert-icon">';
						echo 'ERREUR APIDAE > ';
						echo $results->message; 
						echo ' - Message de retour > ';
						echo $results->errorType.'<i class="icon"></i></div>'; 
						
					}	
					if ($accessToken=="") { 
						echo '<div class="alert alert-danger alert-icon">Erreur sur votre TOKEN OPENAGENDA - Il faut vérifier vos clefs dans votre ligne de commande <i class="icon"></i></div>';
					}
					write_to_console("il y a ".$nbmanif." manif");
					
					if ($_GET['test']=="oui") { 
						echo '<div class="alert alert-warning alert-icon">Le mode <b>test</b> de l\'API apparaît lorsque vous testez la création d\'un événement dans OPENAGENDA - Network Test <i class="icon"></i></div>';
					}
							
					$obj = json_decode(file_get_contents($route), false);
					
					$openagenda_nb_event=$obj->total;	
					$retobjetsTouristiques = $results->objetsTouristiques;
					
					$openagenda_id_event=$obj->events[0]->uid;	
					write_to_console("Openagenda_id_event >".$openagenda_id_event);			
					
					$liste_affichage=0;
					
					foreach($retobjetsTouristiques as $fiche=>$lesdates)
					{
						foreach ($lesdates as $retourfiche) 
						{
							
							if ($retourfiche->id==$arr_id[$liste_affichage]) 
							{	

								$arr_id[$liste_affichage]=""; 		
								$liste_affichage++;	
							
								$json_event_date_ouverture = $retourfiche->ouverture;
								$event_heure_ouverture = array();
								
								$event_tarif=array();
								$event_illustrations=array();

								$nb_tarif=0;
								$nb_periode=0;
								$nb_illustrations=0;
								$modele_commercial	=$retourfiche->descriptionTarif->indicationTarif;		
								$tarifsEnClair		=$retourfiche->descriptionTarif->tarifsEnClair->libelleFr;	
								$event_adresse 		= $retourfiche->localisation;
								$event_adresse = ""; // init l'adresse
					
	/* GESTION DES ADRESSES */	

								if (isset($retourfiche->localisation->adresse->nomDuLieu)) 		{	$event_adresse.= $retourfiche->localisation->adresse->nomDuLieu.", ";	}
								if (isset($retourfiche->localisation->adresse->adresse1)) 		{	$event_adresse.= $retourfiche->localisation->adresse->adresse1.", ";	}
								if (isset($retourfiche->localisation->adresse->adresse2)) 		{	$event_adresse.= $retourfiche->localisation->adresse->adresse2.", ";	}
								if (isset($retourfiche->localisation->adresse->codePostal)) 	{	$event_adresse.= $retourfiche->localisation->adresse->codePostal." ";	}
								if (isset($retourfiche->localisation->adresse->commune->nom))	{	$event_adresse.= $retourfiche->localisation->adresse->commune->nom;		}
								
								if (isset($retourfiche->localisation->adresse->commune->pays->libelleFr))	{
									$event_adresse.=", ".$retourfiche->localisation->adresse->commune->pays->libelleFr." ";
								}
							 
								if ($retourfiche->localisation->geolocalisation->valide=="true") 			
								{
									$geolocalisation_long		=$retourfiche->localisation->geolocalisation->geoJson->coordinates['0'];
									$geolocalisation_lat		=$retourfiche->localisation->geolocalisation->geoJson->coordinates['1'];
									$complement_geolocalisation	=$retourfiche->localisation->geolocalisation->complement->libelleFr;
								}
								
								$nomDuLieu 	= $retourfiche->localisation->adresse->nomDuLieu;
								$codePostal = $retourfiche->localisation->adresse->codePostal;
								$ville		= $retourfiche->localisation->adresse->commune->nom;
								
								if ($_GET['test']=="oui") 		
								{ 
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
									'department'	=>	$department,				
									'timezone'		=>	$timezone,					
									'countryCode' 	=>	$countryCode,				
									'latitude'		=> 	$geolocalisation_lat,
									'longitude'		=> 	$geolocalisation_long,
									'test'			=> 	$etat_test
								);

	/*---------------------------------------------------------------
	* Création du lieu dans OPENAGENDA
	*---------------------------------------------------------------*/
					
								if ($_GET['id_create']==$retourfiche->id) {
									ecrire_data(" Dans le if create ".$result_loc);									
									$received_content_id_adresse = create_localisation_event($accessToken,$Openagenda_event_adresse,$agendaUid);
									$result_loc=json_decode($received_content_id_adresse,false);
								} 
				
								$result_loc=json_decode($received_content_id_adresse,false);
								// ecrire_data(" result_loc> ".$result_loc);
								
								// if ($result_loc->error!="") { /* En cas d'erreur, arrêt du script avec die !  */
									// die("Il existe une erreur dans la création de la Localisation.  ['".$result_loc->error."']");
								// }
								
								$result_uid_location=$result_loc->location->uid;
								
								
								if ($result_uid_location!="") write_to_console('Retour de Uid LORS DE LA CREATION de la location > '.$result_loc->location->uid);

	/* GESTION DES DATES */			
								$nb_date_ouverture=0;
								do 
								{ // 2022-10-26T12:00:00+0200
									/* Recherche des horaires - Ouvertures et fermetures ainsi que la date en cours  */
									
									$begin			=$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->dateDebut."T".$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->horaireOuverture;
									$end			=$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->dateFin  ."T".$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->horaireFermeture;
									$date_ouverture	=$json_event_date_ouverture->periodesOuvertures[$nb_date_ouverture]->dateDebut;
									

	/* GESTION DES ERREURS DE DATE */	
									sscanf($begin, 	"%4s-%2s-%2sT%2s:%2s:%2s"	,$annee_begin	, $mois_begin	, $jour_begin	, $heure_begin	, $minute_begin	, $seconde_begin);
									sscanf($end, 	"%4s-%2s-%2sT%2s:%2s:%2s"	, $annee_end	, $mois_end		, $jour_end		, $heure_end	, $minute_end	, $seconde_end);
									
									if ($jour_begin!=$jour_end)  {
										$erreur_jour_different_id[$nb_date_ouverture]="OUI";
										$erreur_jour_different_begin[$nb_date_ouverture]=$begin;
										$erreur_jour_different="OUI";
										$end=$annee_end.'-'.$mois_end.'-'.$jour_begin.'T'.$heure_end.':'.$minute_end.':'.$seconde_end;
									}
									
									if ($mois_begin!=$mois_end)  {
										$erreur_mois_different_id[$nb_date_ouverture]="OUI";
										$erreur_mois_different_end[$nb_date_ouverture]=$end; /* AVANT LA CORRECTION */
										$erreur_mois_different="OUI";
										$end=$annee_end.'-'.$mois_begin.'-'.$jour_end.'T'.$heure_end.':'.$minute_end.':'.$seconde_end;
									}
									
									$event_heure_ouverture[] = array('begin' => $begin."+0200", 'end' => $end."+0200");
								} 
								while ($json_event_date_ouverture->periodesOuvertures[++$nb_date_ouverture]->dateDebut!="");
								
								
								$testdebut=substr(substr($begin, 0, 10), -2);

	/* GESTION DU HANDICAP  */		

	/* GESTION DU PUBLIC  */		

					// echo "Type de Public :"				.$retourfiche->prestations->typesClientele[0]->libelleFr."<hr>";
					// echo "Type de animauxAcceptes :	"	.$retourfiche->prestations->animauxAcceptes."<hr>";
					// echo "Type de complementAccueil :"	.$retourfiche->prestations->complementAccueil->libelleFr."<hr>";
					// echo "Type de modesPaiement :"		.$retourfiche->descriptionTarif->modesPaiement[0]->libelleFr."<hr>";
					
					// echo "Type de modesPaiement :"		.$retourfiche->descriptionTarif->modesPaiement[0]->libelleFr."<hr>";				
					
	/* GESTION DES TARIFS  */			
								$nb_periode=0;
								do 	{	
									do 	{
										$tarifs_minimum		=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->minimum;					
										$tarifs_maximum		=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->maximum;	
										$tarif_cible		=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->type->libelleFr;	
										$tarif_description	=$retourfiche->descriptionTarif->periodes[$nb_periode]->tarifs[$nb_tarif]->type->description;	
										$tarif_description	= substr($tarif_description, 0, 250)."...";
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
									$photo_url			= $retourfiche		->illustrations[$nb_illustrations]->traductionFichiers[0]->urlDiaporama;
									$photo_copyright 	= $retourfiche		->illustrations[$nb_illustrations]->copyright->libelleFr;
									$photo_fileName		= $retourfiche		->illustrations[$nb_illustrations]->traductionFichiers[0]->fileName;
									$photo_libelleFr	= $retourfiche		->illustrations[$nb_illustrations]->traductionFichiers[0]->nom->libelleFr;
									
									// 	Il existe plusieurs méthodes pour extraire le nom d'un fichier d'un chemin complet.
									// $file = basename($photo_url);
									// echo "<hr>".$file."<br>";
									$event_illustrations[$nb_illustrations] = array('photo_url'	=> $photo_url, 'photo_copyright' => $photo_copyright,'photo_fileName'=> $photo_fileName,'photo_libelleFr'=> $photo_libelleFr);
								} 
								while ($retourfiche->illustrations[++$nb_illustrations]->traductionFichiers[0]->url!="");
					
	/* GESTION DES DONNEES DE BASE */	

								if (isset($retourfiche->nom->libelleFr) && !empty($retourfiche->nom->libelleFr))	
								{
									$titre = $retourfiche->nom->libelleFr;
								} 
								else	
								{
									continue;
								}

								$descriptifCourt	= $retourfiche->presentation->descriptifCourt->libelleFr;
								$descriptifDetaille	= $retourfiche->presentation->descriptifDetaille->libelleFr;
								
								$reservation_registration=$retourfiche->reservation->organismes[0]->moyensCommunication[0]->coordonnees->fr;
								
								$letterslug = array("'", " ");	 /* 1 - Construction de slug ... la menace de Namek ? */ 
								$letterslug = array(",", " ");
								$search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
								//Préférez str_replace à strtr car strtr travaille directement sur les octets, ce qui pose problème en UTF-8
								$replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
								$titre_replace = str_replace($search, $replace, $titre);
								$slug = str_replace($letterslug, "-", strtolower($titre_replace));
								
																
								/* Après analyse du retour du fichier Json OPENAGENDA - il est possible qu'il en manque ! 
									type public >
										20 = Familles , 
										25 = Tout public
										17 = Adultes,
										18 = Enfants, 
										24 = Adolescents
								*/
								
								$dejafait=0;
								$i=0;
								do 
								{
									// echo "Titre : ".$titre." id Loc :".$unique_data_openagenda_loc[$i]."<br>";
									if ($titre==$unique_data_openagenda[$i]) { 
										$dejafait=1;
										$id_update=$unique_data_openagenda_uid[$i];
										$result_uid_location=$unique_data_openagenda_loc[$i];
									}
									$i++;
								}
								while ($i<$sizeapidae);								
								
								for  ($j=0;$j<$nb_tarif;$j++) {
									$result_event_tarif.="Tarif mini : ".$event_tarif[$j]['tarifs_minimum']." €, maxi ".$event_tarif[$j]['tarifs_maximum']." € - Cible :".$event_tarif[$j]['tarif_cible']." ";
								}
								
								if ($tarifsEnClair=="Gratuit.") {
									$result_event_tarif="Gratuit";
								}
								
								$b=0;$keywords="";
								do 	{ /* la virgule permet de couper les mots pas OpenAgenda */
									$keywords.=$retourfiche->informationsFeteEtManifestation->themes[$b]->libelleFr.", ";
								}	
								while ($retourfiche->informationsFeteEtManifestation->themes[++$b]->libelleFr!="");
								
								$keywords=substr($keywords, 0, -2); /* Pour supprimer la virgule en fin de chaine et l'espace ", " */

/* detection et correction des erreurs */

								$erreur_date=0;
								for ($i=0;$i<$nb_date_ouverture;$i++)
									if (substr($event_heure_ouverture[$i]['begin'], -6)=="T+0200") 	{
										$erreur_date++;
										$begin_erreur[$i]=$event_heure_ouverture[$i]['begin'];
										$event_heure_ouverture[$i]['begin']=substr($event_heure_ouverture[$i]['begin'],0, 11)."12:00:00+0200";
									}
								for ($i=0;$i<$nb_date_ouverture;$i++)
									if (substr($event_heure_ouverture[$i]['end'], -6)=="T+0200") { 
										$erreur_date++;
										$end_erreur[$i]=$event_heure_ouverture[$i]['end'];
										
										$event_heure_ouverture[$i]['end']=substr($event_heure_ouverture[$i]['end'],0, 11)."20:00:00+0200";
									}
								
								if ($reservation_registration=="") 	$erreur_reservation_registration="OUI"; 

								$Openagenda_event_data = array(
									  'title' => array(
										'fr' => $titre_test.$titre
									  ),
									  'state' => 0, 		/* 0: événement non publié, à contrôler - 1: événement non publié, controlé - 2: événement publié (valeur par défaut) */
									  'image' 			=> 	array('url' => $event_illustrations[0]['photo_url']),
									  'imageCredits'	=>	$event_illustrations[0]['photo_copyright'],
									  'locationUid' 	=> 	$result_uid_location, /* id de l'adresse, soit elle n'existe pas et donc il s'agit d'une création de lieu, soit il faut la récupérer */
									  'longDescription' => 	array('fr'=> $descriptifDetaille),
									  'description' 	=> 	array('fr'=> substr($descriptifCourt, 0, 200).'...'), /* Champ obligatoire ne pouvant excéder 200 caractères par langue */
									  'public' 			=> 	25,	
									  'conditions'		=> 	substr($tarifsEnClair, 0, 250)."...", //$result_event_tarif,
									  'registration' 	=> 	array($reservation_registration),
									  'nature' 			=> 	57, /* NON DOCUMENTÉ */
									  'thematique' 		=>	2,  /* NON DOCUMENTÉ */
									  'fadas' 			=>	46, /* NON DOCUMENTÉ */
									  'featured'		=> 	false, /* true quand l'événement doit apparaître en tête de liste ( en une ) */
									  'keywords' 		=> 	array('fr' => $keywords),
									  'timings' 		=> 	$event_heure_ouverture,
									  'slug' 			=>	$slug					  
									);
									
								$ds=json_encode($Openagenda_event_data);
								// ecrire_data("***************************************************************************************************");
								// ecrire_data("ds >".$ds);
								// ecrire_data("***************************************************************************************************");
					
	/* CREATION DE L'EVENEMENT ------------------------------------------------------------------------------------------------------- */

								if ($_GET['id_create']==$retourfiche->id) {
									$received_content_id_event = create_event($accessToken,$Openagenda_event_data,$agendaUid);
									
									$result_event=json_decode($received_content_id_event,false);
								}
								
								if ($_GET['id_update']==$retourfiche->id) {
									$received_content_id_event = update_event($accessToken,$Openagenda_event_data,$agendaUid,$_GET['id_update']);
									$result_event=json_decode($received_content_id_event,false);
								}

								if ($_GET['taille_fiches']=="") 
									$taille_fiches=3;
								else
									$taille_fiches=$_GET['taille_fiches'];
								
								echo '<div class="col-lg-'.$taille_fiches.'">'; /* width:396px;min-width: 200px;max-width: 400px; */
								echo '<div class="tile" style="height: 570px;min-height: 600px; max-height: 650px;">';
								
								if (($_GET['id_create']==$retourfiche->id) || ($_GET['id_update']==$retourfiche->id))	{
									echo '<div style="background-color: #a944427a;">';
								}
													
								// echo '<div class="tile-title"><center><small class="text-muted"> Etat sur Apidae [<b>'. $retourfiche->state.'</b>] - id de l\'annonce <b>'.$retourfiche->id.'</b></small></center></div>';
								echo '<div class="tile-title"><center><b><h5>'.$titre_test.$titre;
								if ($erreur_date>0) {
									echo ' <a data-toggle="modal" href="#modalNarrower'.$retourfiche->id.'"><span class="icon"></span></a>';
								}
								echo '</h5></b></center></div>';
									
									echo '<div class="modal fade" id="modalNarrower'.$retourfiche->id.'" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
											<div class="modal-dialog modal-sm">
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
														<h4 class="modal-title">Correction des dates / Heures </h4>
													</div>
													<div class="modal-body">
														<p>';
														echo '<b>Avant correction :</b><br>';
														for ($i=0;$i<$nb_date_ouverture;$i++) {
															echo 'Début >'.$begin_erreur[$i]."<br>";
															echo 'Fin   >'.$end_erreur[$i]."<br>";
														}
													echo '<br><b>Après correction :</b><br>';
														for ($i=0;$i<$nb_date_ouverture;$i++) {
															echo 'Début >'.$event_heure_ouverture[$i]['begin']."<br>";
															echo 'Fin   >'.$event_heure_ouverture[$i]['end']."<br>";
														}
													echo '</p>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-sm" data-dismiss="modal">Fermer</button>
													</div>
												</div>
											</div>
										</div>';	

								// if ($erreur_reservation_registration=="OUI") {
									// echo '<div class="alert alert-danger fade in" style="padding: 5px;margin-inline: 20px;">
									// <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									// Attention : Il n\'y a pas de lien de réservation</div>';
								// }

								// if ($erreur_date>0) {
									// echo '<div class="alert alert-danger fade in" style="padding: 5px;margin-inline: 20px;">
									// <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									// Attention : il y a '.$erreur_date.' erreur(s) sur les heures</div>';
								// }
								
								
								// echo '<center><b><h5>'.$titre_test.$titre.'</h5></b></center><br>';
								// if ($_GET["test"]=="oui") {
									// echo "<center>Mode Test<br>";
									// echo "Nombre de Tarif :".$nb_tarif."<br>";
									// echo "Tarif en clair :".$tarifsEnClair."<br>";
									
									
									// echo "Tarif à ajouter :".$result_event_tarif."</center>";		
									// echo "<br>";
								// }

								// echo '<center><img src="'.$event_illustrations[0]['photo_url'].'" width="240px" height="200px" class="img-rounded m-r-10 m-b-10"><br><small class="text-muted">Crédit '.$event_illustrations[0]['photo_copyright'].'</small></center>';

								echo '<div class="tab-container">
									<ul class="nav tab nav-tabs">
										<li class="active"><a href="#home'.$liste_affichage.'">Court</a></li>
										<li><a href="#profile'.$liste_affichage.'">Detaillé</a></li>'; //$erreur_date_identique
										
									if (($erreur_jour_different=="OUI") || ($erreur_mois_different=="OUI")) {
										echo '<li><a href="#horaire'.$liste_affichage.'"><b>Horaire*</b></a></li>'; 
									}
									else {
										echo '<li><a href="#horaire'.$liste_affichage.'">Horaire</a></li>'; 
									}
									
									if (strlen($tarifsEnClair)>254) {
										echo '<li><a href="#settings'.$liste_affichage.'"><b>Note*</b></a></li>'; 
									}
									else {
										echo '<li><a href="#settings'.$liste_affichage.'">Note</a></li>'; 
									}

									echo '<li><a href="#keywords'.$liste_affichage.'">KEY</a></li>
										<li><a href="#requete'.$liste_affichage.'">Requêtes</a></li>										
									</ul>';
								echo '<div class="tab-content">';
								echo '<div class="tab-pane active" id="home'.$liste_affichage.'">';
								echo '	<center><a href="'.$event_illustrations[0]['photo_url'].'" data-rel="gallery" class="pirobox_gall img-popup item" title="'.$titre_test.$titre.'" rev="1">
													<img src="'.$event_illustrations[0]['photo_url'].'" style="object-fit: cover;border-radius: 4px;position: relative;max-width: 100%;width: 550px;height: 350px;overflow: hidden;">';
                    
													if (($erreur_date>0)||($erreur_reservation_registration=="OUI")) {
														echo '<div class="carousel-caption hidden-xs" style="background: rgb(223 69 69 / 59%);border-radius:4px">';
														// echo '<h3>Attention</h3><p> Il y a '.$erreur_date.' erreur(s) sur les heures</p></div>';
														echo '<h3>Attention</h3><p> Cette fiche comporte des erreurs qui ont été corrigées.(Nb '.$erreur_date.')</p></div>';
													}
								echo '			</a></center>';								
								
								echo '<p style="margin: 10px 10px 10px;"><i>'.substr($retourfiche->presentation->descriptifCourt->libelleFr, 0, 300).'...</i></p>';
								echo '</div>';

								echo '<div class="tab-pane" id="profile'.$liste_affichage.'">';
								echo '<p>Nombre de caractères : '.strlen($retourfiche->presentation->descriptifDetaille->libelleFr).'</p>';
								echo '<p style="margin: 10px 10px 10px;"><div id="div2"><i>'.substr($retourfiche->presentation->descriptifDetaille->libelleFr, 0, 2000).'</i></div></p><br>';
								echo '</div>';
								
								echo '<div class="tab-pane" id="horaire'.$liste_affichage.'">';
								
									$i=0;
									
									echo "<b><center><h5>Les horaires de l'annonce :</h5></center></b>";
									
									if ($erreur_jour_different=="OUI") {
										echo '<center><p style="color: #f30117;background: white;"><b>';
										echo 'Attention, un évènement ne peut pas depasser les 24 H !';
										echo '</b></p></center>';
										
									}
									if ($erreur_mois_different=="OUI") {
										echo '<center><p style="color: #f30117;background: white;"><b>';
										echo 'Attention, les mois doivent être identiques !';
										echo '</b></p></center>';										
									}					
									if ($erreur_date>0) {
										echo '<center><p style="color: #f30117;background: white;"><b>Les heures ont été corrigées</b></p></center>';
									}									
									echo '<div id="div2"><center><small class="text-muted">Formaté pour la requête</small></center>';
									do 
									{

											sscanf($erreur_jour_different_begin[$i]	, "%4s-%2s-%2sT%2s:%2s:%2s", $annee_begin	, $mois_begin, $jour_begin, $heure_begin, $minute_begin, $seconde_begin);
											sscanf($erreur_jour_different_end[$i]	, "%4s-%2s-%2sT%2s:%2s:%2s", $annee_end		, $mois_end, $jour_end, $heure_end, $minute_end, $seconde_end);



											
											if ($erreur_jour_different_id[$i]=="OUI")	{ 
												$jour_begin='<b style="color: red;">'.$jour_begin.'</b>';
												$jour_end='<b style="color: red;">'.$jour_end.'</b>';
												echo '<p style="color: #f30117;background: white;"><center> Erreur car les jours sont différents '.$jour_begin.' ≠ '.$jour_end.'</center></p>';
											}
											
								
											if ($erreur_mois_different_id[$i]=="OUI")	{ 
												$mois_begin='<b style="color: red;">'.$mois_begin.'</b>';
												$mois_end='<b style="color: red;">'.$mois_end.'</b>';
												echo '<p style="color: #f30117;background: white;"><center> Erreur car les mois sont différents '.$mois_begin.' ≠ '.$mois_end.'</center></p>';
											}
											
											if (($erreur_jour_different_id[$i]=="OUI") || ($erreur_mois_different_id[$i]=="OUI") ) {
												$erreur_jour_different_id[$i]="";
												$erreur_mois_different_id[$i]="";
												$erreur_jour_different_begin[$i]="";
												$erreur_mois_different_end[$i]="";
												
												echo '<center>'.$annee_begin.'-'.$mois_begin.'-'.$jour_begin.'T'.$heure_begin.':'.$minute_begin.':'.$seconde_begin.'+200 ≠ '.$annee_end.'-'.$mois_end.'-'.$jour_end.'T'.$heure_end.'-'.$minute_end.'-'.$seconde_end.'+200</center><br>';
												echo '<center> Avec Correction : </center>';
											}
											echo '<center>'.$event_heure_ouverture[$i]['begin'].' - '.$event_heure_ouverture[$i]['end'].'</center><br>';
									
									} while ($event_heure_ouverture[++$i]!="");
									
									$erreur_jour_different="";
									
								
								echo '</div></div>';
								
/* ***************************************************************************************************************************************** */		
						
								echo '<div class="tab-pane" id="settings'.$liste_affichage.'"><div id="div2">';
								echo 'Il y a '. $nb_tarif.' tarif(s) avec comme tarifs en Clair <bt>';

								if (strlen($tarifsEnClair)>254) 	{ 
									echo '<br><br><p style="color: #f30117;background: white;"><b>Attention, le descriptif tarif est trop long. OpenAgenda limite le texte à 255 caractères. Le texte va être coupé pour l\'importation.</b></p>'; 
								}
									
								echo '<b>'.$tarifsEnClair.'</b><br/>';	
								
								for  ($j=0;$j<$nb_tarif;$j++) 
									echo "Mini: ".$event_tarif[$j]['tarifs_minimum']."€, max  ".$event_tarif[$j]['tarifs_maximum']." €";
								
								echo "<br>Cible :".$event_tarif[$j]['tarif_cible']."</br></br>";
								echo '<hr  class="whiter">';
								echo "<br><b>Adresse de l'annonce :</b><br>". $event_adresse."<br /><br />";
								echo '<hr class="whiter"><br /><br />';
								echo "<b>Note : </b><br>".substr($retourfiche->presentation->typologiesPromoSitra[0]->libelleFr, 0, 180)."";
								echo '</div></div>';
								
/* ***************************************************************************************************************************************** */			
		
								echo '<div class="tab-pane" id="keywords'.$liste_affichage.'"><div id="div2">';
								echo "<b>keywords : </b> ".$keywords.".";
								echo '</div></div>';
/* ***************************************************************************************************************************************** */								
								
								echo '<div class="tab-pane" id="requete'.$liste_affichage.'">';
								// if ($dejafait==1) echo 'En cas de mise à jour UNIQUEMENT id du lieu >'.$result_uid_location.'<br>';
								
								echo "Données structurées pour l'<b>Adresse</b> (JSON) ";
								echo "<pre>";	
									$ds=json_encode($Openagenda_event_adresse);
									print_r($ds);
								echo "</pre>";
								echo "Données structurées pour l'<b>événement</b> (JSON) ";								
								echo "<pre>";	
									$ds=json_encode($Openagenda_event_data);
									print_r($ds);
								echo "</pre>";
								echo '</div>';
/* ***************************************************************************************************************************************** */								
								
							echo '</div>';
						echo '</div>';
	
								if (($_GET['id_create']==$retourfiche->id) || ($_GET['id_update']==$retourfiche->id)) { 
										echo '</div>';
									}
								echo '<center> ';
									echo '<div style="position:absolute;bottom:0;width:100%;padding-top: 0px;height:50px;">';
							?>
								
								<a href="#" onClick="window.open('https://maps.google.com/?q=<?php echo $geolocalisation_lat.','.$geolocalisation_long; ?>','MAPS','width=600, height=480')" target="_blank" class="btn btn-sm btn-alt m-r-5"> MAPS </a>
								
							<?php
								
									if ($dejafait==1) { 	
										if ($_GET['test']=="oui") $valtest="&test=oui";
										echo '<a href="index.php?id_update='.$id_update.$valtest.'" target="_self" class="btn btn-sm btn-alt m-r-5" >M.A.J</a>'; 
									}
									
									if ($dejafait==0) {
										if ($_GET['test']=="oui") {
											$valtest="&test=oui"; 
										}
										if ($erreur_date>0) {
											echo '<a href="index.php?id_create='.$retourfiche->id.''.$valtest.'" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Les dates/heures ont été corrigés" title="" data-original-title="Note de la publication" target="_self" class="btn btn-sm pover btn-alt m-r-5"  >AJOUTER</a>';
										}
										else {
											echo '<a href="index.php?id_create='.$retourfiche->id.''.$valtest.'" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Aucune erreur détectée." title="" data-original-title="Note de la publication" target="_self" class="btn btn-sm pover btn-alt m-r-5"  >AJOUTER</a>';
										}
									}
								if ($reservation_registration!="") {
									echo '<a href="#" class="btn btn-sm btn-alt m-r-5" onclick="window.open(\''.$reservation_registration.'\',\'lien de réservation\',\'width=800, height=600\')"><img src="img/link-ok.png" alt="" style="height:18px;width:18px;"></a>';
								} else {
										echo '<a href="#" class="btn btn-sm btn-alt m-r-5"><img src="img/link-brocken.png" alt="" style="height:18px;width:18px;"></a>';
								}
									
								echo '</div>';
								echo '</center> '; 

							echo '</div>';
							echo '</div>';
								
								$result_event_tarif=""; // remise à zéro des tarifs pour la future fiche à charger.
								$tarifsEnClair="";
							}		
							else 
							{
								 // echo "(".$liste_affichage." ) - Retour ".$retourfiche->id." | Arr ".$arr_id[$liste_affichage]."<br>";
								$liste_affichage++;
							}
				
						}
								
					}
				
?>
			
			</div>
	
        </section>
	</section>
	
	
	<div class="modal fade" id="form-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" type="button" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title">Note sur l'API</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" role="form">
						<div class="form-group">
							<label for="inputName4" class="col-md-2 control-label">Note : </label>
							<div class="col-md-9">
								<textarea class="input-sm validate[required] form-control" style="height: 350px; outline: none;" placeholder="..." id="form-validation-field-0"></textarea>
							</div>
						</div>
                    </form>
                </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-alt">Sauvegarder</button>
					<button type="button" class="btn btn-sm btn-alt" data-dismiss="modal">Fermer</button>
				</div>
			</div>
		</div>
	</div>	
	
	
	<div class="modal fade" id="modalDefault" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Afficher le fichier pages\config.php</h4>
				</div>
				
				
				<div class="tab-container tile">
                        <ul class="nav tab nav-tabs">
                            <li class="active"><a href="#home">Config</a></li>
                            <li class=""><a href="#profile">Clefs API</a></li>
                            <li><a href="#messages23">Aides</a></li>
                        </ul>
                          
                        <div class="tab-content">
                            <div class="tab-pane active" id="home">

								<br>
								<div class="alert alert-info alert-dismissable fade in" style="margin-left: 40px;margin-right: 40px;">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									Par mesure de sécurité, la sauvegarde est désactivée. vous ne pouvez pas modifier ce fichier.
								</div>
								<div class="modal-body">
													  <!--<div id="div3">  -->
									<textarea class="input-sm validate[required] form-control" style="height: 350px; outline: none;" placeholder="..." id="form-validation-field-0">
										<?php 

										$line="";
										$txt_file = fopen('pages/config.php','r');
										$num_ligne = 1;
										while ($line = fgets($txt_file))	 {
											echo $line;
											$num_ligne++;
										}
										fclose($txt_file);									
										?>
									</textarea>
														 <!--</div>	  Pour le DIV3-->
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-sm" disabled>Sauvegarder</button>
									<button type="button" class="btn btn-sm" data-dismiss="modal">Fermer</button>
								</div>
							
							</div>
                            <div class="tab-pane" id="profile">
								<form method="POST" action="api.php">  
								<p>LISTE DES CLEFS (Version bêta) </p>
									<?php 
											
										
										$txt_file = fopen('pages/config.php','r');
										$num_ligne = 1;
										$nbkeypublic=0;
										$nbkeysecret=0;
										$nbapiKey=0;
										$nbselectionIds=0;
										$nbagendaUid=0;
										$nbterritoireIds=0;
										$nbvar=0;
										$nbprojetId=0;
																			
										while ($line = fgets($txt_file))	 {
																				
												$pos[0] = strpos($line, '"public"=>"');
												if ($pos[0] !== false) {	
													preg_match_all('/=>([^=> ]+)/', $line, $out, PREG_PATTERN_ORDER);
													$valkey = substr($out[0][0],3);
													$keypublic=substr($valkey,0,-3);
													// echo 'Public ".$keypublic." <br> ";
													echo '<div class="col-md-4 m-b-10">';
													echo '<p>Clef public OpenAgenda ('.$nbkeypublic++.')</p>';
													echo '<input type="text" value="'.$keypublic.'" class="form-control input-sm validate[required]" name="keypublic" id= "keypublic" placeholder="Votre clef public..." >';
													echo '</div>';							
												}
												$pos[1] = strpos($line, '"secret"=>"');
												if ($pos[1] !== false) {	
													preg_match_all('/=>([^=> ]+)/', $line, $out, PREG_PATTERN_ORDER);
													$valkey = substr($out[0][0],3);
													$keysecret=substr($valkey,0,-3);
													// echo "Secret : =".$keysecret." <br> ";
													echo '<div class="col-md-4 m-b-10">';
													echo '<p>Clef Secrète OpenAgenda ('.$nbkeysecret++.')</p>';
													echo '<input type="text" value="'.$keysecret.'" class="form-control input-sm validate[required]" name="keysecret" id= "keysecret" placeholder="Votre clef public..." >';
													echo '</div>';						
												}
												$pos[2] = strpos($line, '$agendaUid=');
												if ($pos[2] !== false) {	
													preg_match_all('/=([^; ]+)/', $line, $out, PREG_PATTERN_ORDER);
													$valkey=$out[0][0];
													$agendaUid = substr($valkey,1);
													// echo "AgendaUid >".$agendaUid."<br>";*
													echo '<p><div class="col-md-3 m-b-10">';
													echo '<p>AgendaUid OpenAgenda ('.$nbagendaUid++.')</p>';
													echo '<input type="text" value="'.$agendaUid.'" class="form-control input-sm validate[required]" name="agendaUid" id= "agendaUid" placeholder="Votre clef public..." >';
													echo '</div></p>';									
												}
												$pos[2] = strpos($line, '$territoireIds=array("');
												if ($pos[2] !== false) {	
													preg_match_all('/=([^; ]+)/', $line, $out, PREG_PATTERN_ORDER);
													$valkey=$out[0][0];
													$valkey = substr($out[0][0],8);
													$territoireIds = substr($valkey,0,-2);
													echo '<div class="clearfix"></div>';
													echo '<div class="col-md-2 m-b-10">';
													echo '<p>TerritoireIds ('.$nbterritoireIds++.')</p>';
													echo '<input type="text" value="'.$territoireIds.'" class="form-control input-sm validate[required]" name="territoireIds" id= "territoireIds" placeholder="Votre clef public..." >';
													echo '</div>';															
												}
												$pos[3] = strpos($line, '$selectionIds=array("');
												if ($pos[3] !== false) {	
													preg_match_all('/=([^; ]+)/', $line, $out, PREG_PATTERN_ORDER);
													$valkey=$out[0][0];
													$valkey = substr($out[0][0],8);
													$selectionIds = substr($valkey,0,-2);
													// $nbselectionIds++;
													echo '<div class="col-md-3 m-b-10">';
													echo '<p>SelectionIds APIDAE ('.$nbselectionIds++.')</p>';
													echo '<input type="text" value="'.$selectionIds.'" class="form-control input-sm validate[required]" name="selectionIds" id= "selectionIds" placeholder="Votre clef public..." >';
													echo '</div>';															
												}			
												$pos[4] = strpos($line, '$apiKey="');
												if ($pos[4] !== false) {	
													preg_match_all('/=([^; ]+)/', $line, $out, PREG_PATTERN_ORDER);
													$valkey=$out[0][0];
													$valkey = substr($valkey,2);				
													$apiKey = substr($valkey,0,-1);
												
													echo '<div class="col-md-3 m-b-10">';
													echo '<p>ApiKey APIDAE ('.$nbapiKey++.')</p>';
													echo '<input type="text" value="'.$apiKey.'" class="form-control input-sm validate[required]" name="apiKey" id= "apiKey" placeholder="Votre clef public..." >';
													echo '</div>';															
												}	
												$pos[5] = strpos($line, '$projetId="');
												if ($pos[5] !== false) {	
													preg_match_all('/=([^; ]+)/', $line, $out, PREG_PATTERN_ORDER);
													$valkey=$out[0][0];
													$valkey = substr($valkey,2);				
													$projetId = substr($valkey,0,-1);
													
													echo '<div class="col-md-2 m-b-10">';
													echo '<p>ProjetId APIDAE ('.$nbprojetId++.')</p>';
													echo '<input type="text" value="'.$projetId.'" class="form-control input-sm validate[required]" name="projetId" id= "projetId" placeholder="Votre clef public..." >';
													echo '</div>';															
												}			

											$num_ligne++;$valkey="";
											// echo $num_ligne;
										}
										fclose($txt_file);									
										?>
								<div class="clearfix"></div>		
								<br>
								<div class="modal-footer">
		
									<input class="btn btn-sm" type="submit" name="submit" id="sauvegarder" value="sauvegarder">
									<input class="btn btn-sm" type="submit" name="submit" id="Recharger" value="Recharger">
								</div>
						
							</form>

								
							</div>
                            <div class="tab-pane" id="messages23">
                                <p>Cette fonction est en version bêta. Elle peut subir des bug/pannes, entraînant ainsi des pertes de données dans le fichiers config.php.</p>
                                <p>Pensez à faire une sauvegarde de vos clefs et autres valeurs du fichiers config.php</p>
								
								<div class="modal-footer">
									<button type="button" class="btn btn-sm" data-dismiss="modal">Fermer</button>
								</div>                            
							</div>
                        </div>
                    </div>
				
			
			
			<!-- ****************************************************************************************************************************** -->
			
			</div>
		</div>
	</div>	
	
	
	</section>
	

	<script src="js/jquery.min.js"></script> 
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.easing.1.3.js"></script>
	
	<script src="js/bootstrap.min.js"></script>
	 <script src="js/easypiechart.js"></script> 

     <script src="js/select.min.js"></script> 
	 <script src="js/slider.min.js"></script> 

	 <script src="js/summernote.js"></script> 
     <script> 
         $(document).ready(function() { 
			$('#summernote').summernote(); 
         }); 
     </script> 


	 <script src="js/validate.min.js"></script>
	 <script src="js/validationEngine.min.js"></script> 
	 <script src="js/jquery.dataTables.js"></script> 

	 <script src="js/icheck.js"></script> <!-- Custom Checkbox + Radio --> 
     <script src="js/autosize.min.js"></script> <!-- Textarea autosize -->	 
	 <script src="js/pirobox.min.js"></script> 
	 <script src="js/scroll.min.js"></script> <!-- Custom Scrollbar --> 


	 <script src="js/feeds.min.js" charset="utf-8"></script> 
	
	 <script src="js/materialize.min.js"></script> 

	 <script src="js/datetimepicker.min.js"></script> 
	 <script src="js/functions.js"></script> 
	 <script src="js/script.js"></script> 