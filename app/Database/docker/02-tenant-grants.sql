-- Privileges for per-shop databases (wbpos_t_*).
-- Re-run this on existing Docker volumes:
--   docker exec -i wbpos-mysql mysql -uroot -pwbpos_root < app/Database/docker/02-tenant-grants.sql

GRANT CREATE ON *.* TO 'wbpos'@'%';
GRANT ALL PRIVILEGES ON `wbpos_t_%`.* TO 'wbpos'@'%';
FLUSH PRIVILEGES;
