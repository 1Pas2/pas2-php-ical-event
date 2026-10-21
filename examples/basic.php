<?php
declare(strict_types=1);
namespace Pas2\IcalEvent;

require __DIR__ . "/../src/library.php";
$start = new \DateTimeImmutable("2026-01-01T10:00:00Z");
echo event(
    "demo@example.test",
    "Team meeting",
    $start,
    $start->modify("+1 hour"),
    $start,
);
