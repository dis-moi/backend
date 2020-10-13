# Notices stats for period

SET time_zone = "+2:00";

## List rating types

SELECT rating.type, COUNT(*) FROM rating GROUP BY rating.type;

## Notices rating stats for 1 month

SELECT n.id, n.message, n.contributor_id,
       (select COUNT(*) as badged FROM rating r WHERE r.notice_id = n.id AND r.type = 'badge' AND TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 90) as badged,
       (select COUNT(*) as displayed FROM rating r WHERE r.notice_id = n.id AND r.type = 'display' AND TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 90) as displayed,
       (select COUNT(*) as unfolded FROM rating r WHERE r.notice_id = n.id AND r.type = 'unfold' AND TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 90) as unfolded,
       (select COUNT(*) as outbound_clicked FROM rating r WHERE r.notice_id = n.id AND r.type = 'outbound-click' AND TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 90) as outbound_clicked,
       (select COUNT(*) as liked FROM rating r WHERE r.notice_id = n.id AND r.type = 'like' AND TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 90) as liked,
       (select COUNT(*) as disliked FROM rating r WHERE r.notice_id = n.id AND r.type = 'dislike' AND TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 90) as disliked,
       (select COUNT(*) as dismissed FROM rating r WHERE r.notice_id = n.id AND r.type = 'dismiss' AND TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 90) as dismissed
FROM notice n
ORDER BY displayed DESC;


### Filter test

select r.notice_id, r.type, r.context_timestamp FROM rating r WHERE TIMESTAMPDIFF(DAY, r.context_timestamp, NOW()) < 20 ORDER BY r.context_timestamp;