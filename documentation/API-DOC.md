# URL pour créer utilisateur

```http
POST http://192.168.88.9:8000/utilisateurs
```

**Authentification:** `#[TokenRequired(['Admin'])]`

**Body:**
```json
{
  "email":"admin@gmail.com",
  "mdp":"adminadmin",
  "idRole": 1,
  "entite": "Admin"
}
```

**Réponse:**
```json
{
  "status": "success",
  "data": {
      "email": "admin@gmail.com",
      "mdp": "$2y$10$kXQ/WPe1pV8VniNoumBGguTew7fW36rY4jhUKeZTAaVyYG0bxVqxe",
      "entite": "Admin",
      "role": {
          "name": "Admin",
          "id": 1,
          "createdAt": "2026-02-25T17:17:16+03:00",
          "deletedAt": null,
          "deleted": false
      },
      "id": 1,
      "createdAt": "2026-02-25T17:21:40+03:00",
      "deletedAt": null,
      "deleted": false
  }
}
```
# URL pour login

```http
POST http://192.168.88.9:8000/utilisateurs/login
```

**Body:**
```json
{
  "email":"admin@gmail.com",
  "mdp":"adminadmin"
}
```

**Réponse:**
```json
{
  "status": "success",
  "data": {
      "membre": {
          "email": "test@gmail.com",
          "role": "Utilisateur",
          "entite": "SP"
      },
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzcyMDI5NDk5LjI1NTQ1MSwiZXhwIjoxNzcyMDMzMDk5LjI1NTQ1MSwiaWQiOjIsImVtYWlsIjoidGVzdEBnbWFpbC5jb20iLCJyb2xlIjoiVXRpbGlzYXRldXIiLCJlbnRpdGUiOiJTUCJ9.EuJL-c5gUQ3ZTqldfSnVjDsNN7068DdnYXItYK4a8Cs"
  }
}
```

# URL pour créer calendrier

```http
POST http://192.168.88.9:8000/calendriers
```

**Authentification:** `#[TokenRequired(['Admin'])]`

**Body:**
```json
{
  "dateDebut": "2026-01-01",
  "dateFin": "2026-01-31",
  "typeCalendrierId": 2
}
```
  
# URL pour faire un rapport

```http
POST http://192.168.88.9:8000/rapports
```

**Body:**
```json
{
  "idCalendrier": 2,
  "activites": [
    {
      "activite": {
        "name": "Reboisement communautaire",
        "id": 30
      },
      "effects": [
        { "name": "Amélioration de la qualité de l'air" },
        { "name": "Réduction de l'érosion des sols" }
      ],
      "impacts": [
        { "name": "Augmentation de la biodiversité" },
        { "name": "Sensibilisation environnementale de la population" }
      ]
    },
    {
      "activite": {
        "name": "Campagne de sensibilisation environnementale",
        "id": 31
      },
      "effects": [
        { "name": "Augmentation de la sensibilisation" },
        { "name": "Changement de comportement des citoyens" },
        { "name": "Réduction des déchets plastiques" }
      ],
      "impacts": [
        { "name": "Amélioration de la propreté urbaine" },
        { "name": "Diminution de la pollution" }
      ]
    },
    {
      "activite": {
        "name": "Atelier de formation en gestion des déchets",
        "id": 32
      },
      "effects": [
        { "name": "Acquisition de bonnes pratiques" },
        { "name": "Réduction des déchets industriels" }
      ],
      "impacts": [
        { "name": "Meilleure gestion des ressources locales" },
        { "name": "Réduction de la pollution environnementale" }
      ]
    }
  ]
}
```

**Réponse:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 17,
            "createdAt": "2026-02-25 19:13:08",
            "deletedAt": null,
            "utilisateur": {
                "email": "admin@gmail.com",
                "mdp": "$2y$10$r43kiahjMyCje0hyQ5KWjOLNRQil5KmArQqwy1qKpXP6FMSJ48m6e",
                "entite": "Admin",
                "id": 1,
                "createdAt": "2026-02-25 18:19:39",
                "deletedAt": null,
                "role": "Admin"
            },
            "calendrier": {
                "id": 1,
                "dateDebut": "2026-01-01",
                "dateFin": "2026-01-31",
                "typeCalendriers": {
                    "id": 1,
                    "name": "Hebdomadaire"
                }
            },
            "activites": [
                {
                    "activite": {
                        "name": "Projet ERP",
                        "id": 17,
                        "createdAt": "2026-02-25 19:13:08",
                        "deletedAt": null
                    },
                    "effectsImpacts": [
                        {
                            "effect": "Retard livraison",
                            "impact": "Décalage planning",
                            "id": 1,
                            "createdAt": "2026-02-25 19:13:08",
                            "deletedAt": null
                        },
                        {
                            "effect": "Absence équipe",
                            "impact": "Baisse productivité",
                            "id": 2,
                            "createdAt": "2026-02-25 19:13:08",
                            "deletedAt": null
                        }
                    ]
                },
                {
                    "activite": {
                        "name": "Projet CRM",
                        "id": 18,
                        "createdAt": "2026-02-25 19:13:08",
                        "deletedAt": null
                    },
                    "effectsImpacts": [
                        {
                            "effect": "Retard test",
                            "impact": "Vody bobota",
                            "id": 3,
                            "createdAt": "2026-02-25 19:13:08",
                            "deletedAt": null
                        }
                    ]
                }
            ]
        }
    ]
}
```
# URL pour avoir la liste des calendriers

```http
GET http://192.168.88.9:8000/calendriers
```

**Authentification:** `#[TokenRequired(admin)]`

**Méthode:** GET

**Réponse:**
```json
{
      "status": "success",
      "data": [
          {
              "dateDebut": "2026-01-01",
              "dateFin": "2026-01-31",
              "id": 1,
              "typeCalendrier": {
                  "name": "Hebdomadaire",
                  "id": 1
              }
          }
      ]
  }
```

# URL pour récupérer les rapports

```http
GET http://192.168.88.9:8000/rapports
```

**Authentification:** `#[TokenRequired]`

```http
GET http://192.168.88.9:8000/rapports/calendrier?idCalendrier=1
```

**Authentification:** `#[TokenRequired(admin)]`

**Réponse:**
```json
{
  "status": "success",
  "data": [
      {
          "id": 17,
          "utilisateur": {
              "role": "Admin"
          },
          "calendrier": {
              "dateDebut": "2026-01-01",
              "dateFin": "2026-01-31",
              "typeCalendrier": {
                  "name": "Hebdomadaire"
              }
          },
          "activites": [
              {
                  "activite": {
                      "name": "Projet ERP",
                      "id": 17
                  },
                  "effectsImpacts": [
                      {
                          "effect": "Retard livraison",
                          "impact": "Décalage planning",
                          "id": 1
                      },
                      {
                          "effect": "Absence équipe",
                          "impact": "Baisse productivité",
                          "id": 2
                      }
                  ]
              },
              {
                  "activite": {
                      "name": "Projet CRM",
                      "id": 18
                  },
                  "effectsImpacts": [
                      {
                          "effect": "Retard test",
                          "impact": "Vody bobota",
                          "id": 3
                      }
                  ]
              }
          ]
      }
  ]
}
```
    

# URL pour récupérer les utilisateurs qui n'ont pas de calendrier

```http
GET http://192.168.88.9:8000/utilisateurs/calendrierRetard?idCalendrier=2
```

**Authentification:** `#[TokenRequired(admin)]`

**Réponse:**
```json
{
    "status": "success",
    "data": [
        {
            "email": "admin@gmail.com",
            "entite": "Admin",
            "id": 1,
            "role": "Admin"
        },
        {
            "email": "test@gmail.com",
            "entite": "SP",
            "id": 2,
            "role": "Utilisateur"
        }
    ]
}
```

# URL pour récupérer les calendriers disponibles pour un utilisateur

```http
GET http://192.168.88.9:8000/calendriers/utilisateur
```

**Authentification:** `#[TokenRequired]`

**Réponse:**
```json
{
    "status": "success",
    "data": [
        {
            "dateDebut": "2026-02-01 00:00:00",
            "dateFin": "2026-02-07 00:00:00",
            "id": 2,
            "typeCalendrier": {
                "name": "Hebdomadaire",
                "id": 1
            }
        }
    ]
}
```

# URL pour créer un OS

```http
POST http://192.168.88.9:8000/rapports/OS
```

**Authentification:** `#[TokenRequired]`

**Body:**
```json
{
    "name":"LI 1"
}
```

**Réponse:**
```json
{
    "status": "success",
    "data": {
        "name": "OS 1",
        "id": 1
    }
}
```

# URL pour récupérer les OS

```http
GET http://192.168.88.9:8000/rapports/OS
```

**Authentification:** `#[TokenRequired]`

**Réponse:**
```json
{
    "status": "success",
    "data": [
        {
            "name": "OS 1",
            "id": 1
        }
    ]
}
```

# URL pour créer un LI

```http
POST http://192.168.88.9:8000/rapports/LI
```

**Authentification:** `#[TokenRequired]`

**Body:**
```json
{
    "name":"LI 1"
}
```

**Réponse:**
```json
{
    "status": "success",
    "data": {
        "name": "LI 1",
        "id": 1
    }
}
```

# URL pour récupérer les LI

```http
GET http://192.168.88.9:8000/rapports/LI
```

**Authentification:** `#[TokenRequired]`

**Réponse:**
```json
{
    "status": "success",
    "data": [
        {
            "name": "LI 1",
            "id": 1
        }
    ]
}
```

# URL pour éditer un OS

```http
PUT http://192.168.88.9:8000/rapports/OS/{id}
```

**Authentification:** `#[TokenRequired]`

**Body:**
```json
{
    "name": "OS modifié"
}
```

**Réponse:**
```json
{
    "status": "success",
    "data": {
        "name": "OS modifié",
        "id": 1
    }
}
```

# URL pour éditer un LI

```http
PUT http://192.168.88.9:8000/rapports/LI/{id}
```

**Authentification:** `#[TokenRequired]`

**Body:**
```json
{
    "name": "LI modifié"
}
```

**Réponse:**
```json
{
    "status": "success",
    "data": {
        "name": "LI modifié",
        "id": 1
    }
}
```
