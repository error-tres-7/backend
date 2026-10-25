# Entrepuestos API

PHP 8.3 + MySQL JSON API, served at `https://api.entrepuestos.com` from Railway.

## Endpoints

| Method | Path | Body (JSON or form) | Returns |
| --- | --- | --- | --- |
| GET | `/health` | — | `200 {status:"ok"}` or `503` if MySQL is unreachable |
| POST | `/auth/register` | `nombre, correo, contraseña, rol?` (`cliente`\|`comerciante`) | `201` user · `409` email taken · `422` invalid |
| POST | `/auth/login` | `correo, contraseña` | `200` user + session cookie · `401` |
| POST | `/auth/logout` | — | `200` |
| GET | `/auth/me` | — | `200` user · `401` |

Errors are `{ "message": string, "errors"?: string[] }`, as the frontend expects. `contrasena` is accepted
as an alias of `contraseña`. The frontend must call with `credentials: "include"`.

The `/merchant/*` contract in the frontend's `MERCHANT-API.md` is **not implemented yet**.

## Deploy on Railway

1. New project → **Deploy from GitHub repo** → `error-tres-7/backend`. `railway.json` selects the Dockerfile,
   runs `php bin/migrate.php` before each deploy, and health-checks `/health`.
2. **+ New → Database → MySQL** in the same project.
3. In the API service → **Variables**:

   ```
   MYSQLHOST=${{MySQL.MYSQLHOST}}
   MYSQLPORT=${{MySQL.MYSQLPORT}}
   MYSQLUSER=${{MySQL.MYSQLUSER}}
   MYSQLPASSWORD=${{MySQL.MYSQLPASSWORD}}
   MYSQLDATABASE=${{MySQL.MYSQLDATABASE}}
   ALLOWED_ORIGINS=https://entrepuestos.com,https://www.entrepuestos.com
   SESSION_DOMAIN=.entrepuestos.com
   ```

4. API service → **Settings → Networking → Custom Domain** → `api.entrepuestos.com`. Add the `CNAME`
   (and TXT, if shown) record Railway gives you at your DNS provider. If the DNS is on Cloudflare,
   use DNS-only (grey cloud) until Railway issues the certificate.
5. In the frontend, set `API_URL=https://api.entrepuestos.com` and rebuild (it is a static variable).

## Local

```sh
docker build -t entrepuestos-api .
docker run --rm -p 8080:8080 -e MYSQLHOST=host.docker.internal -e MYSQLPASSWORD=... \
  -e ALLOWED_ORIGINS=http://localhost:5173 -e SESSION_SECURE=false entrepuestos-api
docker run --rm -e MYSQLHOST=host.docker.internal -e MYSQLPASSWORD=... entrepuestos-api php bin/migrate.php
```
