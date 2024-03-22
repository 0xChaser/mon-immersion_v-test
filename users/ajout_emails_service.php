<?php
require_once "../database/config.php";

if (isset($_POST['emails']) && isset($_POST['nomAgence']) && isset($_POST['nomService'])) {
    $emails = $_POST['emails'];
    $nomAgence = $_POST['nomAgence'];
    $nomService = $_POST['nomService'];
    $heure_debut = $_POST['dateDebutImmersion'];
    $heure_fin = $_POST['dateFinImmersion'];

    try {
        $bdd = connect();

        $query = "UPDATE vue_agences SET email_service = :emails, heure_debut =:heure_debut, heure_fin =:heure_fin WHERE nomAgence = :nomAgence AND nomService = :nomService";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':emails', json_encode($emails));
        $stmt->bindParam(':heure_debut', $heure_debut);
        $stmt->bindParam(':heure_fin', $heure_fin);
        $stmt->bindParam(':nomAgence', $nomAgence);
        $stmt->bindParam(':nomService', $nomService);
        $stmt->execute();

        $response = array('success' => true, 'message' => 'Mise à jour réussie');
        echo json_encode($response);
    } catch (PDOException $e) {

        $response = array('success' => false, 'message' => 'Erreur lors de la mise à jour');
        echo json_encode($response);
    }
} else {
    $response = array('success' => false, 'message' => 'Données manquantes');
    echo json_encode($response);
}
?>
