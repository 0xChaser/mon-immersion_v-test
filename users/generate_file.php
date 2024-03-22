<?php
if (!isset($_POST['activeDays']) || !isset($_POST['inactiveDays']) || !isset($_POST['disabledDates']) || !isset($_POST['nomService']) || !isset($_POST['nomAgence']) || !isset($_POST['email']) || !isset($_POST['joursMaxs'])) {
    http_response_code(400);
    echo 'Paramètre manquant';
    return;
}

$activeDays = json_decode($_POST['activeDays'], true);
$inactiveDays = json_decode($_POST['inactiveDays'], true);
$nomService = $_POST['nomService'];
$nomAgence = $_POST['nomAgence'];
$emailArray = $_POST['email'];
$joursMaxs = $_POST['joursMaxs'];

$disabledDatesRaw = json_decode($_POST['disabledDates'], true);
$disabledDates = [];
foreach ($disabledDatesRaw as $rawDate) {
    $date = date_create_from_format('Y-m-d\TH:i:s.u\Z', $rawDate);
    $disabledDates[] = date_format($date, 'Y-m-d');
}

$emailContent = "";
foreach ($emailArray as $email) {
    $emailContent .= "\$email[] = \"" . $email . "\";\n";

}

$fileContent = "<?php\n\n\$activeDays = " . var_export($activeDays, true) . ";\n\n\$inactiveDays = " . var_export($inactiveDays, true) . ";\n\n\$disabledDates = " . var_export($disabledDates, true) . ";\n\n\$joursMaxs = " . $joursMaxs . ";\n\n\$nomService = \"" . $nomService . "\";\n\n\$nomAgence = \"" . $nomAgence . "\";\n\n" . $emailContent . "\n\n?>";

file_put_contents('../calendrier_agences/' . $nomService . "_" . 'calendrier' . '_' . $nomAgence . '.php', $fileContent);

echo 'Fichier généré avec succès';
?>
