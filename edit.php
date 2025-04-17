<?php
include 'db.php';
include 'navbar.php';

$cef = $_GET['cef'] ?? '';
$stmt = $conn->prepare("SELECT * FROM etudiants WHERE cef = ?");
$stmt->execute([$cef]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    echo "Étudiant introuvable.";
    exit;
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $github = trim($_POST['github']);
    $filiere = $_POST['filiere'] ?? '';
    $gente = $_POST['gente'] ?? '';
    $loisirs = $_POST['loisirs'] ?? [];
    $imageName = $etudiant['image'];

    if (empty($fullName) || !preg_match("/^[A-Z][A-Za-z']{2,}(\s[A-Za-z']+)*$/", $fullName)) {
        $errors[] = "Nom complet invalide.";
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

    if (!empty($_FILES['image']['name'])) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileExt, $allowedExt)) {
            $errors[] = "Image invalide.";
        } else {
            $imageName = uniqid() . "." . $fileExt;
            move_uploaded_file($fileTmp, "uploads/" . $imageName);
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE etudiants SET fullName=?, email=?, github=?, filiere=?, image=?, gente=?, loisirs=? WHERE cef=?");
        $stmt->execute([
            $fullName,
            $email,
            $github,
            $filiere,
            $imageName,
            $gente,
            implode(', ', $loisirs),
            $cef
        ]);
        $success = "Étudiant mis à jour avec succès.";
        $stmt = $conn->prepare("SELECT * FROM etudiants WHERE cef = ?");
        $stmt->execute([$cef]);
        $etudiant = $stmt->fetch();
    }
}
?>

<h2>Modifier l'étudiant <?= $etudiant['fullName'] ?></h2>

<?php if ($success): ?>
    <div style="color:green;"><?= $success ?></div>
<?php endif; ?>

<?php if ($errors): ?>
    <div style="color:red;">
        <ul>
            <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
    <label>Nom complet:</label>
    <input type="text" name="fullName" value="<?= $etudiant['fullName'] ?>"><br><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?= $etudiant['email'] ?>"><br><br>

    <label>GitHub:</label>
    <input type="url" name="github" value="<?= $etudiant['github'] ?>"><br><br>

    <label>Filière:</label>
    <select name="filiere">
        <option value="">-- Choisissez --</option>
        <?php foreach (["GI", "TM", "GE"] as $opt): ?>
            <option value="<?= $opt ?>" <?= ($etudiant['filiere'] == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Image actuelle:</label>
    <img src="uploads/<?= $etudiant['image'] ?>" width="50"><br>
    <label>Changer image:</label>
    <input type="file" name="image"><br><br>

    <label>Gente:</label>
    <input type="radio" name="gente" value="Homme" <?= ($etudiant['gente'] == 'Homme') ? 'checked' : '' ?>> Homme
    <input type="radio" name="gente" value="Femme" <?= ($etudiant['gente'] == 'Femme') ? 'checked' : '' ?>> Femme
    <br><br>

    <label>Loisirs:</label><br>
    <?php
    $loisirsListe = ['Lecture', 'Sport', 'Musique', 'Voyage'];
    $etudiantLoisirs = explode(', ', $etudiant['loisirs']);
    foreach ($loisirsListe as $l):
    ?>
        <input type="checkbox" name="loisirs[]" value="<?= $l ?>" <?= in_array($l, $etudiantLoisirs) ? 'checked' : '' ?>> <?= $l ?><br>
    <?php endforeach; ?>
    <br>

    <input type="submit" value="Modifier">
</form>
