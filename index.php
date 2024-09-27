<?php
session_start();
include 'config.php';

$errors = [];
$message = $_SESSION['message'] ?? ''; 
unset($_SESSION['message']);

$sql = "SELECT * FROM users";
$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Sederhana</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Data Users</h2>
        <a href="create.php" class="btn btn-success mb-3">Tambah User</a>

          <!-- Alert Message -->
          <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Tabel Users -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td>
                            <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal" data-id="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-email="<?= $row['email'] ?>" data-phone="<?= $row['phone'] ?>" data-gender="<?= $row['gender'] ?>" data-hobbies="<?= $row['hobbies'] ?>">Detail</button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $row['id'] ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama:</strong> <span id="detailName"></span></p>
                    <p><strong>Email:</strong> <span id="detailEmail"></span></p>
                    <p><strong>Phone:</strong> <span id="detailPhone"></span></p>
                    <p><strong>Jenis Kelamin:</strong> <span id="detailGender"></span></p>
                    <p><strong>Hobi:</strong> <span id="detailHobbies"></span></p>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Konfirmasi Hapus Data-->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        // Delete
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-id'); 
            var confirmDelete = deleteModal.querySelector('#confirmDelete'); 

            confirmDelete.href = 'delete.php?id=' + userId;
        });

        // Detail
        var detailModal = document.getElementById('detailModal');
        detailModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button yang diklik
            var name = button.getAttribute('data-name');
            var email = button.getAttribute('data-email');
            var phone = button.getAttribute('data-phone');
            var gender = button.getAttribute('data-gender');
            var hobbies = button.getAttribute('data-hobbies');

            detailModal.querySelector('#detailName').textContent = name;
            detailModal.querySelector('#detailEmail').textContent = email;
            detailModal.querySelector('#detailPhone').textContent = phone;
            detailModal.querySelector('#detailGender').textContent = gender;
            detailModal.querySelector('#detailHobbies').textContent = hobbies;
        });
    </script>
</body>
</html>
