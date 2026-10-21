<?php
declare(strict_types=1);
namespace Pas2\IcalEvent;

require __DIR__ . "/../src/library.php";
function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new \RuntimeException($message);
    }
}
function fails(callable $fn): void
{
    try {
        $fn();
    } catch (\Throwable) {
        return;
    }
    throw new \RuntimeException("Expected failure");
}
$a = new \DateTimeImmutable("2026-01-01T10:00:00Z");
$b = $a->modify("+1 hour");
$ics = event("demo@example.test", "Meet, team\nNext", $a, $b, $a);
check(str_contains($ics, "DTSTART:20260101T100000Z"), "UTC");
check(str_contains($ics, 'SUMMARY:Meet\, team\nNext'), "escaping");
foreach (
    explode("\r\n", event("x", str_repeat("é", 100), $a, $b, $a))
    as $line
) {
    check(strlen($line) <= 75, "folding");
}
fails(fn() => event("x", "bad", $a, $a, $a));
fails(fn() => event("bad\nUID", "x", $a, $b, $a));
echo "All checks passed\n";
