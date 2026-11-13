<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// --- Stat: Monthly recurring revenue (sum of active subscriptions' plan price) ---
$mrr = $pdo->query("
    SELECT COALESCE(SUM(p.price), 0) AS total
    FROM subscriptions s
    JOIN plans p ON p.id = s.plan_id
    WHERE s.status = 'active'
")->fetch()['total'];

// --- Stat: Active subscriptions count ---
$activeCount = $pdo->query("SELECT COUNT(*) AS c FROM subscriptions WHERE status = 'active'")->fetch()['c'];

// --- Stat: Past due count ---
$pastDueCount = $pdo->query("SELECT COUNT(*) AS c FROM subscriptions WHERE status = 'past_due'")->fetch()['c'];

// --- Recent subscribers (join customers + plans + subscriptions) ---
$recentSubs = $pdo->query("
    SELECT c.full_name, c.email, p.plan_name, s.status, s.next_billing_date, p.price
    FROM subscriptions s
    JOIN customers c ON c.id = s.customer_id
    JOIN plans p ON p.id = s.plan_id
    ORDER BY s.created_at DESC
    LIMIT 5
")->fetchAll();

// --- Latest invoices ---
$recentInvoices = $pdo->query("
    SELECT i.invoice_number, c.full_name, i.issued_date, i.status, i.amount
    FROM invoices i
    JOIN subscriptions s ON s.id = i.subscription_id
    JOIN customers c ON c.id = s.customer_id
    ORDER BY i.issued_date DESC
    LIMIT 5
")->fetchAll();

$activePage = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="topbar">
  <div>
    <h1>Billing overview</h1>
    <div class="sub"><?= date('l, j F Y') ?></div>
  </div>
</div>

<div class="stats">
  <div class="stat">
    <div class="label">Monthly recurring revenue</div>
    <div class="value">&#8377;<?= number_format($mrr, 0) ?></div>
  </div>
  <div class="stat">
    <div class="label">Active subscriptions</div>
    <div class="value"><?= (int)$activeCount ?></div>
  </div>
  <div class="stat">
    <div class="label">Past due</div>
    <div class="value"><?= (int)$pastDueCount ?></div>
  </div>
</div>

<section>
  <div class="section-head">
    <h2>Recent subscribers</h2>
    <a class="link-action" href="subscribers.php">View all &rarr;</a>
  </div>
  <table>
    <thead>
      <tr><th>Customer</th><th>Plan</th><th>Status</th><th>Next billing</th><th>Amount</th></tr>
    </thead>
    <tbody>
      <?php foreach ($recentSubs as $row): ?>
      <tr>
        <td>
          <div class="cust"><?= htmlspecialchars($row['full_name']) ?></div>
          <div class="cust-email"><?= htmlspecialchars($row['email']) ?></div>
        </td>
        <td><?= htmlspecialchars($row['plan_name']) ?></td>
        <td><span class="badge <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$row['status']))) ?></span></td>
        <td><?= htmlspecialchars($row['next_billing_date']) ?></td>
        <td class="amount">&#8377;<?= number_format($row['price'], 0) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <div class="section-head">
    <h2>Latest invoices</h2>
    <a class="link-action" href="invoices.php">View all &rarr;</a>
  </div>
  <table>
    <thead>
      <tr><th>Invoice</th><th>Customer</th><th>Issued</th><th>Status</th><th>Total</th></tr>
    </thead>
    <tbody>
      <?php foreach ($recentInvoices as $row): ?>
      <tr>
        <td class="invoice-num"><?= htmlspecialchars($row['invoice_number']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['issued_date']) ?></td>
        <td><span class="badge <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars(ucfirst($row['status'])) ?></span></td>
        <td class="amount">&#8377;<?= number_format($row['amount'], 0) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
