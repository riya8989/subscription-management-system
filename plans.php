<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$message = '';

// Handle "add new plan" form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['plan_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $interval    = $_POST['billing_interval'] ?? 'monthly';

    if ($name !== '' && $price > 0) {
        $stmt = $pdo->prepare(
            'INSERT INTO plans (plan_name, description, price, billing_interval) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $description, $price, $interval]);
        $message = 'Plan created successfully.';
    } else {
        $message = 'Plan name and a valid price are required.';
    }
}

// Fetch all plans with subscriber counts
$plans = $pdo->query("
    SELECT p.*, COUNT(s.id) AS subscriber_count
    FROM plans p
    LEFT JOIN subscriptions s ON s.plan_id = p.id AND s.status = 'active'
    GROUP BY p.id
    ORDER BY p.price ASC
")->fetchAll();

$activePage = 'plans';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="topbar">
  <div>
    <h1>Subscription plans</h1>
    <div class="sub">Manage what customers can subscribe to</div>
  </div>
</div>

<?php if ($message): ?>
  <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<section>
  <div class="section-head"><h2>Existing plans</h2></div>
  <div class="plans">
    <?php foreach ($plans as $plan): ?>
    <div class="plan">
      <div class="tag"><?= $plan['is_active'] ? 'ACTIVE' : 'DISABLED' ?></div>
      <h3><?= htmlspecialchars($plan['plan_name']) ?></h3>
      <div class="price">&#8377;<?= number_format($plan['price'], 0) ?><small> / <?= $plan['billing_interval'] ?></small></div>
      <p style="font-size:13px; color:#6B7280; margin:12px 0;"><?= htmlspecialchars($plan['description']) ?></p>
      <div class="subs-count"><?= (int)$plan['subscriber_count'] ?> active subscribers</div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<section>
  <div class="section-head"><h2>Add a new plan</h2></div>
  <form method="POST" action="plans.php" class="plan-form">
    <label for="plan_name">Plan name</label>
    <input type="text" id="plan_name" name="plan_name" required>

    <label for="description">Description</label>
    <input type="text" id="description" name="description">

    <label for="price">Price (&#8377;)</label>
    <input type="number" id="price" name="price" step="0.01" min="0" required>

    <label for="billing_interval">Billing interval</label>
    <select id="billing_interval" name="billing_interval">
      <option value="monthly">Monthly</option>
      <option value="yearly">Yearly</option>
    </select>

    <button type="submit" class="btn-primary">Create plan</button>
  </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
