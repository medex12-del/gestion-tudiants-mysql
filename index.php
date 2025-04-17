<?php
include 'db.php';
include 'navbar.php';

$stmt = $conn->query("SELECT * FROM etudiants");
$etudiants = $stmt->fetchAll();
?>

<h2>Liste des étudiants</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>CEF</th><th>Nom complet</th><th>Email</th><th>GitHub</th><th>Filière</th><th>Image</th><th>Genre</th><th>Loisirs</th><th>Actions</th>
    </tr>
    <?php foreach ($etudiants as $etudiant): ?>
    <tr>
        <td><?= $etudiant['cef'] ?></td>
        <td><?= $etudiant['fullName'] ?></td>
        <td><?= $etudiant['email'] ?></td>
        <td><a href="<?= $etudiant['github'] ?>" target="_blank">GitHub</a></td>
        <td><?= $etudiant['filiere'] ?></td>
        <td><img src="uploads/<?= $etudiant['image'] ?>" width="50"></td>
        <td><?= $etudiant['gente'] ?></td>
        <td><?= $etudiant['loisirs'] ?></td>
        <td>
            <a href="edit.php?cef=<?= $etudiant['cef'] ?>">Edit</a> |
            <a href="delete.php?cef=<?= $etudiant['cef'] ?>" onclick="return confirm('Supprimer ?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
