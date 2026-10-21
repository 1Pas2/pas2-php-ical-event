<?php
declare(strict_types=1);
namespace Pas2\IcalEvent;

function text(string $value): string
{
    return str_replace(
        ["\\", "\r\n", "\r", "\n", ";", ","],
        ["\\\\", "\\n", "\\n", "\\n", "\\;", "\\,"],
        $value,
    );
}
function event(
    string $uid,
    string $summary,
    \DateTimeImmutable $start,
    \DateTimeImmutable $end,
    \DateTimeImmutable $stamp,
): string {
    if ($end <= $start) {
        throw new \InvalidArgumentException("End must follow start");
    }
    if ($uid === "" || preg_match('/[\r\n]/', $uid)) {
        throw new \InvalidArgumentException("Invalid UID");
    }
    $utc = new \DateTimeZone("UTC");
    $format = fn($date) => $date->setTimezone($utc)->format("Ymd\THis\Z");
    $lines = [
        "BEGIN:VCALENDAR",
        "VERSION:2.0",
        "PRODID:-//1Pas2//Pas2 Kit//EN",
        "BEGIN:VEVENT",
        "UID:" . text($uid),
        "DTSTAMP:" . $format($stamp),
        "DTSTART:" . $format($start),
        "DTEND:" . $format($end),
        "SUMMARY:" . text($summary),
        "END:VEVENT",
        "END:VCALENDAR",
    ];
    $folded = [];
    foreach ($lines as $line) {
        $current = "";
        $length = 0;
        foreach (
            preg_split("//u", $line, -1, PREG_SPLIT_NO_EMPTY) ?: []
            as $char
        ) {
            if ($length + strlen($char) > 75) {
                $folded[] = $current;
                $current = " ";
                $length = 1;
            }
            $current .= $char;
            $length += strlen($char);
        }
        $folded[] = $current;
    }
    return implode("\r\n", $folded) . "\r\n";
}
