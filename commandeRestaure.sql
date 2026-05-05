#Pour lancer une commande
psql -U postgres -d rapport -f rapportBackup.sql

#Pour restaurer une base de donnee
pg_restore -U postgres -d rapport rapportBackup.sql



