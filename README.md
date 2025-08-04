# PolyLeave - Leave Management System 

A lightweight example that showcases how different technologies can live under one roof — Vue SPA, Laravel, FastAPI, Rails — all balanced by Nginx and backed by a shared MySQL database.

## 1. Architecture (bird’s‑eye view)

```shell
                         ┌──────────────┐
                         │   Internet   │
                         └──────┬───────┘
                                │ 80 / 443
                                ▼
                         ┌──────────────┐
                         │  Host Nginx  │   ← TLS + redirects
                         └──────┬───────┘
                                │ 8080 (localhost)
                                ▼
                   ┌──────────────────────────┐
                   │  Docker Nginx Load-bal.  │
                   └──────┬───────────┬───────┘                       
                          │           │                               
          ┌───────────────┘     ┌─────└────────────┐──────────────────┐                  
          │                     │                  │                  │
┌──────────────┐      ┌────────────────┐   ┌────────────────┐   ┌──────────────┐
│  Vue SPA     │      │  Laravel API   │   │  FastAPI API   │   │  Rails API   │
│ (frontend)   │      │  (PHP)         │   │  (Python)      │   │  (Ruby)      │
└──────────────┘      └───────┬────────┘   └──────┬─────────┘   └─────┬────────┘
                              │                   │                   │
                              └───────────────────│───────────────────┘
                                                  │                   
                                                  │                   
                              ┌───────────────────│───────────────────┐                    
                              │                   │                   │      
                              │                   │                   │
                              │                   │                   │      
                              │                   │                   │
                   ┌──────────┴─────────┐   ┌─────┴───────┐   ┌───────┴──────┐
                   │      MySQL         │   │    Redis    │   │   MailHog    │
                   │  (shared schema)   │   │(cache/queue)│   │ (dev SMTP)   │
                   └─────────┬──────────┘   └─────────────┘   └──────────────┘
                             ▲
                             │
                       ┌─────┴─────┐
                       │  dbmate   │
                       │ migrations│
                       └───────────┘
```

# PolyLeave – Leave Management System

A lightweight example that showcases how different technologies can live under one roof — Vue SPA, Laravel, FastAPI, Rails — all balanced by Nginx and backed by a shared MySQL database.

---

*A single `docker‑compose` file wires everything together.*

---

## 2. Environment setup

```bash
cp .env.example .env   # copy template
# …tweak anything you like (defaults work out‑of‑the‑box)
```
* **Docker 20.10+** & **Docker Compose v2**
* *(optional)* GitHub OAuth credentials if you want “Sign in with GitHub”.

---

## 3. Prerequisites

### Enable GitHub sign‑in (optional)

1. **GitHub → Settings → Developer settings → OAuth Apps → New OAuth App**
2. Fill in:

    * **Homepage URL:** `http://localhost:8080`
    * **Callback URL:** `http://localhost:8080/oauth/github`
3. Copy **Client ID** & **Client Secret** to `.env`:

   ```dotenv
   GITHUB_CLIENT_ID=xxx
   GITHUB_CLIENT_SECRET=yyy
   ```

---

## 4. One‑command bootstrap

```bash
sudo docker compose up mysql redis mailhog migrate laravel frontend lb
```

> **Tip:** omit service names to build & start *everything*.

| URL                                            | What it is                     |
| ---------------------------------------------- | ------------------------------ |
| [http://localhost:8080](http://localhost:8080) | Web app (SPA + APIs)           |
| [http://localhost:8025](http://localhost:8025) | MailHog UI (intercepted email) |

Services spun‑up by the command:

| Service      | Role                                |
| ------------ | ----------------------------------- |
| **mysql**    | MySQL 8 database                    |
| **redis**    | Redis cache / queue                 |
| **mailhog**  | Dev SMTP & UI for outgoing mail     |
| **migrate**  | Runs dbmate migrations & seeders    |
| **laravel**  | PHP API                             |
| **frontend** | Vite + Vue SPA                      |
| **lb**       | Nginx reverse‑proxy / load‑balancer |

---

## 5. Demo logins

| Role      | E‑mail                                                | Password |
| --------- | ----------------------------------------------------- | -------- |
| Moderator | [moderator@example.com](mailto:moderator@example.com) | `secret` |
| User      | [john@example.com](mailto:john@example.com)           | `secret` |

> Moderators can approve leave requests and manage users.

---

## 6. Next steps

* Point your browser at **`localhost:8080`**, sign in, and request your first leave.
* Explore each API individually (Laravel, FastAPI, Rails) via the load‑balancer.
* Extend the data model by adding new `dbmate` migrations.

Enjoy hacking! ✨
