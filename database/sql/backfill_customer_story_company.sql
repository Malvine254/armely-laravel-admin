-- Backfill `company` for customer_stories and reduce `position` to the job title only.
-- Safe to run once in production. Each row is matched by id AND only updated while
-- `company` is still empty, so re-running it is a no-op.
--
-- IDs and values below match the production rows shown in phpMyAdmin (ids 11-20).
-- Review before running; adjust any company name you want displayed differently.

UPDATE customer_stories SET position = 'Chief Information Officer',          company = 'Swope Health'                  WHERE id = 11 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'IT Director',                        company = 'American Medical Staffing'     WHERE id = 12 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'IT Manager',                         company = 'KCG, Inc.'                     WHERE id = 13 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'Controller',                         company = 'Sage Butte Energy, LLC'        WHERE id = 14 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'CEO & Managing Partner',             company = 'T4S Partners'                  WHERE id = 15 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'Executive Director',                 company = 'Homeward Bound Inc.'           WHERE id = 16 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'Client Director',                    company = ''                              WHERE id = 17 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'IT Director',                        company = 'Northwood Energy'              WHERE id = 18 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'Vice President',                     company = 'IT Marketing Management, Inc'  WHERE id = 19 AND (company IS NULL OR company = '');
UPDATE customer_stories SET position = 'National Director of Development & Marketing', company = 'Lambda Legal'        WHERE id = 20 AND (company IS NULL OR company = '');
