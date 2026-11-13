<?php
/**
 * Run this daily via cron, e.g.:
 * 0 1 * * * /usr/bin/php /path/to/billing_system/cron/generate_invoices.php
 *
 * 1. Finds active subscriptions whose next_billing_date is today.
 * 2. Creates an invoice for each.
 * 3. Advances next_billing_date by one billing cycle.
 * 4. Marks unpaid invoices past their due date as 'overdue',
 *    and flips the related subscription to 'past_due'.
 */
require_once __DIR__ . '/../config/db.php';

$today = date('Y-m-d');

// --- 1 & 2 & 3: generate invoices for subscriptions due today ---
$due = $pdo->prepare("
    SELECT s.id AS subscription_id, p.price, p.billing_interval
    FROM subscriptions s
    JOIN plans p ON p.id = s.plan_id
    WHERE s.status = 'active' AND s.next_billing_date = ?
");
$due->execute([$today]);

foreach ($due->fetchAll() as $sub) {
    $invoiceNumber = 'INV-' . strtoupper(uniqid());
    $dueDate = date('Y-m-d', strtotime('+7 days'));

    $insert = $pdo->prepare("
        INSERT INTO invoices (invoice_number, subscription_id, amount, status, issued_date, due_date)
        VALUES (?, ?, ?, 'unpaid', ?, ?)
    ");
    $insert->execute([$invoiceNumber, $sub['subscription_id'], $sub['price'], $today, $dueDate]);

    $nextDate = $sub['billing_interval'] === 'yearly'
        ? date('Y-m-d', strtotime('+1 year'))
        : date('Y-m-d', strtotime('+1 month'));

    $update = $pdo->prepare("UPDATE subscriptions SET next_billing_date = ? WHERE id = ?");
    $update->execute([$nextDate, $sub['subscription_id']]);

    echo "Generated {$invoiceNumber} for subscription #{$sub['subscription_id']}\n";
}

// --- 4: mark overdue invoices and flip subscriptions to past_due ---
$overdue = $pdo->prepare("
    UPDATE invoices SET status = 'overdue'
    WHERE status = 'unpaid' AND due_date < ?
");
$overdue->execute([$today]);

$pastDue = $pdo->prepare("
    UPDATE subscriptions s
    JOIN invoices i ON i.subscription_id = s.id
    SET s.status = 'past_due'
    WHERE i.status = 'overdue' AND s.status = 'active'
");
$pastDue->execute();

echo "Done.\n";
