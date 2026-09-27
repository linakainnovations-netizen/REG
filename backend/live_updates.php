<?php
/**
 * Live Updates helper — short posts from parish offices.
 * Goal: reduce long verbal Sunday announcements. Public feed + home widget read this.
 */

class LiveUpdates
{
    public static function offices(): array
    {
        return [
            'parish_priest' => "Parish Priest",
            'parish_council' => "Parish Council",
            'youth_office' => "Youth Office",
            'treasurer' => "Treasurer / Finance",
            'liturgy' => "Liturgy",
            'media' => "Media & Livestream",
            'lay_group' => "Lay Groups",
            'choir' => "Choirs",
            'scc' => "SCC / Zone",
        ];
    }

    public static function latest(PDO $pdo, int $limit = 10, string $audience = 'all'): array
    {
        try {
            $sql = "SELECT l.*, u.full_name AS author_name FROM live_updates l
                    LEFT JOIN users u ON l.author_id = u.id
                    WHERE l.status = 'published'
                      AND (l.expires_at IS NULL OR l.expires_at > NOW())
                      AND (l.audience = 'all' OR l.audience = ?)
                    ORDER BY l.is_pinned DESC, l.created_at DESC LIMIT " . intval($limit);
            $s = $pdo->prepare($sql);
            $s->execute([$audience]);
            return $s->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            return [];
        }
    }
}
