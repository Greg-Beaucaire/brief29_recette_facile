<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recette Facile</title>
  <meta name="description" content="Des recettes qu'elles sont faciles !">
</head>
<body><pre><?php

  // séparer ses identifiants et les protéger, une bonne habitude à prendre
  include "db_connect.php";

  try {

    // instancie un objet $connexion à partir de la classe PDO
    $connexion = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

    // Requête de sélection dans la table 'recettes'
    $requete = "SELECT * FROM `recettes`";
    $prepare = $connexion->prepare($requete);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]); // debug & vérification


    // Requête d'insertion dans la table 'recettes'
    $requete = "INSERT INTO `recettes` (`recette_titre`, `recette_contenu`)
                VALUES (:recette_titre, :recette_contenu);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":recette_titre" => "Saucisse au ketchup",
      ":recette_contenu" => "Faire cuire des saucisses et y ajouter du ketchup GGWPNORE"
    ));
    $resultat = $prepare->rowCount(); // rowCount() pour check combien de row ont été ajouté
    $lastInsertedRecetteId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL, on s'en servira en dessous pour la modification et la suppression
    print_r([$requete, $resultat, $lastInsertedRecetteId]); // debug & vérification

    //Requête de modification
    $requete = "UPDATE `recettes`
                SET `recette_titre` = :recette_titre -- Ici on précise ce qu'on souhaite modifier
                WHERE `recette_id` = :recette_id;"; // Et là on target avec l'id pour trouver l'élément à modifier
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":recette_id"   => $lastInsertedRecetteId,
      ":recette_titre" => "Saucisse au ketchup trobonne" // parce que c'est trobon les saucisses au ketchup
    ));
    $resultat = $prepare->rowCount();
    print_r([$requete, $resultat]); // debug & vérification

    // Requête de suppression, j'ai choisi ici de supprimer l'entrée que je vennais d'ajouter
    $requete = "DELETE FROM `recettes`
                WHERE ((`recette_id` = :recette_id));";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array($lastInsertedRecetteId)); // on lui passe l'id tout juste créé
    $resultat = $prepare->rowCount();
    print_r([$requete, $resultat, $lastInsertedRecetteId]); // debug & vérification

    // Requête d'insertion dans la table 'hashtags'
    $requete = "INSERT INTO `hashtags` (`hashtag_nom`)
                VALUES (:hashtag_nom);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":hashtag_nom" => "levain"
    ));
    $resultat = $prepare->rowCount(); // rowCount() pour check combien de row ont été ajouté
    $lastInsertedHashtagId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requete, $resultat, $lastInsertedHashtagId]); // debug & vérification
    
    // Requête d'insertion dans la table associatives 'assoc_hashtags_recettes'
    $requete = "INSERT INTO `assoc_hashtags_recettes` (`assoc_hr_hashtag_id`, assoc_hr_recette_id)
                VALUES (:assoc_hr_hashtag_id, :assoc_hr_recette_id);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":assoc_hr_hashtag_id" => "4",
      ":assoc_hr_recette_id" => "1"
    ));
    $resultat = $prepare->rowCount(); // rowCount() pour check combien de row ont été ajouté
    $lastInsertedAssocId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requete, $resultat, $lastInsertedAssocId]); // debug & vérification

    // Requête de sélection pour afficher le titre des recettes en visant le hashtag 'nourriture'
    $requete = "SELECT recette_titre -- L'élément qu'on souhaite pull
                FROM assoc_hashtags_recettes -- on le pull depuis la table associative grâce aux deux lignes du dessous
                JOIN recettes on recettes.recette_id = assoc_hashtags_recettes.assoc_hr_recette_id -- JOIN pour créer la liaison dans la requête entre la table 'recettes' et la table assoc
                JOIN hashtags on hashtags.hashtag_id = assoc_hashtags_recettes.assoc_hr_hashtag_id -- JOIN pour créer la liaison dans la requête entre la table 'hashtags' et la table assoc
                WHERE hashtags.hashtag_nom = 'nourriture' "; // ici on spécifie qu'on ne veut pull que les entrées recette_titre associées au hashtag 'nourriture'
    $prepare = $connexion->prepare($requete);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]); // debug & vérification

  } catch (PDOException $e) {

    // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
    exit("❌🙀💀 OOPS :\n" . $e->getMessage());

  }

?></pre></body>
</html>