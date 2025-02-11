<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'todo');

// Tambah Task
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($task) && !empty($priority) && !empty($due_date)) {
        mysqli_query($koneksi, "INSERT INTO tasks VALUES(NULL, '$task','$priority', '$due_date', '0')");
        echo "<script>alert('Data berhasil disimpan')</script>";
    } else {
        echo "<script>alert('Semua Kolom Harus Diisi!')</script>";
    }
}

// Menandai Task Selesai
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($koneksi, "UPDATE tasks SET status=1 WHERE id=$id");
    echo "<script>alert('Data Berhasil diperbarui')</script>";
    header('location:index.php');
}

// Menghapus Task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM tasks WHERE id=$id");
    echo "<script>alert('Data Berhasil dihapus')</script>";
    header('location:index.php');
}

$result = mysqli_query($koneksi, "SELECT * FROM tasks ORDER BY status ASC, priority DESC, due_date ASC");


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Todo List | UKK RPL 2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-2">
        <h2 class="text-center">Aplikasi Todo List</h2>
        <form method="POST" class="border rounded bg-light p-2">
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" placeholder="Masukan Task Baru" autocomplete="off" autofocus required>
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="">--Pilih Prioritas--</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>
            <label class="form-label">Tanggal</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-d') ?>" required>
            <button class="btn btn-primary w-100 mt-2" name="add_task">Tambah</button>
        </form>

        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Task</th>
                    <th>Prioritas</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['task']; ?></td>
                            <td><?php
                                if ($row['priority'] == 1) {
                                    echo "Low";
                                } elseif ($row['priority'] == 2) {
                                    echo "Medium";
                                } else {
                                    echo "High";
                                }
                                ?>
                            </td>
                            <td><?php echo $row['due_date']; ?></td>
                            <td><?php
                                if ($row['status'] == 0) {
                                    echo "Belum Selesai";
                                } else {
                                    echo "Selesai";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($row['status'] == 0) { ?>
                                    <a href="?complete=<?php echo $row['id'] ?>" class="btn btn-success">Selesai</a>
                                <?php  }
                                ?>
                                <a href="?delete=<?php echo $row['id'] ?>" class="btn btn-danger">Hapus</a>
                            </td>
                        </tr>
                <?php }
                } else {
                    echo "Tidak ada Data";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>