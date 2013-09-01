<?php
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

define('_CONTACT','Formulaire de contact');
define('_CONTACTFORM','Veuillez remplir le formulaire ci-dessous puis cliquer sur Envoyer');
define('_YNICK','Votre Nom');
define('_YMAIL','Votre Email');
define('_YSUBJECT','Objet');
define('_YCOMMENT','Votre message');
define('_SEND','Envoyer');
define('_NOCONTENT','Vous avez oublié de remplir des champs obligatoires');
define('_NONICK','Vous n\'avez pas entré votre nom !');
define('_NOSUBJECT','Vous n\'avez pas entré de sujet !');
define('_BADMAIL','Adresse email non valide !');
define('_SENDCMAIL','Votre email a bien été envoyé, nous vous répondrons dans les plus brefs délais.');
define('_FLOODCMAIL','Vous avez déja posté un mail il y\'a moins de ' . $nuked['contact_flood'] . ' minutes,<br />veuillez patienter avant de renvoyer un autre email...');

define('_NOENTRANCE','Désolé mais vous n\'avez pas les droits pour accéder à cette page');
define('_ZONEADMIN','Cette zone est réservée a l\'Admin, Désolé...');
define('_NOEXIST','Désolé cette page n\'existe pas ou l\'adresse que vous avez tapé est incorrecte');
define('_ADMINCONTACT','Administration Contact');
define('_HELP','Aides');
define('_DELETEMESSAGEFROM','Vous êtes sur le point de supprimer le message de');
define('_LISTMAIL','Liste des messages');
define('_PREFS','Préférences');
define('_TITLE','Titre');
define('_NAME','Nom');
define('_DATE','Date');
define('_READMESS','Lire');
define('_DEL','Supprimer');
define('_BACK','Retour');
define('_FROM','De');
define('_THE','le');
define('_NOMESSINDB','Aucun message dans la base de données');
define('_READTHISMESS','Lire ce message');
define('_DELTHISMESS','Supprimer ce message');
define('_MESSDELETE','Message supprimé avec succès');
define('_PREFUPDATED','Préférences modifiées avec succès.');
define('_EMAILCONTACT','Email de reception');
define('_FLOODCONTACT','Durée en minutes entre 2 messages (flood)');
define('_NOTCON','Vous avez reçu un mail contact');
define('_ACTIONDELCONTACT','a supprimé un mail contact reçu');
define('_ACTIONPREFCONT','a modifié les préférences du module contact');
?>
