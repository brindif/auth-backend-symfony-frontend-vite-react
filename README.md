# Empty Auth project in Symfony 8 api and frontend Vite React

A reusable, full-stack authentication foundation featuring a Symfony 8 / API Platform API secured with JWT access tokens, HttpOnly refresh tokens, and email verification, paired with a Vite + React frontend using Ant Design, Axios, and Redux for state and API calls.

- Frontend : https://github.com/brindif/frontend-auth-backend-symfony-frontend-vite-react
- Backend : https://github.com/brindif/auth-backend-symfony-frontend-vite-react

## Stack

- Backend : Symfony 8, API Platform
- Auth : lexik/jwt-authentication-bundle, gesdinet/jwt-refresh-token-bundle (refresh token en cookie HttpOnly)
- Emails : symfonycasts/verify-email-bundle, symfonycasts/reset-password-bundle
- Frontend : Vite React, TypeScript, Redux, Ant Design, axios

## Prérequis
- PHP >= 8.4
- Composer >= 2.9
- Node.js >= 10.9
- DB : PostgreSQL

## Installation (dev)

### Backend

```bash
git clone git@github.com:brindif/auth-backend-symfony-frontend-vite-react.git
cd auth-backend-symfony-frontend-vite-react
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

/etc/apache2/sites-available/backend-ssl.conf
```xml
<VirtualHost *:443>
    ServerName backend.local
    DocumentRoot /var/www/auth-backend-symfony-frontend-vite-react/public

    <Directory /var/www/auth-backend-symfony-frontend-vite-react/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.4-fpm.sock|fcgi://localhost/"
    </FilesMatch>

    SSLEngine on
    SSLCertificateFile /var/www/auth-backend-symfony-frontend-vite-react/rpg.local.pem
    SSLCertificateKeyFile /var/www/auth-backend-symfony-frontend-vite-react/rpg.local-key.pem

    ErrorLog ${APACHE_LOG_DIR}/rpg_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/rpg_ssl_access.log combined
</VirtualHost>

```

### Frontend

```bash
git clone git@github.com:brindif/frontend-auth-backend-symfony-frontend-vite-react.git
cd frontend-auth-backend-symfony-frontend-vite-react
npm install
npm run dev
```

## Configuration

### Backend (frontend/.env.local)

```dotenv
###> doctrine/doctrine-bundle ###
DATABASE_URL="postgresql://user:your-password@127.0.0.1:5432/backend?serverVersion=xx&charset=utf8"

###> nelmio/cors-bundle ###
CORS_ALLOW_ORIGIN='^https?://(frontend\.local|localhost|127\.0\.0\.1)(:[0-9]+)?$'

###> symfony/mailer ###
MAILER_DSN=smtp://localhost:1025
EMAIL=administrator@backend.local
SENDER_NAME=Administrator
EMAIL_SUBJECT=Authed-Platform
FRONTEND_URL=https://frontend.local:5173
BACKEND_URL='https?://(backend\.local|localhost|127\.0\.0\.1)(:[0-9]+)?'

###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=xxx
```

### Clés JWT

```bash
cd backend
php bin/console lexik:jwt:generate-keypair
```

### Frontend (frontend/.env.local)

```dotenv
VITE_API_BASE_URL=https://backend.local/api
```