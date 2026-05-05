
INSERT INTO roles (id, name,created_at) VALUES (1, 'Admin', NOW());
INSERT INTO roles (id, name,created_at) VALUES (2, 'Utilisateur',NOW());
INSERT INTO roles (id, name,created_at) VALUES (3, 'Supervisor',NOW());

INSERT INTO type_calendriers (id, name,created_at) VALUES (1, 'Hebdomadaire', NOW());
INSERT INTO type_calendriers (id, name,created_at) VALUES (2, 'Mensuel', NOW());
INSERT INTO type_calendriers (id, name,created_at) VALUES (3, 'Trimestriel',NOW());
INSERT INTO type_calendriers (id, name,created_at) VALUES (4, 'Annuel',NOW());

INSERT INTO type_effect_impacts (id, name,created_at) VALUES (1, 'Impact', NOW());
INSERT INTO type_effect_impacts (id, name,created_at) VALUES (2, 'Effet',NOW());
INSERT INTO type_effect_impacts (id, name,created_at) VALUES (3, 'Produit',NOW());
INSERT INTO type_effect_impacts (id, name,created_at) VALUES (4, 'Cible',NOW());
INSERT INTO type_effect_impacts (id, name,created_at) VALUES (5, 'Prevision',NOW());
INSERT INTO type_effect_impacts (id, name,created_at) VALUES (6, 'Realisation',NOW());
INSERT INTO type_effect_impacts (id, name,created_at) VALUES (7, 'TauxRealisation',NOW());
INSERT INTO type_effect_impacts (id, name,created_at) VALUES (8, 'Observation',NOW());



INSERT INTO utilisateurs (id, role_id, created_at, deleted_at, email, mdp, entite, date_validation) VALUES
(2, 2, '2026-02-25 18:20:15', NULL, 'test@gmail.com', '$2y$10$ttmhUKN2FFIODtv/WP9sjuGskXi5T2ni0JZyfdv4lHgT70p7otfXm', 'SP', NULL),

(3, 2, '2026-03-02 17:19:34', NULL, 'randriadode@gmail.com', '$2y$10$.A8ZGJp5dDs3w0baVRf9F.CFjKn.cDcFbcGSWOIkXX6kiNlt2CuNi', 'utilisateur', NULL),

(1, 1, '2026-02-25 18:19:39', NULL, 'admin@gmail.com', '$2y$10$xdhyIwCoCdrbcqWIKxDHQOH7YMRePJJSxWYIFdJlMcpQviLVDNk2C', 'Admin', '2026-03-05 14:54:10');
