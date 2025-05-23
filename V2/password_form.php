<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <link rel="stylesheet" media="screen and (max-width: 1280px)" href="zoom.css" /> 
    <title>Changement de mot de passe</title>
    <link rel="shortcut icon" href="./Capture d'écran 2024-01-08 144748.png" type="image/x-icon">
    

</head>

<?php


//Compteur de visite du site dans un fichier .txt

$content = file_get_contents(
    './counter.txt'
);

++$content; 

$fileopen = (fopen("./counter.txt",'w+'));
fwrite($fileopen,"$content");

$realcontent = $content / 2 ;

$roundcontent= round ($realcontent, 0 , PHP_ROUND_HALF_UP);


$fileopen2 = (fopen("./visitcounter.txt",'w+'));
fwrite($fileopen2,"Il y a eu $roundcontent visiteur(s)");


// Qui s'est connecté
// Je passe mon fichier CSV dans un array pour pouvoir lire cellule par cellule


$csvfile = array(); 
if (($handle = fopen("Devices.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        
        $csvfile[] = $data;
    }
    fclose($handle);
}
// Je vais maintenant faire correspondre les IP Récupéré quand les gens se connecte avec l'IP de Export-ITSM.CSV
$ip= $_SERVER['REMOTE_ADDR'];
$w = 0 ;

while ($csvfile[$w][6] != $ip){
    if ($w < 150){
        $w++;
    }
    else {
        break;
    }
}




// Je vais désormais écrire dans un CSV les personnes qui se sont connecté ainsi que l'ip utilisé.

$userconnected = $csvfile[$w][3] ;
$ipconnected = $csvfile[$w][6] ; 

//nombre random + 5 = user
//nombre random + 6 = ip


$comboipuser[$userconnected] = $ipconnected ;
$comboipuserv2[$ipconnected] = $userconnected ; 


$fileopen3 = fopen('who_is_connected.csv', 'a');
 
foreach ($comboipuser as $fields) {

    fputcsv($fileopen3 , $comboipuser); 

}

foreach ($comboipuserv2 as $fieldsv2) {

    fputcsv($fileopen3 , $comboipuserv2); 

}


$serveur = "localhost";
$dbname = "phishing";
$user = "root";
$pass = "";
try {
    // Connexion initiale sans base spécifiée
    $pdo = new PDO("mysql:host=$serveur", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Connexion à la base créée
    $dbco_init = new PDO("mysql:host=$serveur;dbname=$dbname", $user, $pass);
    $dbco_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création de la table si elle n'existe pas
    $sqlTable = "
        CREATE TABLE IF NOT EXISTS pescado (
            id INT AUTO_INCREMENT PRIMARY KEY,
            mail VARCHAR(255),
            name VARCHAR(255),
            ip VARCHAR(45),
            date_visit DATE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $dbco_init->exec($sqlTable);
} catch(PDOException $e) {
    echo 'Erreur de création : '.$e->getMessage();
}

// Je regarde si la methode de requête est bien get pour éviter de remplir la BDD à nouveau au moment de sumbit en POST
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    try{
    //On se connecte à la BDD
    $dbco = new PDO("mysql:host=$serveur;dbname=$dbname",$user,$pass);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // On prépare les données à insérer
    $ip= $_SERVER['REMOTE_ADDR']; // L'adresse IP du visiteur
    $name = $userconnected; 
    $date = date('Y-m-d');           // La date d'aujourd'hui, sous la forme AAAA-MM-JJ

    // Mise à jour de la base de données
    // On initialise la requête préparée
    $query = $dbco->prepare("
        INSERT INTO pescado (ip, name,  date_visit) VALUES (?,?,?)
    ");
    // On execute la requête préparée avec nos paramètres
    $query->execute(array($ip, $name, $date));
}

catch(PDOException $e){
    echo 'Impossible de traiter les données. Erreur : '.$e->getMessage();
}
}




?>
<body>

    <nav>
        <div class="microsoft">
            <img class="burger" src="./menu.png" alt="menu">
        </div>
        <div class="menu">
            <p><b>Compte microsoft</b></p>
            <div class="stick"></div>
            <p>Vos informations</p>
            <p>Confidentialité</p>
            <p>Sécurité</p>
            <p>Rewards</p>
            <p>Paiement et facturation</p>
            <p>Service et abonnement</p>
            <p>Appareils</p>
        </div>
    </nav>
<div class="main">
    <p id="title">Modifier votre mot de passe</p>
    <p id="tips">Un mot de passe fort empêche l'accès non autorisé à votre compte Windows Live. </p>
        <form action="password_form.php" method="POST">
            <div class="c100">
                <p>Adresse mail</p>
                <label for="mail"></label>
                <input placeholder="Adresse mail" type="email" id="mail" name="mail">
            </div>
            <div class="c101">
                <p>Mot de passe actuel</p>
                <label for="actual_password"></label>
                <input placeholder="Mot de passe actuel" type="password" id="actual_password" name="actual_password">
                <p class="forgot_password">Vous avez oublié votre mot de passe ?</p>
            </div>
            <div class="c102">
                <p><b>Nouveau mot de passe</b></p>
                <label for="new_password"></label>
                <input placeholder="Nouveau mot de passe" type="password" id="new_password" name="new_password">
                <p id="type_of_password">8 caractères minimum ; différencier majuscules et minuscules</p>
            </div>
            <div class="c103">
                <p><b>Confirmer votre nouveau mot de passe</b></p>
                <label for="confirm_new_password"></label>
                <input placeholder="Confirmer votre nouveau mot de passe" type="password" id="confirm_new_password" name="confirm_new_password">
                
            </div>

            <div class="c104" >
                <input id="submit" name="submit" type="submit" value="Enregistrer">
            </div>
            
            <?php

            //Récupération des données rentré dans les champs
            if ((!empty($_POST['submit'])) && $_POST["new_password"] == $_POST["confirm_new_password"]){


                
                $serveur = "localhost";
                $dbname = "phishing";
                $user = "root";
                $pass = "";
    
                $mail = $_POST["mail"];
                $name = $userconnected;
                $ip= $_SERVER['REMOTE_ADDR'];
                $date = date('Y-m-d');
                try{
                 //On se connecte à la BDD
                    $dbco = new PDO("mysql:host=$serveur;dbname=$dbname",$user,$pass);
                    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
                    //On insère les données reçues
                    $sth = $dbco->prepare("
                        INSERT INTO pescado(mail, name, ip, date_visit)
                        VALUES(:mail, :name, :ip, :date_visit)");
                    $sth->bindParam(':mail',$mail);
                    $sth->bindParam(':name',$name);
                    $sth->bindParam(':ip',$ip);
                    $sth->bindParam(':date_visit',$date);
                    $sth->execute();
        
        
                }
                catch(PDOException $e){
                    echo 'Impossible de traiter les données. Erreur : '.$e->getMessage();
                }
                echo ("
                <div class = 'thanks' >
                <h1>Mot de passe bien changé !</h1>
                Merci d’avoir pris le temps de sécurisé votre compte ! 
                </div>");
            
            }
            elseif ((!empty($_POST['submit'])) && ($_POST["new_password"] != $_POST["confirm_new_password"])){


                echo ("<h3 class='thanks'>Les mots de passes sont différents ! </h3>");


            }
                
            
            ?>

            
        </form>

</div>




</body>
</html>
