<?php

namespace App\Services;

use App\Config\Database;
use PDO;

/**
 * Subscription tier from landing / registration + enforcement limits.
 * Paystack amount and DB premium_price are unchanged; this only gates features by plan.
 */
final class CompanyPlan
{
    public const STARTER = 'starter';
    public const PROFESSIONAL = 'professional';
    public const ENTERPRISE = 'enterprise';

    /**
     * @return list<string>
     */
    public static function allTiers(): array
    {
        return [self::STARTER, self::PROFESSIONAL, self::ENTERPRISE];
    }

    public static function normalize(?string $plan): string
    {
        $p = strtolower(trim((string) $plan));
        return in_array($p, self::allTiers(), true) ? $p : self::STARTER;
    }

    /** @return int|null null = unlimited */
    public static function maxSuppliers(string $plan): ?int
    {
        return $plan === self::STARTER ? 50 : null;
    }

    /** @return int|null null = unlimited */
    public static function maxUsers(string $plan): ?int
    {
        return match ($plan) {
            self::STARTER => 1,
            self::PROFESSIONAL => 5,
            default => null,
        };
    }

    /** @return int|null null = unlimited */
    public static function maxCriteria(string $plan): ?int
    {
        return $plan === self::STARTER ? 5 : null;
    }

    public static function canExport(string $plan): bool
    {
        return $plan !== self::STARTER;
    }

    public static function label(string $plan): string
    {
        return match (self::normalize($plan)) {
            self::STARTER => 'Starter',
            self::PROFESSIONAL => 'Professional',
            self::ENTERPRISE => 'Enterprise',
            default => 'Starter',
        };
    }

    public static function syncSessionFromDb(?PDO $conn, int $companyId): void
    {
        if ($conn === null || $companyId < 1) {
            return;
        }
        $stmt = $conn->prepare('SELECT plan FROM companies WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $companyId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['company_plan'] = self::normalize($row['plan'] ?? self::PROFESSIONAL);
    }

    public static function current(): string
    {
        if (empty($_SESSION['company_id'])) {
            return self::ENTERPRISE;
        }
        if (!empty($_SESSION['company_plan'])) {
            return self::normalize($_SESSION['company_plan']);
        }
        $db = new Database();
        self::syncSessionFromDb($db->getConnection(), (int) $_SESSION['company_id']);
        return self::normalize($_SESSION['company_plan'] ?? self::PROFESSIONAL);
    }

    public static function supplierCount(PDO $conn, int $companyId): int
    {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM suppliers WHERE company_id = :cid');
        $stmt->execute(['cid' => $companyId]);
        return (int) $stmt->fetchColumn();
    }

    public static function userCount(PDO $conn, int $companyId): int
    {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE company_id = :cid');
        $stmt->execute(['cid' => $companyId]);
        return (int) $stmt->fetchColumn();
    }

    public static function criteriaCount(PDO $conn, int $companyId): int
    {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM criteria WHERE company_id = :cid');
        $stmt->execute(['cid' => $companyId]);
        return (int) $stmt->fetchColumn();
    }

    /** After successful payment: Starter → Professional (Enterprise unchanged). */
    public static function applyPaidUpgrade(PDO $conn, int $companyId): void
    {
        $stmt = $conn->prepare('SELECT plan FROM companies WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $companyId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $plan = self::normalize($row['plan'] ?? self::STARTER);
        if ($plan === self::STARTER) {
            $u = $conn->prepare("UPDATE companies SET plan = 'professional' WHERE id = :id");
            $u->execute(['id' => $companyId]);
            if (!empty($_SESSION['company_id']) && (int) $_SESSION['company_id'] === $companyId) {
                $_SESSION['company_plan'] = self::PROFESSIONAL;
            }
        }
    }
}
