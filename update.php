<?php
session_start();
include 'config.php';

$errors = [];
$id = $_GET['id'] ?? '';

if ($id) {
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = $connect->query($sql);
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "Data tidak ditemukan!";
        exit;
    }
} else {
    echo "ID tidak valid!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = ($_POST['name']) ?? '';
    $email = ($_POST['email'])?? '';
    $phone = ($_POST['phone']) ?? '';
    $gender = $_POST['gender'] ?? '';
    $hobbies = $_POST['hobbies'] ?? [];


    if (empty($name)) {
        $errors['name'] = 'Nama harus diisi.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Nomor telepon harus diisi.';
    } elseif (!preg_match('/^[0-9]{10,13}$/', $phone)) {
        $errors['phone'] = 'Nomor telepon harus berisi 10-13 digit angka.';
    }

    if (empty($gender)) {
        $errors['gender'] = 'Jenis kelamin harus dipilih.';
    }

    if (empty($hobbies)) {
        $errors['hobbies'] = 'Pilih setidaknya satu hobi.';
    }

    if (empty($errors)) {
        $sql_check = "SELECT * FROM users WHERE (name = '$name' OR email = '$email' OR phone = '$phone') AND id != $id";
        $check_result = $connect->query($sql_check);
        if ($check_result->num_rows > 0) {
            $duplicate_names = [];
            $duplicate_emails = [];
            $duplicate_phones = [];

            while ($row = $check_result->fetch_assoc()) {
                if ($row['name'] == $name) {
                    $duplicate_names[] = $row['name'];
                }
                if ($row['email'] == $email) {
                    $duplicate_emails[] = $row['email'];
                }
                if ($row['phone'] == $phone) {
                    $duplicate_phones[] = $row['phone'];
                }
            }

            if (!empty($duplicate_names)) {
                $errors['duplicate_name'] = "Nama '$name' sudah terdaftar.";
            }
            if (!empty($duplicate_emails)) {
                $errors['duplicate_email'] = "Email '$email' sudah terdaftar.";
            }
            if (!empty($duplicate_phones)) {
                $errors['duplicate_phone'] = "Nomor telepon '$phone' sudah terdaftar.";
            }
        } else {
            $hobbies_serialized = implode(', ', $hobbies);
            
            $sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone', gender = '$gender', hobbies = '$hobbies_serialized' WHERE id = $id";
        
            if ($connect->query($sql)) {
                $_SESSION['message'] = 'Data berhasil diedit';
                header('Location: index.php');
                exit;
            } else {
                echo "Gagal memperbarui data: " . $connect->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit User</h2>

       <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php 
            foreach ($errors as $error) {
                echo $error . "<br>";
            }
            ?>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label>Nama:</label>
                <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= isset($name) ? htmlspecialchars($name) : htmlspecialchars($user['name']) ?>" required>
                <div class="invalid-feedback"><?= $errors['name'] ?? '' ?></div>
            </div>

            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= isset($email) ? htmlspecialchars($email) : htmlspecialchars($user['email']) ?>" required>
                <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
            </div>

            <div class="mb-3">
                <label>Phone:</label>
                <input type="text" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= isset($phone) ? htmlspecialchars($phone) : htmlspecialchars($user['phone']) ?>" required>
                <div class="invalid-feedback"><?= $errors['phone'] ?? '' ?></div>
            </div>

            <!-- Jenis Kelamin (Radio Button) -->
            <div class="mb-3">
                <label>Jenis Kelamin:</label><br>
                <div class="form-check form-check-inline">
                    <input type="radio" name="gender" value="Laki-laki" class="form-check-input <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" <?= (isset($gender) && $gender == 'Laki-laki') || $user['gender'] == 'Laki-laki' ? 'checked' : '' ?> required>
                    <label class="form-check-label">Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="gender" value="Perempuan" class="form-check-input <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" <?= (isset($gender) && $gender == 'Perempuan') || $user['gender'] == 'Perempuan' ? 'checked' : '' ?> required>
                    <label class="form-check-label">Perempuan</label>
                </div>
                <div class="invalid-feedback"><?= $errors['gender'] ?? '' ?></div>
            </div>

            <!-- Hobi (Checkbox) -->
            <div class="mb-3">
                <label>Hobi:</label><br>
                <?php
                $hobbies_selected = explode(', ', $user['hobbies']);
                ?>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="hobbies[]" value="Membaca" class="form-check-input <?= isset($errors['hobbies']) ? 'is-invalid' : '' ?>" <?= in_array('Membaca', $hobbies_selected) ? 'checked' : '' ?>>
                    <label class="form-check-label">Membaca</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="hobbies[]" value="Olahraga" class="form-check-input <?= isset($errors['hobbies']) ? 'is-invalid' : '' ?>" <?= in_array('Olahraga', $hobbies_selected) ? 'checked' : '' ?>>
                    <label class="form-check-label">Olahraga</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="hobbies[]" value="Menulis" class="form-check-input <?= isset($errors['hobbies']) ? 'is-invalid' : '' ?>" <?= in_array('Menulis', $hobbies_selected) ? 'checked' : '' ?>>
                    <label class="form-check-label">Menulis</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="hobbies[]" value="Memasak" class="form-check-input <?= isset($errors['hobbies']) ? 'is-invalid' : '' ?>" <?= in_array('Memasak', $hobbies_selected) ? 'checked' : '' ?>>
                    <label class="form-check-label">Memasak</label>
                </div>
                <div class="invalid-feedback"><?= $errors['hobbies'] ?? '' ?></div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
