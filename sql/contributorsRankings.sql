# Contributors ranking

## By numbers of followers
SELECT c.name, (SELECT COUNT(*) as followers
    FROM subscription s
    WHERE s.contributor_id = c.id AND DAY(TIMEDIFF(NOW(), s.updated)) < 14
    ) as followers, total_subscriptions
FROM contributor c
ORDER BY followers DESC;


## By numbers of display notices
SELECT c.name, SUM(
    (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = 'display')
) as display
FROM contributor c
LEFT JOIN (notice n) ON (c.id = n.contributor_id)
GROUP BY c.id
ORDER BY display DESC;

## By numbers of unfold notices
SELECT c.name, SUM(
    (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = 'unfold')
) as unfolds
FROM contributor c
LEFT JOIN (notice n) ON (c.id = n.contributor_id)
GROUP BY c.id
ORDER BY unfolds DESC;


## By pertinence : unfold / display
SELECT name, display, unfolds, unfolds / display as ratio FROM (
                SELECT c.name,
                       SUM(
                               (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = 'unfold')
                           ) as unfolds,
                       SUM(
                               (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = 'display')
                           ) as display
                FROM contributor c
                         LEFT JOIN (notice n) ON (c.id = n.contributor_id)
                GROUP BY c.id
            ) sums
ORDER BY ratio DESC;


## By pertinence : unfold / display with over 10 displays
SELECT name, display, unfolds, unfolds / display * 100 as ratio FROM (
                SELECT c.name,
                       SUM(
                               (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = 'unfold')
                           ) as unfolds,
                       SUM(
                               (SELECT COUNT(*) FROM rating r WHERE r.notice_id = n.id AND r.type = 'display')
                           ) as display
                FROM contributor c
                         LEFT JOIN (notice n) ON (c.id = n.contributor_id)
                GROUP BY c.id
            ) sums
WHERE display >= 10
ORDER BY ratio DESC