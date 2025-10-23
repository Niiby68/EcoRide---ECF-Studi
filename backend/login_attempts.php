<?php
declare(strict_types=1);



//
//  Gestion des tentatives de connexion par IP
//  Chemin : /backend/login_attempts.php
//



//
// Configuration
//
define('MAX_ATTEMPTS', 5);
define('LOCKOUT_MINUTES', 15);
define('RESET_MINUTES', 30);



//
// Récupération de l'IP du client
//
function get_client_ip(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}



//
// Conversion IP -> binaire
//
function ip_to_bin(string $ip) {
    $bin = @inet_pton($ip);
    return $bin === false ? null : $bin;
}



//
// Nettoyage des vieilles tentatives
//
function cleanup_old_attempts(PDO $pdo): void {
    $threshold = (new DateTime())->modify('-' . RESET_MINUTES . ' minutes')->format('Y-m-d H:i:s');
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE last_attempt < :th");
    $stmt->execute([':th' => $threshold]);
}



//
// Vérifie le statut de l'IP
//
function is_ip_blocked(PDO $pdo, string $ip): array {
    cleanup_old_attempts($pdo);

    $bin = ip_to_bin($ip);
    if ($bin === null) return ['blocked' => false, 'remaining_seconds' => 0];

    $stmt = $pdo->prepare("SELECT id, attempts, last_attempt FROM login_attempts WHERE ip = :ip LIMIT 1");
    $stmt->execute([':ip' => $bin]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return ['blocked' => false, 'remaining_seconds' => 0];

    $attempts = (int)$row['attempts'];
    $last_attempt_dt = new DateTime($row['last_attempt']);
    $now = new DateTime();

    if ($attempts >= MAX_ATTEMPTS) {
        $blocked_until = clone $last_attempt_dt;
        $blocked_until->modify('+' . LOCKOUT_MINUTES . ' minutes');
        if ($blocked_until > $now) {
            $remaining = $blocked_until->getTimestamp() - $now->getTimestamp();
            return ['blocked' => true, 'remaining_seconds' => $remaining];
        } else {
            $del = $pdo->prepare("DELETE FROM login_attempts WHERE id = :id");
            $del->execute([':id' => $row['id']]);
            return ['blocked' => false, 'remaining_seconds' => 0];
        }
    }

    return ['blocked' => false, 'remaining_seconds' => 0];
}



//
// Enregistrement d'une tentative échouée
//
function record_failed_attempt(PDO $pdo, string $ip): void {
    $bin = ip_to_bin($ip);
    if ($bin === null) return;

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT id, attempts FROM login_attempts WHERE ip = :ip FOR UPDATE");
        $stmt->execute([':ip' => $bin]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $now = (new DateTime())->format('Y-m-d H:i:s');

        if ($row) {
            $newAttempts = min(255, (int)$row['attempts'] + 1);
            $upd = $pdo->prepare("UPDATE login_attempts SET attempts = :attempts, last_attempt = :last WHERE id = :id");
            $upd->execute([
                ':attempts' => $newAttempts,
                ':last' => $now,
                ':id' => $row['id']
            ]);
        } else {
            $ins = $pdo->prepare("INSERT INTO login_attempts (ip, attempts, last_attempt) VALUES (:ip, 1, :last)");
            $ins->execute([':ip' => $bin, ':last' => $now]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}



//
// Réinitialisation des tentatives
//
function reset_attempts(PDO $pdo, string $ip): void {
    $bin = ip_to_bin($ip);
    if ($bin === null) return;

    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip = :ip");
    $stmt->execute([':ip' => $bin]);
}
?>
