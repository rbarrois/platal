<?php
	
	global $globals;
        //chemin de stockage de la cl� priv�e  A MODIFIER
        $chemin_cle_privee = $globals->webservice->private_key_ax;
        //phrase cryptant la cl� priv�e
        $phrase = $globals->webservice->pass_ax;
//	"Cryptage de la cle pour polytechnique.org";
            
        //param�tres serveurs  
        $nom_script_renvoi_challenge = $globals->webservice->host_ax."serveur_genere_challenge.php";
        $nom_script_renvoi_infos = $globals->webservice->host_ax."serveur_renvoie_infos_ancien_direct.php";
?>