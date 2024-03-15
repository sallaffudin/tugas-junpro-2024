<?php
include('../../config/connection.php');

function getCategories($searchQuery = '', $offset = 0, $limit = 10)
{
    global $conn;
    $categories = array();

    if (!$conn) {
        echo "Database connection failed: " . mysqli_connect_error();
        return $categories;
    }

    $query = "SELECT * FROM category";

    if (!empty($searchQuery)) {
        $query .= " WHERE (id LIKE '%" . $searchQuery . "%' OR judul LIKE '%" . $searchQuery . "%' OR deskripsi LIKE '%" . $searchQuery . "%')";
    }

    $query .= " LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    } else {
        echo "Error retrieving categories: " . mysqli_error($conn);
    }

    mysqli_free_result($result);

    return $categories;
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 4; // Jumlah kategori per halaman

// Hitung offset
$offset = ($page - 1) * $limit;

$categoriesData = getCategories($query, $offset, $limit);

$totalCategoriesQuery = "SELECT COUNT(*) AS total FROM category";
if (!empty($query)) {
    $totalCategoriesQuery .= " WHERE (id LIKE '%$query%' OR judul LIKE '%$query%' OR deskripsi LIKE '%$query%')";
}
$totalCategoriesResult = $conn->query($totalCategoriesQuery);
$totalCategoriesRow = $totalCategoriesResult->fetch_assoc();
$totalCategories = $totalCategoriesRow['total'];

// Hitung total halaman
$totalPages = ceil($totalCategories / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/category.css?v=1.3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <a href="../home/index.php" class="back-button"><i class="bi bi-arrow-left"></i> Back</a>
    <div class="container">
        <h1>Manage Categories</h1>
        <button id="addCategoryBtn" onclick="openAddCategoryModal()">+ Add Category</button>
        </a>
        <form action="" method="GET">
            <div class="search-container">
                <input name="query" type="text" placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>">
            </div>
        </form>
        <table id="categories-table">
            <thead>
                <tr>
                    <th style="width: 20px;">ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th style="width: 50px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categoriesData) > 0) : ?>
                    <?php foreach ($categoriesData as $category) : ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo $category['judul']; ?></td>
                            <td><?php echo $category['deskripsi']; ?></td>
                            <td class="action-buttons">
                                <a href="#" class="edit" onclick="openEditModal(<?php echo $category['id']; ?>)"><i class="bi bi-pencil-fill"></i></a>
                                <!-- Icon Edit -->
                                <a href="#" class="delete" onclick="openDeleteConfirmationModal(<?php echo $category['id']; ?>, '<?php echo $category['judul']; ?>')"><i class="bi bi-trash"></i></a>
                                <!-- Icon Hapus -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Data not found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1) : ?>
            <div class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?>&query=<?php echo urlencode($query); ?>">Sebelumnya</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>&query=<?php echo urlencode($query); ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?php echo $page + 1; ?>&query=<?php echo urlencode($query); ?>">Selanjutnya</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Add Category -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddCategoryModal()">&times;</span>
            <h2>Add Category</h2>
            <form action="add_category.php" method="POST" id="addCategoryForm">
                <label for="add-title">Title:</label><br>
                <input type="text" id="add-title" name="add-title" required><br>
                <label for="add-description">Description:</label><br>
                <input type="text" id="add-description" name="add-description" required><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Category</h2>
            <div id="editSuccessNotification" class="notification"></div>
            <form action="edit_category.php" method="POST" id="editForm">
                <input type="hidden" id="edit-category-id" name="edit-category-id" value="">
                <label for="edit-title">Title:</label><br>
                <input type="text" id="edit-title" name="edit-title" value=""><br>
                <label for="edit-description">Description:</label><br>
                <input type="text" id="edit-description" name="edit-description" value=""><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>

    <div id="deleteConfirmationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteConfirmationModal()">&times;</span>
            <h2>Delete Confirmation</h2>
            <p id="delete-category-message"></p>
            <button onclick="deleteCategory()">Yes</button>
            <button onclick="closeDeleteConfirmationModal()">No</button>
        </div>
    </div>

    <script>
        function openAddCategoryModal() {
            var modal = document.getElementById("addCategoryModal");
            modal.style.display = "block";
        }

        function closeAddCategoryModal() {
            var modal = document.getElementById("addCategoryModal");
            modal.style.display = "none";
        }

        document.getElementById("addCategoryForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah pengiriman formulir default
            var formData = new FormData(this);

            fetch("add_category.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Category added successfully!");
                        closeAddCategoryModal(); // Tutup modal
                        window.location.reload(); // Muat ulang halaman untuk memperbarui tampilan kategori
                    } else {
                        alert(data.error); // Tampilkan notifikasi gagal jika ada
                    }
                })
                .catch(error => {
                    console.error("Error adding category:", error);
                    alert("An error occurred. Please try again later.");
                });
        });

        function openAddCategoryModal() {
            var modal = document.getElementById("addCategoryModal");
            modal.style.display = "block";
        }

        function closeAddCategoryModal() {
            var modal = document.getElementById("addCategoryModal");
            modal.style.display = "none";
        }

        function openEditModal(categoryId) {
            var modal = document.getElementById("editModal");
            modal.style.display = "block";

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var categoryData = JSON.parse(xhr.responseText);
                        document.getElementById("edit-category-id").value = categoryData.id;
                        document.getElementById("edit-title").value = categoryData.judul;
                        document.getElementById("edit-description").value = categoryData.deskripsi;
                    } else {
                        console.error('Error fetching category data: ' + xhr.status);
                    }
                }
            };
            xhr.open("GET", "edit_category.php?id=" + categoryId, true);
            xhr.send();
        }

        function closeEditModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        }

        function openDeleteConfirmationModal(categoryId, categoryName) {
            var modal = document.getElementById("deleteConfirmationModal");
            var message = document.getElementById("delete-category-message");
            message.textContent = "Are you sure you want to delete category \"" + categoryName + "\"?";
            modal.setAttribute("data-category-id", categoryId);
            modal.style.display = "block";
        }

        function closeDeleteConfirmationModal() {
            var modal = document.getElementById("deleteConfirmationModal");
            modal.style.display = "none";
        }

        function deleteCategory() {
            var modal = document.getElementById("deleteConfirmationModal");
            var categoryId = modal.getAttribute("data-category-id");

            fetch("delete_category.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "id=" + categoryId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Category deleted successfully!");
                        closeDeleteConfirmationModal();
                        window.location.reload(); // Reload halaman untuk memperbarui tampilan kategori
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error("Error deleting category:", error);
                    alert("An error occurred. Please try again later.");
                });
        }

        // Tangani pengiriman formulir secara asinkron
        document.getElementById("editForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah pengiriman formulir default
            var formData = new FormData(this);

            fetch("edit_category.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Category updated successfully!");
                        closeEditModal(); // Tutup modal
                        window.location.href = "category.php"; // Alihkan kembali ke halaman category.php
                    } else {
                        alert(data.error); // Tampilkan notifikasi gagal jika ada
                    }
                })
                .catch(error => {
                    console.error("Error updating category data:", error);
                    alert("An error occurred. Please try again later.");
                });
        });
    </script>
</body>

</html>