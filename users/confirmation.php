<!DOCTYPE html>
<html>
<head>
    <title>Mon Immersion - Confirmation de demande</title>
    <link rel="stylesheet" href="../css/confirmation.css">
    <link rel="shortcut icon" type="image/png" href="../img/logo_enedis.png">
</head>



<body>

<div class="container">

    <h1>Votre demande d'immersion a été envoyée !</h1>
    <h4>Elle est maintenant en attente de validation managériale<h4>
    <h6> Vous pourrez dès à présent la trouver dans votre espace : </h6>
    <a href="./own_immersions.php" class="link">Mes Immersions</a>
    <p>Envie de demander une autre immersion ? </p>

    <button id="redirectionButton">Retourner à l'accueil</button>

</div>

<script>

    var redirectionButton = document.getElementById("redirectionButton");
    
    redirectionButton.addEventListener("click", function() {

        window.location.href = "./accueil.php";
    });
    
</script>

</body>
</html>
