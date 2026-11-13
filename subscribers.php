<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$subscribers = $pdo->query("
    SELECT c.full_name, c.email, c.company_name, p.plan_name, p.price,
           s.status, s.next_billing_date
    FROM subscriptions s
    JOIN customers c ON c.id = s.customer_id
    JOIN plans p ON p.id = s.plan_id
    ORDER BY s.created_at DESC
")->fetchAll();

$activePage = 'subscribers';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="topbar">
  <div>
    <h1>Subscribers</h1>
    <div class="sub"><?= count($subscribers) ?> total subscriptions</div>
  </div>
</div>

<section>
  <table>
    <thead>
      <tr><th>Customer</th><th>Company</th><th>Plan</th><th>Status</th><th>Next billing</th><th>Amount</th></tr>
    </thead>
    <tbody>
      <?php foreach ($subscribers as $row): ?>
      <tr>
        <td>
          <div class="cust"><?= htmlspecialchars($row['full_name']) ?></div>
          <div class="cust-email"><?= htmlspecialchars($row['email']) ?></div>
        </td>
        <td><?= htmlspecialchars($row['company_name']) ?></td>
        <td><?= htmlspecialchars($row['plan_name']) ?></td>
        <td><span class="badge <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$row['status']))) ?></span></td>
        <td><?= htmlspecialchars($row['next_billing_date']) ?></td>
        <td class="amount">&#8377;<?= number_format($row['price'], 0) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
