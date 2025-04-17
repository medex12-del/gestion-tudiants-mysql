<?php
include 'db.php';
include 'navbar.php';

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cef = trim($_POST['cef']);
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $github = trim($_POST['github']);
    $filiere = $_POST['filiere'] ?? '';
    $gente = $_POST['gente'] ?? '';
    $loisirs = $_POST['loisirs'] ?? [];
    $imageName = '';

    if (empty($cef)) {
        $errors[] = "Le CEF est obligatoire.";
    } elseif (!ctype_digit($cef)) {
        $errors[] = "Le CEF doit être numérique.";
    } else {
        $stmt = $conn->prepare("SELECT cef FROM etudiants WHERE cef = ?");
        $stmt->execute([$cef]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Ce CEF existe déjà.";
        }
    }

    if (empty($fullName)) {
        $errors[] = "Le nom complet est obligatoire.";
    } elseif (!preg_match("/^[A-Z][A-Za-z']{2,}(\s[A-Za-z']+)*$/", $fullName)) {
        $errors[] = "Le nom complet n'est pas valide.";
    }

    if (empty($filiere)) {
        $errors[] = "La filière est obligatoire.";
    }

    if (empty($gente)) {
        $errors[] = "La gente est obligatoire.";
    }

    if (count($loisirs) < 2) {
        $errors[] = "Choisissez au moins deux loisirs.";
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileExt, $allowedExt)) {
            $errors[] = "L'image doit être en format jpg, jpeg ou png.";
        } else {
            $imageName = uniqid() . "." . $fileExt;
            move_uploaded_file($fileTmp, "uploads/" . $imageName);
        }
    } else {
        $errors[] = "L'image est obligatoire.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO etudiants (cef, fullName, email, github, filiere, image, gente, loisirs) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $cef,
            $fullName,
            $email,
            $github,
            $filiere,
            $imageName,
            $gente,
            implode(', ', $loisirs)
        ]);
        $success = "Étudiant ajouté avec succès.";
    }
}
?>

<h2>Ajouter un étudiant</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php elseif ($success): ?>
    <div style="color:green;"><?= $success ?></div>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
    <label>CEF:</label>
    <input type="text" name="cef" value="<?= $_POST['cef'] ?? '' ?>"><br><br>

    <label>Nom complet:</label>
    <input type="text" name="fullName" value="<?= $_POST['fullName'] ?? '' ?>"><br><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?= $_POST['email'] ?? '' ?>"><br><br>

    <label>Lien GitHub:</label>
    <input type="url" name="github" value="<?= $_POST['github'] ?? '' ?>"><br><br>

    <label>Filière:</label>
    <select name="filiere">
        <option value="">-- Choisissez --</option>
        <option value="GI" <?= (($_POST['filiere'] ?? '') === "GI") ? 'selected' : '' ?>>GI</option>
        <option value="TM" <?= (($_POST['filiere'] ?? '') === "TM") ? 'selected' : '' ?>>TM</option>
        <option value="GE" <?= (($_POST['filiere'] ?? '') === "GE") ? 'selected' : '' ?>>GE</option>
    </select><br><br>

    <label>Image:</label>
    <input type="file" name="image"><br><br>

    <label>Gente:</label>
    <input type="radio" name="gente" value="Homme" <?= (($_POST['gente'] ?? '') === "Homme") ? 'checked' : '' ?>> Homme
    <input type="radio" name="gente" value="Femme" <?= (($_POST['gente'] ?? '') === "Femme") ? 'checked' : '' ?>> Femme
    <br><br>

    <label>Loisirs:</label><br>
    <input type="checkbox" name="loisirs[]" value="Lecture" <?= in_array("Lecture", $_POST['loisirs'] ?? []) ? 'checked' : '' ?>> Lecture<br>
    <input type="checkbox" name="loisirs[]" value="Sport" <?= in_array("Sport", $_POST['loisirs'] ?? []) ? 'checked' : '' ?>> Sport<br>
    <input type="checkbox" name="loisirs[]" value="Musique" <?= in_array("Musique", $_POST['loisirs'] ?? []) ? 'checked' : '' ?>> Musique<br>
    <input type="checkbox" name="loisirs[]" value="Voyage" <?= in_array("Voyage", $_POST['loisirs'] ?? []) ? 'checked' : '' ?>> Voyage<br><br>

    <input type="submit" value="Ajouter">
</form>
