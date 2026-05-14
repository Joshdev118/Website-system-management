<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['item_id'])) {
    $action = $_POST['action'];
    $item_id = intval($_POST['item_id']);

    if ($action === 'delete') {
        $deleteStmt = $conn->prepare('DELETE FROM items WHERE id = ? AND user_id = ?');
        $deleteStmt->bind_param('ii', $item_id, $current_user_id);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows > 0) {
            $message = 'Item deleted successfully.';
        } else {
            $error = 'Unable to delete this item.';
        }
        $deleteStmt->close();
    }

    if ($action === 'handover') {
        $handoverStmt = $conn->prepare("UPDATE items SET status = 'Sold' WHERE id = ? AND user_id = ? AND status != 'Sold'");
        $handoverStmt->bind_param('ii', $item_id, $current_user_id);
        $handoverStmt->execute();

        if ($handoverStmt->affected_rows > 0) {
            $message = 'Item marked as Sold successfully.';
        } else {
            $error = 'Unable to confirm handover for this item.';
        }
        $handoverStmt->close();
    }

    header('Location: my_items.php' . ($message ? '?message=' . urlencode($message) : ($error ? '?error=' . urlencode($error) : '')));
    exit();
}

if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

$query = 'SELECT id, item_name, price, image_path, status, description, phone_number, created_at FROM items WHERE user_id = ? ORDER BY created_at DESC';
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);

function safeImagePath($path) {
    $path = trim($path);
    if (strpos($path, 'uploads/') !== 0) {
        $path = 'uploads/' . $path;
    }
    return htmlspecialchars($path);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Items | Inventory</title>
    <style>
        :root {
            --bg-light: #f4f7fb;
            --surface-light: #ffffff;
            --surface-muted-light: #f1f5f9;
            --text-dark: #102a43;
            --text-muted: #52606d;
            --accent-blue: #2563eb;
            --accent-gold: #f59e0b;
            --border-light: #d9e2ec;
            --shadow-light: rgba(15, 23, 42, 0.08);
            --bg-dark: #0f172a;
            --surface-dark: #111827;
            --surface-muted-dark: #1f2937;
            --text-light: #e2e8f0;
            --text-muted-light: #94a3b8;
            --accent-blue-dark: #60a5fa;
            --accent-gold-dark: #fbbf24;
            --border-dark: rgba(148, 163, 184, 0.24);
            --shadow-dark: rgba(0, 0, 0, 0.45);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
        }

        body.dark-theme {
            background: var(--bg-dark);
            color: var(--text-light);
        }

        .page-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .header-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        .page-title {
            margin: 0;
            font-size: 2rem;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: var(--text-muted);
            margin: 8px 0 0;
            font-size: 0.95rem;
        }

        body.dark-theme .page-subtitle {
            color: var(--text-muted-light);
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pill {
            background: var(--surface-light);
            color: var(--text-dark);
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid var(--border-light);
            box-shadow: 0 10px 30px var(--shadow-light);
            font-weight: 600;
        }

        body.dark-theme .pill {
            background: var(--surface-dark);
            color: var(--text-light);
            border-color: var(--border-dark);
            box-shadow: 0 12px 34px var(--shadow-dark);
        }

        .theme-toggle {
            border: none;
            cursor: pointer;
            padding: 12px 18px;
            font-weight: 700;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-gold));
            color: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.18);
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
        }

        .panel {
            background: var(--surface-light);
            border: 1px solid var(--border-light);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 48px var(--shadow-light);
        }

        body.dark-theme .panel {
            background: var(--surface-dark);
            border-color: var(--border-dark);
            box-shadow: 0 16px 48px var(--shadow-dark);
        }

        .alert {
            margin-bottom: 20px;
            padding: 14px 18px;
            border-radius: 14px;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .alert-success {
            color: #1d4ed8;
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .alert-error {
            color: #b91c1c;
            background: #fee2e2;
            border-color: #fecaca;
        }

        body.dark-theme .alert-success {
            color: #bfdbfe;
            background: rgba(96, 165, 250, 0.14);
            border-color: rgba(96, 165, 250, 0.28);
        }

        body.dark-theme .alert-error {
            color: #fecaca;
            background: rgba(248, 113, 113, 0.14);
            border-color: rgba(248, 113, 113, 0.28);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 18px;
            min-width: 100%;
            background: var(--surface-light);
            border: 1px solid var(--border-light);
        }

        body.dark-theme table {
            background: var(--surface-dark);
            border-color: var(--border-dark);
        }

        thead {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(245, 158, 11, 0.12));
        }

        th {
            text-align: left;
            padding: 16px 18px;
            color: var(--text-dark);
            font-size: 0.84rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-weight: 700;
        }

        body.dark-theme th {
            color: var(--text-light);
        }

        tbody td {
            padding: 18px;
            border-top: 1px solid var(--border-light);
            vertical-align: middle;
            color: var(--text-dark);
        }

        body.dark-theme tbody td {
            color: var(--text-light);
            border-color: var(--border-dark);
        }

        tbody tr {
            transition: transform 0.25s ease, background 0.25s ease;
        }

        tbody tr:hover {
            transform: translateY(-2px);
            background: rgba(37, 99, 235, 0.04);
        }

        .item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid var(--border-light);
        }

        body.dark-theme .item-img {
            border-color: var(--border-dark);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .status-available { background: #dbeafe; color: #1d4ed8; }
        .status-reserved { background: #ffedd5; color: #c2410c; }
        .status-sold { background: #dcfce7; color: #166534; }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .action-form {
            display: inline-flex;
            margin: 0;
        }

        .btn-action {
            border: none;
            border-radius: 14px;
            padding: 10px 16px;
            cursor: pointer;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        .btn-handover {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
            box-shadow: 0 12px 28px rgba(16, 185, 129, 0.18);
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            box-shadow: 0 12px 28px rgba(239, 68, 68, 0.18);
        }

        .btn-disabled {
            background: rgba(148, 163, 184, 0.16);
            color: var(--text-muted);
            cursor: default;
            box-shadow: none;
        }

        .product-row {
            cursor: pointer;
        }

        .product-row:hover {
            background: rgba(37, 99, 235, 0.04);
        }

        .review-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 2000;
        }

        .review-modal.open {
            display: flex;
        }

        .review-window {
            width: min(760px, 100%);
            background: var(--surface-light);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.2);
            position: relative;
        }

        body.dark-theme .review-window {
            background: var(--surface-dark);
        }

        .modal-close {
            position: absolute;
            top: 18px;
            right: 18px;
            border: none;
            background: rgba(15, 23, 42, 0.08);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.25rem;
            cursor: pointer;
            color: var(--text-dark);
        }

        body.dark-theme .modal-close {
            background: rgba(226, 232, 240, 0.1);
            color: var(--text-light);
        }

        .modal-grid {
            display: grid;
            grid-template-columns: 1fr 1.25fr;
            gap: 24px;
            align-items: start;
        }

        .modal-grid img {
            width: 100%;
            border-radius: 20px;
            object-fit: cover;
            max-height: 400px;
            border: 1px solid var(--border-light);
        }

        .modal-grid h2 {
            margin: 0 0 12px;
            font-size: 1.75rem;
        }

        .modal-grid p {
            margin: 0 0 14px;
            line-height: 1.7;
            color: var(--text-muted);
        }

        .modal-meta {
            display: grid;
            gap: 12px;
            margin-top: 10px;
        }

        .modal-meta span {
            display: inline-flex;
            align-items: center;
            padding: 10px 14px;
            border-radius: 14px;
            background: var(--surface-muted-light);
            color: var(--text-dark);
            font-weight: 600;
        }

        body.dark-theme .modal-meta span {
            background: rgba(226, 232, 240, 0.06);
            color: var(--text-light);
        }

        .modal-actions {
            margin-top: 22px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .modal-actions .btn-action {
            min-width: 150px;
        }

        .text-muted {
            color: var(--text-muted);
        }

        body.dark-theme .text-muted {
            color: var(--text-muted-light);
        }

        .footer-link {
            display: inline-block;
            margin-top: 24px;
            color: var(--accent-blue);
            font-weight: 600;
            text-decoration: none;
        }

        body.dark-theme .footer-link {
            color: var(--accent-blue-dark);
        }

        @media (max-width: 900px) {
            .header-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .toolbar {
                width: 100%;
                justify-content: space-between;
            }

            th, td {
                padding: 14px 12px;
            }

            .item-img {
                width: 70px;
                height: 70px;
            }
        }

        @media (max-width: 680px) {
            .page-shell {
                padding: 18px 16px;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 18px;
                border-radius: 20px;
                overflow: hidden;
                border: 1px solid var(--border-light);
                background: var(--surface-light);
            }

            body.dark-theme tr {
                background: var(--surface-dark);
                border-color: var(--border-dark);
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
            }

            td::before {
                position: absolute;
                top: 18px;
                left: 18px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: 700;
                color: var(--text-muted);
            }

            td:nth-of-type(1)::before { content: 'Preview'; }
            td:nth-of-type(2)::before { content: 'Item Name'; }
            td:nth-of-type(3)::before { content: 'Price'; }
            td:nth-of-type(4)::before { content: 'Status'; }
            td:nth-of-type(5)::before { content: 'Date Added'; }
            td:nth-of-type(6)::before { content: 'Actions'; }

            td:last-child {
                padding-bottom: 18px;
            }
        }
    </style>
</head>
<body class="light-theme">
    <div class="page-shell">
        <div class="header-row">
            <div>
                <h1 class="page-title">My Listed Assets</h1>
                <p class="page-subtitle">Manage your posted inventory, confirm handovers, and remove listings whenever needed.</p>
            </div>
            <div class="toolbar">
                <span class="pill">Logged in as <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                <button type="button" class="theme-toggle" id="theme-toggle">Switch to Dark Mode</button>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($items) > 0): ?>
                        <?php foreach ($items as $row): ?>
                            <?php
                                $status = $row['status'];
                                $statusClass = 'status-available';
                                if ($status === 'Reserved') {
                                    $statusClass = 'status-reserved';
                                } elseif ($status === 'Sold') {
                                    $statusClass = 'status-sold';
                                }
                            ?>
                            <tr class="product-row" data-item-id="<?php echo intval($row['id']); ?>">
                                <td><img src="<?php echo safeImagePath($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>" class="item-img"></td>
                                <td><strong><?php echo htmlspecialchars($row['item_name']); ?></strong></td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <div class="action-group">
                                        <?php if ($status !== 'Sold'): ?>
                                            <form method="post" class="action-form" onsubmit="return confirm('Mark this item as Sold?');">
                                                <input type="hidden" name="item_id" value="<?php echo intval($row['id']); ?>">
                                                <input type="hidden" name="action" value="handover">
                                                <button type="submit" class="btn-action btn-handover">Confirm Handover</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Handed over</span>
                                        <?php endif; ?>

                                        <form method="post" class="action-form" onsubmit="return confirm('Delete this item permanently?');">
                                            <input type="hidden" name="item_id" value="<?php echo intval($row['id']); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-action btn-delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 40px 18px; color: var(--text-muted);">
                                No posted items found. <a class="footer-link" href="upload.php">Upload a new asset</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="review-modal" id="review-modal" aria-hidden="true">
            <div class="review-window" role="dialog" aria-modal="true" aria-labelledby="review-title">
                <button type="button" class="modal-close" id="modal-close" aria-label="Close review">×</button>
                <div class="modal-grid">
                    <img src="" alt="Item image" id="modal-image">
                    <div>
                        <h2 id="review-title"></h2>
                        <p id="review-description"></p>
                        <div class="modal-meta">
                            <span id="review-price"></span>
                            <span id="review-status"></span>
                            <span id="review-date"></span>
                            <span id="review-phone"></span>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-action btn-handover" id="modal-handover">Confirm Handover</button>
                            <button type="button" class="btn-action btn-delete" id="modal-delete">Delete Item</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="index.php" class="footer-link">← Back to Main Dashboard</a>
    </div>

    <script>
        var itemDetails = <?php echo json_encode(array_column($items, null, 'id'), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
        var themeToggle = document.getElementById('theme-toggle');
        var body = document.body;
        var savedTheme = localStorage.getItem('inventoryTheme') || 'light';

        function setTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-theme');
                body.classList.remove('light-theme');
                themeToggle.textContent = 'Switch to Light Mode';
                localStorage.setItem('inventoryTheme', 'dark');
            } else {
                body.classList.add('light-theme');
                body.classList.remove('dark-theme');
                themeToggle.textContent = 'Switch to Dark Mode';
                localStorage.setItem('inventoryTheme', 'light');
            }
        }

        themeToggle.addEventListener('click', function() {
            if (body.classList.contains('dark-theme')) {
                setTheme('light');
            } else {
                setTheme('dark');
            }
        });

        function openReviewModal(itemId) {
            var item = itemDetails[itemId];
            if (!item) return;
            document.getElementById('review-image').src = item.image_path.indexOf('uploads/') === 0 ? item.image_path : 'uploads/' + item.image_path;
            document.getElementById('review-image').alt = item.item_name;
            document.getElementById('review-title').textContent = item.item_name;
            document.getElementById('review-description').textContent = item.description || 'No description provided.';
            document.getElementById('review-price').textContent = 'Price: $' + parseFloat(item.price).toFixed(2);
            document.getElementById('review-status').textContent = 'Status: ' + item.status;
            document.getElementById('review-date').textContent = 'Posted: ' + new Date(item.created_at).toLocaleDateString();
            document.getElementById('review-phone').textContent = 'Contact: ' + item.phone_number;
            document.getElementById('review-modal').classList.add('open');
            document.getElementById('review-modal').setAttribute('aria-hidden', 'false');
            document.getElementById('modal-handover').style.display = item.status === 'Sold' ? 'none' : 'inline-flex';
            document.getElementById('modal-delete').setAttribute('data-item-id', itemId);
        }

        function closeReviewModal() {
            document.getElementById('review-modal').classList.remove('open');
            document.getElementById('review-modal').setAttribute('aria-hidden', 'true');
        }

        document.querySelectorAll('.product-row').forEach(function(row) {
            row.addEventListener('click', function(event) {
                if (event.target.closest('.action-form')) {
                    return;
                }
                openReviewModal(this.dataset.itemId);
            });
        });

        document.getElementById('modal-close').addEventListener('click', closeReviewModal);
        document.getElementById('review-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeReviewModal();
            }
        });

        document.getElementById('modal-delete').addEventListener('click', function() {
            var itemId = this.getAttribute('data-item-id');
            if (!itemId) return;
            if (confirm('Delete this item permanently?')) {
                var form = document.createElement('form');
                form.method = 'post';
                form.style.display = 'none';
                form.innerHTML = '<input type="hidden" name="item_id" value="' + itemId + '">' +
                                 '<input type="hidden" name="action" value="delete">';
                document.body.appendChild(form);
                form.submit();
            }
        });

        document.getElementById('modal-handover').addEventListener('click', function() {
            var row = document.querySelector('.product-row[data-item-id="' + document.getElementById('modal-delete').getAttribute('data-item-id') + '"]');
            if (!row) return;
            var itemId = row.dataset.itemId;
            if (confirm('Mark this item as Sold?')) {
                var form = document.createElement('form');
                form.method = 'post';
                form.style.display = 'none';
                form.innerHTML = '<input type="hidden" name="item_id" value="' + itemId + '">' +
                                 '<input type="hidden" name="action" value="handover">';
                document.body.appendChild(form);
                form.submit();
            }
        });

        setTheme(savedTheme);
    </script>
</body>
</html>