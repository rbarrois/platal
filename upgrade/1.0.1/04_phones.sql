ALTER TABLE profile_phones MODIFY COLUMN pid INT(6) NOT NULL DEFAULT 0;
ALTER TABLE profile_phones MODIFY COLUMN link_id INT(6) NOT NULL DEFAULT 0;

DELETE FROM profile_phones WHERE search_tel = '' OR display_tel = '' OR link_type = 'hq';

UPDATE geoloc_countries SET phoneFormat = '(+p) # ## ## ## ##' WHERE phoneFormat = '0# ## ## ## ##';

-- vim:set syntax=mysql:
