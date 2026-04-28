-- Players
INSERT INTO PLAYERS (player_id, username, level, server, creation_date) VALUES
(1, 'Faker',    899, 'KR', '2013-04-06 00:00:00'),
(2, 'Oner',   543, 'NA', '2016-09-01 00:00:00'),
(3, 'Gumayusi', 721, 'EU', '2011-05-01 00:00:00'),
(4, 'Zeus', 612, 'NA', '2016-01-01 00:00:00'),
(5, 'Keria', 389, 'KR', '2013-01-01 00:00:00');

-- Characters (League of Legends)
INSERT INTO CHARACTERS (character_id, name, role, difficulty) VALUES
(1, 'Ryze',       'Mid',     10),
(2, 'Jax',        'Top',      5),
(3, 'Alistar',    'Support',  4),
(4, 'Master Yi',  'Jungle',   2),
(5, 'Ashe',       'Bottom',   3);

-- Matches
INSERT INTO MATCHES (match_id, date, duration, map, game_mode) VALUES
(1,  '2026-04-15 10:00:00', 28, 'Summoner''s Rift', 'Ranked'),
(2,  '2026-04-15 12:30:00', 35, 'Summoner''s Rift', 'Ranked'),
(3,  '2026-04-16 09:00:00', 22, 'Winter Valley',    'Normal'),
(4,  '2026-04-16 14:00:00', 41, 'Summoner''s Rift', 'Ranked'),
(5,  '2026-04-17 11:00:00', 19, 'Desert Dunes',     'ARAM'),
(6,  '2026-04-17 16:00:00', 33, 'Summoner''s Rift', 'Normal'),
(7,  '2026-04-18 10:30:00', 27, 'Winter Valley',    'Ranked'),
(8,  '2026-04-19 13:00:00', 38, 'Summoner''s Rift', 'Ranked'),
(9,  '2026-04-20 09:30:00', 24, 'Desert Dunes',     'ARAM'),
(10, '2026-04-21 17:00:00', 31, 'Summoner''s Rift', 'Ranked');

-- Match Stats
-- Match 1:
INSERT INTO MATCH_STATS (match_id, player_id, character_id, win_loss, kills, deaths, assists) VALUES
(1, 1, 1, 'Win',  12, 3,  8),
(1, 2, 3, 'Loss',  1, 9,  6),
(1, 3, 4, 'Win',  10, 4,  7),
(1, 4, 2, 'Loss',  3, 8,  2),
-- Match 2
(2, 1, 4, 'Win',  14, 2, 10),
(2, 5, 3, 'Win',   2, 4, 15),
(2, 4, 1, 'Loss',  5, 7,  3),
(2, 2, 5, 'Loss',  4, 8,  5),
-- Match 3
(3, 1, 1, 'Loss',  6, 8,  4),
(3, 3, 2, 'Win',   5, 3,  9),
(3, 4, 4, 'Win',  11, 5,  6),
(3, 5, 3, 'Loss',  1, 7,  8),
-- Match 4
(4, 2, 1, 'Win',   9, 4,  7),
(4, 3, 5, 'Loss',  3, 9,  4),
(4, 5, 3, 'Win',   3, 2, 18),
(4, 4, 2, 'Loss',  2,10,  1),
-- Match 5
(5, 1, 5, 'Win',   8, 2,  6),
(5, 2, 4, 'Win',   7, 3,  5),
(5, 3, 1, 'Loss',  4, 7,  3),
(5, 4, 3, 'Win',   1, 3, 12),
-- Match 6
(6, 5, 5, 'Win',   5, 3, 10),
(6, 4, 1, 'Loss',  6, 8,  4),
(6, 1, 2, 'Win',   4, 2, 11),
(6, 3, 4, 'Loss',  7, 9,  5),
-- Match 7
(7, 2, 5, 'Win',  10, 3,  8),
(7, 5, 3, 'Loss',  2, 6,  9),
(7, 1, 4, 'Loss',  5, 7,  3),
(7, 4, 1, 'Win',   8, 4,  6),
-- Match 8
(8, 3, 1, 'Win',  11, 3,  9),
(8, 2, 2, 'Loss',  2, 8,  5),
(8, 1, 5, 'Win',   6, 4,  7),
(8, 5, 4, 'Win',   9, 5, 11),
-- Match 9
(9, 4, 4, 'Win',  13, 4,  7),
(9, 3, 3, 'Loss',  3, 9, 12),
(9, 2, 1, 'Win',   8, 5,  6),
(9, 5, 5, 'Loss',  4, 7,  5),
-- Match 10
(10, 1, 1, 'Win',  10, 3,  8),
(10, 4, 4, 'Win',  12, 4,  9),
(10, 2, 3, 'Loss',  2, 7, 10),
(10, 3, 2, 'Loss',  4, 8,  5);
