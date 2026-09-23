<?php
$conn = mysqli_connect("localhost","root","","librarydb");

if(!$conn){
    die("Database Connection Failed");
}

// සාමාන්‍යයෙන් users හෝ members ටේබල් එකෙන් දත්ත ගන්නා Query එක (ඔයාගේ table එකේ නමට අනුව වෙනස් කරගන්න)
$sql = "SELECT * FROM users WHERE role='user' OR role='member' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$total_members = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members</title>
    <link rel="stylesheet" href="members.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="page-container">

    <div class="form-card table-card">

        <div class="form-header table-header-bar">
            <div class="header-left">
                <h1>Manage Members</h1>
                <p>View and manage all registered library members.</p>
            </div>
            <a href="admin_dashboard.php" class="btn-exit dashboard-btn">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
        </div>

        <div class="stat-box">
            <span class="stat-title">Total Members</span>
            <span class="stat-number"><?php echo $total_members; ?></span>
        </div>

        <div class="search-container">
            <div class="input-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search members by name, ID, or email...">
            </div>
        </div>

        <div class="table-responsive">
            <table id="memberTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Member ID</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th style="text-align: center; width: 90px;">Role</th>
                        <th style="text-align: center; width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td><strong><?php echo $i++; ?></strong></td>
                        <td class="member-id-cell"><?php echo $row['member_id']; ?></td>
                        <td class="member-name-cell"><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td style="text-align: center;">
                            <span class="role-badge"><?php echo ucfirst($row['role']); ?></span>
                        </td>
                        <td style="text-align: center;">
                            <div class="action-buttons">
                                
                                <a class="btn-action delete-btn" href="delete_member.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this member?')">
                                    <i class="fa-solid fa-user-minus"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
// Live Filter JS එක මෙතනටත් දැම්මා
const searchInput = document.getElementById("searchInput");
searchInput.addEventListener("keyup", function(){
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#memberTable tbody tr");

    rows.forEach((row) => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

</body>
</html>