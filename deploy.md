# 🚀 Guia Oficial de Deploy em Produção (VPS Hostinger + CloudPanel)

Este manual contém o passo a passo exato e testado para colocar o **CEO Digital** em produção na sua VPS da Hostinger utilizando o painel **CloudPanel**, com base nas configurações já iniciadas.

---

## 📋 Dados e Credenciais do Ambiente

| Parâmetro | Valor Configurado |
| :--- | :--- |
| **Painel CloudPanel** | `https://painel.sisgerp.com/` |
| **IP do Servidor** | `72.60.142.2` |
| **Domínio da Aplicação** | `ceodigital.kltecnologia.com` |
| **Usuário Linux do Site** | `kltecnologia-ceodigital` |
| **Diretório Raiz no Servidor** | `/home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com` |
| **Diretório Público Web** | `/home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com/public` |
| **Versão do PHP** | `PHP 8.5` (ou 8.4) |
| **Nome do Banco de Dados** | `ceodigital` |
| **Usuário do Banco** | `ceodigital` |
| **Senha do Banco** | `1508Cristiane@` |
| **Repositório GitHub** | `https://github.com/rayhenrique/ceodigital.git` |

---

## ⚙️ FASE 1: Verificações no Painel Web (CloudPanel)

Você já avançou muito bem nas telas do CloudPanel! Certifique-se apenas dos pontos abaixo:

1. **Definições do Domínio**:
   - Vá na aba **Definições** do site `ceodigital.kltecnologia.com`.
   - Certifique-se de que o **Diretório raiz** está apontado para:
     ```text
     ceodigital.kltecnologia.com/public
     ```
   - Clique em **Salvar** se ainda não tiver salvo.

2. **Banco de Dados MySQL**:
   - Certifique-se de que o banco `ceodigital` com usuário `ceodigital` foi criado conforme a tela preenchida.

3. **Apontamento DNS (Importante)**:
   - No gerenciador de DNS do domínio `kltecnologia.com` (Hostinger, Cloudflare ou Registro.br), crie o apontamento:
     - **Tipo:** `A`
     - **Nome / Host:** `ceodigital`
     - **Valor / Destino:** `72.60.142.2`
     - **TTL:** Padrão / Automático

---

## 💻 FASE 2: Comandos no Terminal SSH da VPS

Você já está conectado como **`root@sisgerp:~#`**. Execute os blocos de comandos abaixo em ordem:

### 1. Acessar a pasta do site e limpar arquivos temporários do CloudPanel
O CloudPanel cria uma pasta com arquivos de exemplo. Vamos entrar na pasta e limpá-la para receber o projeto do GitHub:

```bash
cd /home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com
rm -rf * .* 2>/dev/null
```

---

### 2. Clonar o Repositório do GitHub

```bash
git clone https://github.com/rayhenrique/ceodigital.git .
```

> [!NOTE]
> Se o seu repositório for **Privado**, o Git solicitará usuário e senha:
> - **Username:** `rayhenrique`
> - **Password:** Utilize o seu **GitHub Personal Access Token (PAT)** com permissão `repo` (gere em *GitHub -> Settings -> Developer Settings -> Personal access tokens*).
>
> *(Se o repositório estiver Público, ele clonará instantaneamente sem pedir senha).*

---

### 3. Criar e Configurar o Arquivo `.env` de Produção

Crie o arquivo `.env` copiando o modelo:

```bash
cp .env.example .env
```

Abra o arquivo para edição com o `nano`:

```bash
nano .env
```

Cole e ajuste as variáveis de produção exatamente conforme abaixo:

```ini
APP_NAME="CEO Digital"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://ceodigital.kltecnologia.com
APP_TIMEZONE=America/Maceio
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceodigital
DB_USERNAME=ceodigital
DB_PASSWORD=1508Cristiane@

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

QUEUE_CONNECTION=database
CACHE_STORE=database
```

Para salvar no `nano`: pressione `Ctrl + O`, tecle `Enter`, e depois `Ctrl + X` para sair.

---

### 4. Instalar Dependências do PHP (Composer)

O Composer precisa rodar primeiro para criar a pasta `vendor/` necessária para os comandos `artisan`:

```bash
composer install --no-dev --optimize-autoloader
```

---

### 5. Gerar a Chave da Aplicação (`APP_KEY`)

Após a instalação do Composer, gere a chave criptográfica do Laravel:

```bash
php artisan key:generate --force
```

---

### 6. Executar Migrações e Dados Iniciais do Banco de Dados

Cria todas as tabelas (UBS, Pacientes, Especialidades, Dentistas, Agenda, Demanda Reprimida, Usuários, etc.) e cadastra as especialidades básicas e o usuário inicial:

```bash
php artisan migrate --seed --force
```

---

### 7. Atualizar o Usuário Administrador no Banco

Para garantir que a sua conta de administrador esteja ativa com seu e-mail e senha pessoal (`1508rcrc`), execute o comando abaixo no terminal da VPS:

```bash
php artisan tinker --execute="
\$u = \App\Models\User::first();
\$u->name = 'Ray Henrique';
\$u->email = 'rayhenrique@gmail.com';
\$u->password = \Illuminate\Support\Facades\Hash::make('1508rcrc');
\$u->role = 'admin';
\$u->status_ativo = true;
\$u->save();
echo 'ADMINISTRADOR CONFIGURADO COM SUCESSO: ' . \$u->email . PHP_EOL;
"
```

---

### 8. Compilar os Arquivos Frontend (Vite / Tailwind / Alpine.js)

Se o Node.js e NPM estiverem instalados na VPS, execute:

```bash
npm install
npm run build
```

> [!TIP]
> Caso a VPS não possua Node.js instalado, você pode instalá-lo facilmente com:
> `curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs`

---

### 9. Criar Link Simbólico de Armazenamento e Caches de Alta Performance

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### 10. Ajustar Permissões de Arquivos e Pastas (CRUCIAL no CloudPanel)

No CloudPanel, o Nginx e o PHP-FPM rodam sob o usuário do site (`kltecnologia-ceodigital`). Portanto, todas as permissões devem pertencer a ele:

```bash
chown -R kltecnologia-ceodigital:kltecnologia-ceodigital /home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com
chmod -R 775 /home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com/storage
chmod -R 775 /home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com/bootstrap/cache
```

---

## 🔒 FASE 3: SSL Grátis (HTTPS) no CloudPanel

1. No CloudPanel, clique no site `ceodigital.kltecnologia.com`.
2. Acesse a aba **SSL/TLS**.
3. Clique em **Novo Certificado SSL**.
4. Selecione **Let's Encrypt**.
5. Clique em **Criar e Instalar**.
6. Em poucos segundos o cadeado verde HTTPS estará ativo para o domínio!

---

## ⏱️ FASE 4: Agendador de Tarefas do Laravel (Cron Job)

Para que o Laravel processe filas, lembretes e rotinas automáticas de faltas e agendamentos:

1. No CloudPanel, no site `ceodigital.kltecnologia.com`, acesse a aba **Cron Jobs**.
2. Clique em **Adicionar Cron Job**.
3. Preencha:
   - **Minuto / Hora / Dia / Mês / Dia da semana:** `* * * * *` (A cada minuto)
   - **Comando:**
     ```bash
     php /home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com/artisan schedule:run >> /dev/null 2>&1
     ```
4. Clique em **Salvar**.

---

## 🔄 FASE 5: Script de Atualização Rápida (Futuros Deploys)

Sempre que você fizer alterações no código e subir para o GitHub, basta entrar no terminal da VPS e rodar estes comandos para atualizar o sistema em segundos:

```bash
cd /home/kltecnologia-ceodigital/htdocs/ceodigital.kltecnologia.com
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R kltecnologia-ceodigital:kltecnologia-ceodigital .
```

---

## ✅ Pronto para Acesso!

Acesse o sistema pelo navegador:
- 🌐 **Landing Page:** [https://ceodigital.kltecnologia.com](https://ceodigital.kltecnologia.com)
- 🔐 **Login Institucional:** [https://ceodigital.kltecnologia.com/login](https://ceodigital.kltecnologia.com/login)

**Credenciais Administrativas:**
- **E-mail:** `rayhenrique@gmail.com`
- **Senha:** `1508rcrc`
