<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "db_scanbl3";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search parameter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get page number, default to page 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 25; // Number of records per page
$offset = ($page - 1) * $limit; // Calculate offset for query

// Base query conditions
$where_clause = $search ? "WHERE rss_code LIKE '%$search%' OR product LIKE '%$search%' OR jam_code LIKE '%$search%'" : "";

// Query to get total number of records
$total_query = "SELECT COUNT(*) AS total FROM add_master $where_clause";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_data / $limit);

// Query to get data with LIMIT and OFFSET
$query = "
    SELECT 
        id_master,
        rss_code AS Barcode,
        product AS Product,
        jam_code AS JamCode
    FROM add_master
    $where_clause
    ORDER BY product ASC
    LIMIT $limit OFFSET $offset
";

$result = $conn->query($query);
?>

<!-- Custom Modal Styles -->
<style>
.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(32, 44, 49, 0.8);
    z-index: 1000;
}

.custom-modal-content {
    background-color: #202C31;
    width: 400px;
    margin: 50px auto;
    padding: 20px;
    border-radius: 8px;
    position: relative;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    color: #FFFFFF;
}

.custom-modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 15px;
    margin-bottom: 15px;
}

.custom-modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: bold;
    color: #FFFFFF;
}

.custom-close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 24px;
    cursor: pointer;
    background: none;
    border: none;
    color: #FFFFFF;
    transition: color 0.3s ease;
}

.custom-close:hover {
    color: #ED1B24;
}

.custom-form-group {
    margin-bottom: 15px;
}

.custom-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #FFFFFF;
}

.custom-form-group input {
    width: 93%;
    padding: 8px 12px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    font-size: 14px;
    background-color: rgba(255, 255, 255, 0.05);
    color: #FFFFFF;
    transition: all 0.3s ease;
}

.custom-form-group input:focus {
    outline: none;
    border-color:rgb(255, 255, 255);
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
}

.custom-btn {
    background-color: #ED1B24;
    color: #FFFFFF;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.3s ease;
    display: block;
    margin: 0 auto;
}

.custom-btn:hover {
    background-color: #c41118;
}

.edit-icon, .delete-icon {
    width: 20px;  /* Set base size for icons */
    height: 20px;
    transition: transform 0.2s ease;  /* Smooth transition for scaling */
    cursor: pointer;
}

.delete-icon {
    margin-left: 20px;
}

.edit-icon:hover, .delete-icon:hover {
    transform: scale(1.2);  /* Scale up to 120% on hover */
}

.custom-text-note-container {
    background-color: rgba(255, 255, 255, 0.1); /* Slightly brighter than modal background */
    border-left: 4px solid yellow;
    padding: 10px;
    margin-top: 20px;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Shadow for 3D effect */
    color: yellow;
    font-size: 0.9rem;
}

</style>

<!-- Edit Modal Structure -->
<div id="editModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">Edit Product</h5>
            <button type="button" class="custom-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" action="update_product.php" method="POST">
            <input type="hidden" id="edit_id_master" name="id_master">
            
            <div class="custom-form-group">
                <label for="edit_barcode">Barcode:</label>
                <input type="text" id="edit_barcode" name="barcode" required>
            </div>
            
            <div class="custom-form-group">
                <label for="edit_product">Product:</label>
                <input type="text" id="edit_product" name="product" required>
            </div>
            
            <div class="custom-form-group">
                <label for="edit_jam_code">Jam Code:</label>
                <input type="text" id="edit_jam_code" name="jam_code">
                <div class="custom-text-note-container">
                    Note: This action is permanent. Please edit the product details carefully!
                </div>
            </div>
            
            <button type="submit" class="custom-btn">Update</button>
        </form>
    </div>
</div>

<!-- Delete Modal Structure -->
<div id="deleteModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">Delete Product</h5>
            <button type="button" class="custom-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <form id="deleteForm" action="delete_product.php" method="POST">
            <input type="hidden" id="delete_id_master" name="id_master">
            <input type="hidden" id="actual_product_name" name="actual_product_name">
            
            <div class="custom-form-group">
                <label for="confirm_product">Enter product name to confirm deletion:</label>
                <input type="text" id="confirm_product" name="confirm_product" required 
                       placeholder="Type product name here">
                <div class="custom-text-note-container">
                    Note: This action is permanent. Please select the correct product carefully!
                </div>
            </div>
            
            <button type="submit" class="custom-btn">Delete</button>
        </form>
    </div>
</div>


<!-- Modal JavaScript -->
<script>

       // Regex untuk karakter berbahaya
const dangerousPattern = /[<>"'\/\\;`&=]/;



// Edit Form Validation
const editForm = document.getElementById("editForm");
if (editForm) {
    editForm.addEventListener("submit", function(event) {
        event.preventDefault();
        
        const realName = document.getElementById("edit_barcode").value;
        const username = document.getElementById("edit_product").value;
        const jamcode = document.getElementById("edit_jam_code").value;

        if (dangerousPattern.test(realName) || dangerousPattern.test(username) || dangerousPattern.test(jamcode)) {
            showNotification("Invalid characters detected in Real Name or Username.(<, >, &, \", ', /, =)", false);
            return;
        }

        if (!realName || !username || !jamcode) {
            showNotification("Please fill in all fields.", false);
            return;
        }

        

        editForm.submit(); // Kirim form jika valid
    });
}



function showNotification(message, isSuccess) {
    // Buat elemen notifikasi
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <img src="../assets/${isSuccess ? 'icon-success.png' : 'icon-error.png'}" alt="${isSuccess ? 'Success' : 'Error'}" style="width: 24px; height: 24px;">
            <span>${message}</span>
        </div>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: ${isSuccess ? '#4CAF50' : '#F44336'};
        color: white;
        padding: 15px;
        border-radius: 4px;
        z-index: 1000;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        min-width: 300px;
        font-family: 'Arial', sans-serif;
        font-size: 14px;
        display: flex;
        align-items: center;
        animation: slideIn 0.5s ease-out;
    `;

    // Tambahkan notifikasi ke body
    document.body.appendChild(notification);

    // Hapus notifikasi setelah 2 detik
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.5s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500); // Waktu untuk animasi slideOut
    }, 2000); // Notifikasi muncul selama 2 detik
}

// Animasi CSS untuk notifikasi
const style = document.createElement('style');
style.innerHTML = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

function openEditModal(data) {
    document.getElementById('edit_id_master').value = data.id;
    document.getElementById('edit_barcode').value = data.barcode;
    document.getElementById('edit_product').value = data.product;
    document.getElementById('edit_jam_code').value = data.jamCode;
    
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openDeleteModal(data) {
    document.getElementById('delete_id_master').value = data.id;
    document.getElementById('actual_product_name').value = data.product;
    document.getElementById('confirm_product').value = '';
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Combined modal close handler
window.onclick = function(event) {
    var editModal = document.getElementById('editModal');
    var deleteModal = document.getElementById('deleteModal');
    if (event.target == editModal) {
        closeEditModal();
    }
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}

// Delete form validation
document.getElementById('deleteForm').onsubmit = function(e) {
    e.preventDefault();
    var actualProduct = document.getElementById('actual_product_name').value;
    var confirmProduct = document.getElementById('confirm_product').value;

    if (dangerousPattern.test(confirmProduct)||dangerousPattern.test(actualProduct)) {
            showNotification("Invalid characters detected in Username confirmation.(<, >, &, \", ', /, =)",false);
            return;
        }
    
    if (actualProduct === confirmProduct) {
        this.submit();
    } else {
        showNotification('Product name does not match. Please try again.','error');
    }
}

let notificationContainer = document.createElement('div');
    notificationContainer.id = 'notification-container';
    document.body.appendChild(notificationContainer);

function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        const iconSrc = type === 'success' ? '../assets/icon-success.png' : '../assets/icon-error.png';
        const bgColor = type === 'success' ? '#4CAF50' : '#f44336';

        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="${iconSrc}" alt="${type}" style="width: 24px; height: 24px;">
                <span>${message}</span>
            </div>
        `;

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: ${bgColor};
            color: white;
            padding: 15px;
            border-radius: 4px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            min-width: 300px;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            display: flex;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        `;

        notificationContainer.appendChild(notification);

         // Fade in
         setTimeout(() => {
            notification.style.opacity = '1';
        }, 100);

        // Remove notification after 2 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 2000);
}
</script>

<?php
// Display table data
if ($result->num_rows > 0) {
    $no = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no . "</td>";
        echo "<td>" . (!empty($row['Barcode']) ? htmlspecialchars($row['Barcode']) : "-") . "</td>";
        echo "<td>" . (!empty($row['Product']) ? htmlspecialchars($row['Product']) : "-") . "</td>";
        echo "<td>" . (!empty($row['JamCode']) ? htmlspecialchars($row['JamCode']) : "-") . "</td>";
        echo "<td class='action-column'>";
        echo "<a href='#' onclick='openEditModal(" . 
            json_encode([
                "id" => $row['id_master'],
                "barcode" => $row['Barcode'],
                "product" => $row['Product'],
                "jamCode" => $row['JamCode']
            ]) . ")'>";
        echo "<img src='../assets/edit.png' alt='Edit' class='edit-icon'>";
        echo "</a>";
        echo "<a href='#' onclick='openDeleteModal(" . 
            json_encode([
                "id" => $row['id_master'],
                "product" => $row['Product']
            ]) . ")'>";
        echo "<img src='../assets/trash.png' alt='Delete' class='delete-icon'>";
        echo "</a>";
        echo "</td>";
        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='5'>Data tidak ditemukan</td></tr>";
}

$conn->close();
?>