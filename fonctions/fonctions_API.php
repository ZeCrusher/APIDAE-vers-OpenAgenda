<?php


/****************************************************************************
*****                       Compositon du code                          *****
*****************************************************************************
* Copyright (c) 2022 - Serge Tsakiropoulos(ZeCrusher) - Office de Tourisme de Martigues
* Dossier apidae-openajenda

			index.php	<- 
			└── fonction /  fonction_API.php	-> Ce fichier ! 

Note dans la lecture des commentaires : 
// INFO ...> 		=> Informations supplémentaire
// WARNING ...> 	=> Cette ligne/bloc nécéssite un peu de maintenance et/ou d'optimisation
// ATTENTON ...> 	=> Le code est potentiellement dangereux et doit être corrigé au plus vite ! 
// TODO ...> 		=> Modifications à apporter ici ou idée suggérée à faire pour une futur version du code
 
*/


/****************************************************************************************************************************************************** 
*  API_Resource est la fonction qui va se charger de la requête à envoyer à APIDAE
* version 1.1
* in  <-- url_source
* out --> result (Le résultat est un flux/fichier au format JSON. Vous trouverez un exemple dans le dossier)
* MAJ : 
* Version 1 - 2022-09-01 - ZeCrusher
******************************************************************************************************************************************************/

function API_Resource($url_source)
{
	$session = curl_init(); /* initialise une nouvelle session et retourne un identifiant de session cURL à utiliser avec les fonctions curl_setopt(), curl_exec() et curl_close(). */
	curl_setopt($session, CURLOPT_POST, 1);
	curl_setopt($session, CURLOPT_URL, $url_source);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	$result = curl_exec($session);
	if (!$result)	{
		die("Il existe une erreur dans votre URL !! ");
	}
	curl_close($session); /* Fermeture de la session */
	
	return $result; 

}

/****************************************************************************************************************************************************** 
*  Création de l'adresse avant la création d'un événement sur l'OpenAgenda. 
* version 1 
* in  <-- 	accessToken (token unique demandé à OpenAgenda)
*			Openagenda_data ( variable sous forme de Json qui contient l'adresse )
*			$agendaUid (l'UID (son numéro d'identification) de l'agenda est visible dans la barre latérale en bas à droite de l'agenda.) 
* out --> result
* MAJ : 
* Version 1 - 2022-09-01 - ZeCrusher
******************************************************************************************************************************************************/

function create_localisation_event($accessToken,$Openagenda_data,$agendaUid)
{ 
	$URL = 'https://api.openagenda.com/v2/agendas/'.$agendaUid.'/locations';
	$session = curl_init();
	curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($session, CURLOPT_URL, $URL );
	curl_setopt($session, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($session, CURLOPT_POST, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, array(
		'access_token' => $accessToken,
		'nonce' => rand(),
		'data' => json_encode($Openagenda_data)
	));
	$received_content = curl_exec($session);
	return $received_content;
}
/****************************************************************************************************************************************************** 
*  Création d'un événement 
* version 1 
* in  <-- 	accessToken (token unique demandé à OpenAgenda)
*			Openagenda_data ( variable sous forme de Json/requête qui contient l'événement )
*			$agendaUid (l'UID (son numéro d'identification) de l'agenda est visible dans la barre latérale en bas à droite de l'agenda.) 
* out --> received_content
* MAJ : 
* Version 1 - 2022-09-01 - ZeCrusher
******************************************************************************************************************************************************/

function create_event( $accessToken, $Openagenda_data, $agendaUid) 
{
  
  	$URL = 'https://api.openagenda.com/v2/agendas/'.$agendaUid.'/events';

	// echo "URL =>".$URL."<br>";
	
	$retour_curl = curl_init();

	curl_setopt($retour_curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($retour_curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt( $retour_curl, CURLOPT_URL, $URL );
	curl_setopt($retour_curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($retour_curl, CURLOPT_POST, true);

	curl_setopt($retour_curl, CURLOPT_POSTFIELDS, array(
		'access_token' => $accessToken,
		'nonce' => rand(),
		'data' => json_encode($Openagenda_data)
	));

	$received_content = curl_exec($retour_curl);
	
	if ($received_content->error!="") { /* En cas d'erreur, arrêt du script avec die !  */
		die("Il existe une erreur  ['".$result_loc->error."']");
	}
	write_to_console("- Début de la fonction create_event de EVENT --------------------------------------------------------------------------------------------------------------");
	write_to_console("Create_event >".$received_content);
	write_to_console("- return de EVENT--------------------------------------------------------------------------------------------------------------");
	
	return $received_content;
	
}
/****************************************************************************************************************************************************** 
*  Mise à jour d'un événement 
* version 1 
* in  <-- 	accessToken (token unique demandé à OpenAgenda)
*			Openagenda_data ( variable sous forme de Json/requête qui contient l'événement )
*			$agendaUid (l'UID (son numéro d'identification) de l'agenda est visible dans la barre latérale en bas à droite de l'agenda.) 
* out --> received_content
* MAJ : 
* Version 1 - 2022-09-01 - ZeCrusher
******************************************************************************************************************************************************/

function update_event($accessToken,$Openagenda_data,$agendaUid,$eventUid) 
{
  
  	$URL = 'https://api.openagenda.com/v2/agendas/'.$agendaUid.'/events/'.$eventUid;
	
	write_to_console("-------Fonction_API-----------------");
	write_to_console("URL_update_event >".$URL);
	write_to_console("agendaUid à MAJ >".$agendaUid);
	write_to_console("eventUid à MAJ >".$eventUid);
	write_to_console("------------------------------------");
	
	$retour_curl = curl_init();

	// echo "Retour_curl >".$retour_curl."<br>";

	curl_setopt($retour_curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($retour_curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($retour_curl, CURLOPT_URL, $URL );
	curl_setopt($retour_curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($retour_curl, CURLOPT_POST, true);

	curl_setopt($retour_curl, CURLOPT_POSTFIELDS, array(
		'eventUid' 		=>	$eventUid,
		'access_token' 	=> 	$accessToken,
		'nonce' 		=> 	rand(),
		'data' 			=> 	json_encode($Openagenda_data)
	));

	$received_content = curl_exec($retour_curl);
	
	if ($received_content->error!="") { /* En cas d'erreur, arrêt du script avec die !  */
		die("Il existe une erreur  ['".$result_loc->error."']");
	}
	// ecrire_data($received_content);
	
	return $received_content;
}
/****************************************************************************************************************************************************** 
*  Mise à jour d'un événement 
* version 1 
* in  <-- clef secrète (L'activation de la clef secrète doit être demandé par mail à support@openagenda.com (nécessaire aux opérations d'écriture)
* out --> access_token 
* MAJ : 
* Version 1 - 2022-09-01 - ZeCrusher
******************************************************************************************************************************************************/

function access_token_get($secret)
{
  $Url_AccessToken =  'https://api.openagenda.com/v2/requestAccessToken';

  $retour_curl = curl_init(); /* Initialise une session cURL */ 

	/* Initialise une nouvelle session et retourne un identifiant de session cURL à utiliser avec les fonction */
    curl_setopt($retour_curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($retour_curl, CURLOPT_SSL_VERIFYPEER, 0);

	curl_setopt( $retour_curl, CURLOPT_URL, $Url_AccessToken );
	curl_setopt($retour_curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($retour_curl, CURLOPT_POST, true);

	curl_setopt($retour_curl, CURLOPT_POSTFIELDS, array(
		'grant_type' => 'authorization_code',
		'code' => $secret
	));

  $received_content = curl_exec($retour_curl);
	
  $access_token = json_decode( $received_content, true )["access_token"];

  if (json_decode( $received_content, true )["message"]!="") { /* ERREUR CAR la clef secrète est erronée */
	echo '<center> <br><br><br><b><font color="red">Il existe une erreur sur votre clef secrète</font></b><br>Clef en cour :'.$secret.' (fonction access_token_get)<br><br></center>';
  }
  
  return $access_token;
}



/* En TEST  ******************************************************************************************************************* */
/* 																*/
/* 															   	*/
/* 															      	*/


function updateOpenagendaEvent($accessToken, $agendaUid, $eventUid, $Openagenda_event_data, $Openagenda_event_adresse) {
    
    $url = "https://api.openagenda.com/v2/agendas/$agendaUid/events/$eventUid";
	
    // Construction du tableau final à envoyer
    $dataToSend = array_merge(
        $Openagenda_event_data,
        array('location' => $Openagenda_event_adresse)
    );

    // Initialisation de la requête cURL
    $ch = curl_init($url);
	
	ecrire_data($url);
	ecrire_data($accessToken);
	ecrire_data($agendaUid);
	ecrire_data($eventUid); 
	ecrire_data($Openagenda_event_data);
	ecrire_data($Openagenda_event_adresse);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Méthode PUT pour mise à jour
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataToSend));

    // Exécution de la requête
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);



    // Vérification du résultat
    if ($httpCode >= 200 && $httpCode < 300) {
        return array(
            'success' => true,
            'response' => json_decode($response, true)
        );
    } else {
        return array(
            'success' => false,
            'httpCode' => $httpCode,
            'error' => $curlError,
            'response' => json_decode($response, true)
        );
    }
}



/****************************************************************************************************************************************************** 
*  Afficher des données dans la console du navigateur
* version 1.1 
* in  <-- "texte" ou variable.
* out --> afficher du texte dans la console -F12 dans chrome par exemple.
* MAJ : 
* Version 1 - 2022-09-01 - ZeCrusher
******************************************************************************************************************************************************/

function write_to_console($data) 
{
	$var_dans_console = $data;
		if (is_array($var_dans_console))	{
			$var_dans_console = implode(',', $var_dans_console);
		}
	echo "<script>console.log('Console: " . $var_dans_console . "' );</script>";

}
?>
