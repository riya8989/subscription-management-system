<?php
// Expects $activePage to be set by the including page (e.g. 'dashboard', 'plans')
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>LedgerFlow — Billing Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="sidebar">
  <div class="brand">Ledger<span>Flow</span></div>

  <a class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">
    <span class="nav-icon">&#9638;</span> Dashboard
  </a>
  <a class="nav-item <?= $activePage === 'subscribers' ? 'active' : '' ?>" href="subscribers.php">
    <span class="nav-icon">&#9680;</span> Subscribers
  </a>
  <a class="nav-item <?= $activePage === 'plans' ? 'active' : '' ?>" href="plans.php">
    <span class="nav-icon">&#9707;</span> Plans
  </a>
  <a class="nav-item <?= $activePage === 'invoices' ? 'active' : '' ?>" href="invoices.php">
    <span class="nav-icon">&#9639;</span> Invoices
  </a>

  <div class="sidebar-foot">
    Logged in as <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?><br>
    <a href="../auth/logout.php" style="color:inherit;">Log out</a>
  </div>
</div>
<div class="main">
