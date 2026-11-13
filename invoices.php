<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$invoices = $pdo->query("
    SELECT i.invoice_number, c.full_name, i.issued_date, i.due_date, i.status, i.amount
    FROM invoices i
    JOIN subscriptions s ON s.id = i.subscription_id
    JOIN customers c ON c.id = s.customer_id
    ORDER BY i.issued_date DESC
")->fetchAll();

$activePage = 'invoices';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="topbar">
  <div>
    <h1>Invoices</h1>
    <div class="sub"><?= count($invoices) ?> invoices generated so far</div>
  </div>
</div>

<section>
  <table>
    <thead>
      <tr><th>Invoice</th><th>Customer</th><th>Issued</th><th>Due</th><th>Status</th><th>Amount</th></tr>
    </thead>
    <tbody>
      <?php foreach ($invoices as $row): ?>
      <tr>
        <td class="invoice-num"><?= htmlspecialchars($row['invoice_number']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['issued_date']) ?></td>
        <td><?= htmlspecialchars($row['due_date']) ?></td>
        <td><span class="badge <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars(ucfirst($row['status'])) ?></span></td>
        <td class="amount">&#8377;<?= number_format($row['amount'], 0) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
