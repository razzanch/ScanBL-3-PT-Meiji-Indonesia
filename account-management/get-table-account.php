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
$where_clause = $search ? "WHERE username LIKE '%$search%' OR real_name LIKE '%$search%'" : "";

// Query to get total number of records
$total_query = "SELECT COUNT(*) AS total FROM accounts $where_clause";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_data / $limit);

// Query to get data with LIMIT and OFFSET
$query = "
    SELECT 
        id_account AS id,
        real_name AS RealName,
        username AS Username,
        role AS Role,
        date AS Date
    FROM accounts
    $where_clause
    ORDER BY date DESC
    LIMIT $limit OFFSET $offset
";

$result = $conn->query($query);
?>

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
    width: 190px;
    
}

.custom-btn:hover {
    background-color: #c41118;
}

.custom-btn.reset-btn {
    background-color:rgb(0, 0, 0);
    border-color: black;
}
.custom-btn.reset-btn:hover {
    background-color:rgb(0, 0, 0);
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
            <h5 class="custom-modal-title">Edit Account</h5>
            <button type="button" class="custom-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" action="update_account.php" method="POST">
            <input type="hidden" id="edit_id_account" name="id_account">
            
            <div class="custom-form-group">
                <label for="edit_real_name">Real Name:</label>
                <input type="text" id="edit_real_name" name="real_name" required>
            </div>
            
            <div class="custom-form-group">
                <label for="edit_username">Username:</label>
                <input type="text" id="edit_username" name="username" required>
                <div class="custom-text-note-container">
                Note: This action is permanent. Please edit the account details carefully!
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <button type="submit" class="custom-btn">Update</button>
                <button type="button" class="custom-btn reset-btn" onclick="resetPassword()">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal Structure -->
<div id="deleteModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">Delete Account</h5>
            <button type="button" class="custom-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <form id="deleteForm" action="delete_account.php" method="POST">
            <input type="hidden" id="delete_id_account" name="id_account">
            <input type="hidden" id="actual_username" name="actual_username">
            
            <div class="custom-form-group">
                <label for="confirm_username">Enter username to confirm deletion:</label>
                <input type="text" id="confirm_username" name="confirm_username" required 
                       placeholder="Type username here">
                <div class="custom-text-note-container">
                    Note: This action is permanent. Please select the correct account carefully!
                </div>
            </div>
            
            <button type="submit" class="custom-btn">Delete</button>
        </form>
    </div>
</div>

<!-- Modal JavaScript -->
<script>
    // Regex untuk karakter berbahaya
    const dangerousPattern = /[<>\"'\/\\;`\-&=]/;


// Edit Form Validation
const editForm = document.getElementById("editForm");
if (editForm) {
    editForm.addEventListener("submit", function(event) {
        event.preventDefault();
        
        const realName = document.getElementById("edit_real_name").value;
        const username = document.getElementById("edit_username").value;

        if (!realName || !username) {
            showNotification("Please fill in all fields.", false);
            return;
        }

        if (dangerousPattern.test(realName) || dangerousPattern.test(username)) {
            showNotification("Invalid characters detected in Real Name or Username.(<, >, &, \", ', /, =)", false);
            return;
        }

        editForm.submit(); // Kirim form jika valid
    });
}

// Delete Form Validation
const deleteForm = document.getElementById("deleteForm");
if (deleteForm) {
    deleteForm.addEventListener("submit", function(event) {
        event.preventDefault();
        
        const confirmUsername = document.getElementById("confirm_username").value;

        if (!confirmUsername) {
            showNotification("Please enter the username to confirm deletion.", false);
            return;
        }

        if (dangerousPattern.test(confirmUsername)) {
            showNotification("Invalid characters detected in Username confirmation.(<, >, &, \", ', /, =)",false);
            return;
        }

        deleteForm.submit(); // Kirim form jika valid
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

function scrollToTable() {
        const tableElement = document.querySelector('table');
        if (tableElement) {
            tableElement.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
function openEditModal(data) {
    document.getElementById('edit_id_account').value = data.id;
    document.getElementById('edit_real_name').value = data.realName;
    document.getElementById('edit_username').value = data.username;
    
    document.getElementById('editModal').style.display = 'block';
    return false;
}

function resetPassword() {
    const idAccount = document.getElementById('edit_id_account').value;

    if (idAccount) {
        const formData = new FormData();
        formData.append('id_account', idAccount);

        fetch('reset_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            closeEditModal(); // Tutup modal
            
            // Dapatkan URL redirect dari response
            const redirectUrl = response.url;

            localStorage.setItem('shouldScrollToTable', 'true');
            
            // Arahkan ke URL tersebut (yang sudah include parameter success/error)
            window.location.href = redirectUrl;
        })
        .catch(error => {
            console.error('Error:', error);
            window.location.href = 'account-management.php?reset_error=1';
        });
    } 
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openDeleteModal(data) {
    document.getElementById('delete_id_account').value = data.id;
    document.getElementById('actual_username').value = data.username;
    document.getElementById('confirm_username').value = '';
    document.getElementById('deleteModal').style.display = 'block';
    return false;
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

 // Modify edit form submission
 if (document.getElementById('editForm')) {
        document.getElementById('editForm').addEventListener('submit', function() {
            localStorage.setItem('shouldScrollToTable', 'true');
        });
    }

    // Modify delete form submission
    if (document.getElementById('deleteForm')) {
        document.getElementById('deleteForm').addEventListener('submit', function() {
            localStorage.setItem('shouldScrollToTable', 'true');
        });
    }

    if (localStorage.getItem('shouldScrollToTable') === 'true') {
        localStorage.removeItem('shouldScrollToTable');
        setTimeout(scrollToTable, 100); // Small delay to ensure table is rendered
    }
</script>

<?php
// Display table data
if ($result->num_rows > 0) {
    $no = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no . "</td>";
        echo "<td>" . htmlspecialchars($row['RealName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Role']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
        echo "<td class='action-column'>";
        echo "<a href='#' onclick='openEditModal(" . 
    json_encode([
        "id" => $row['id'],
        "realName" => $row['RealName'],
        "username" => $row['Username']
    ]) . "); return false;'>";
echo "<img src='../assets/edit.png' alt='Edit' class='edit-icon'>";
echo "</a>";

echo "<a href='#' onclick='openDeleteModal(" . 
    json_encode([
        "id" => $row['id'],
        "username" => $row['Username']
    ]) . "); return false;'>";
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