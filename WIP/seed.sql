-- Players
INSERT INTO PLAYERS (PLAYER_ID, USERNAME, SERVER, LEVEL, CREATE_TIME) VALUES
(1, 'Faker', 'KR', 899, '2013-04-06 00:00:00'),
(2, 'Oner', 'EU', 626, '2020-12-01 00:00:00'),
(3, 'Gumayusi', 'NA', 866, '2020-09-08 00:00:00'),
(4, 'Zeus', 'CN', 783, '2021-02-03 00:00:00'),
(5, 'Keria', 'KR', 973, '2019-12-26 00:00:00');

-- Characters
INSERT INTO CHARACTERS (CHARACTER_ID, NAME, ROLE, DIFFICULTY) VALUES
(1, 'Ryze', 'Middle', 'Hard'),
(2, 'Jax', 'Top', 'Medium'),
(3, 'Ashe', 'Bottom', 'Easy'),
(4, 'Master Yi', 'Jungle', 'Easy'),
(5, 'Alistar', 'Support', 'Easy');

-- Matches
INSERT INTO MATCHES (MATCH_ID, GAME_MODE, MAP, DURATION, DATE) VALUES
(1, 'Normal', 'Summoner''s Rift', 1200, '2026-04-21 00:00:00'),
(2, 'ARAM', 'Winter', 1000, '2026-04-21 12:00:00'),
(3, 'Ranked', 'Summoner''s Rift', 1800, '2026-04-21 18:30:00');

-- Match stats
INSERT INTO MATCH_STATS (PLAYER_ID, MATCH_ID, CHARACTER_ID, KILLS, DEATHS, ASSISTS, WIN_LOSS) VALUES
(1, 1, 1, 10, 4, 12, 'Win'),
(1, 2, 5, 4, 6, 1, 'Win'),
(2, 1, 3, 2, 5, 0, 'Loss'),
(3, 2, 1, 5, 2, 5, 'Win');
