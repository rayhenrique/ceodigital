# CEO Digital - Sistema de Gestão e Regulação Odontológica Especializada (SUS)

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel 13" />
</p>

<p align="center">
  <strong>Solução informatizada para gestão de Centros de Especialidades Odontológicas (CEO) municipais</strong><br>
  Integrada às diretrizes da Política Nacional de Saúde Bucal (Brasil Sorridente / SUS).
</p>

---

## 📌 Sobre o Projeto

O **CEO Digital** é um sistema web corporativo completo, desenvolvido para organizar, regular e otimizar o fluxo de atendimento em Centros de Especialidades Odontológicas (CEO) municipais. A plataforma conecta as Unidades Básicas de Saúde (UBS) de origem da atenção primária aos cirurgiões-dentistas especialistas da atenção secundária, garantindo equidade, transparência e controle rigoroso de vagas.

### Principais Recursos e Módulos:

1. **Recepção e Fluxo em Tempo Real:**
   - Controle dinâmico da fila do dia com registro rápido de chegada (timestamp).
   - Transição de status: `Agendado` ➔ `Presente (Na Recepção)` ➔ `Em Atendimento` ➔ `Concluído` ou `Falta (Ausente)`.
2. **Agenda Operacional Inteligente:**
   - Visualização diária dividida em abas por turno: **Manhã (07h às 12h)**, **Tarde (13h às 18h)** e **Noite (18h às 22h)**.
   - Respeito à grade semanal de turnos configurada por profissional.
   - Bloqueio de agendamento aos domingos.
   - **Encaixes Extraordinários (RF14):** Modal reativo em Alpine.js com validação de teto máximo de **2 encaixes de urgência por turno**.
3. **Fila de Espera Regulada (Demanda Reprimida):**
   - Classificação de risco clínica: **Urgente** (prioridade clínica imediata) vs **Normal** (ordem cronológica de entrada).
   - Promoção atômica via transação (`DB::transaction`): conversão de solicitação da fila diretamente em agendamento na agenda do profissional (**RF20**).
4. **Prontuário e Cadastro de Pacientes:**
   - Validação matemática estrita de dígitos verificadores do CPF brasileiro (`CpfRule`).
   - Higienização automática (`mutators`) para persistência de 11 dígitos numéricos limpos.
   - Vínculo obrigatório à UBS de referência e acompanhamento de Agente Comunitário de Saúde (ACS).
5. **Relatórios Gerenciais com Suporte a Impressão / PDF (`@media print`):**
   - **Absenteísmo e Faltas (RF22):** Taxa percentual de não comparecimento por UBS e lista nominal de faltosos.
   - **Produção Odontológica:** Total de atendimentos concluídos por dentista e especialidade, segregando consultas normais e encaixes.
   - **Demanda Reprimida & Tempo de Espera:** Volume de espera e cálculo de tempo médio na fila em dias.
6. **Segurança e Auditoria Imutável (RF24):**
   - Controle de acesso baseado em perfis: `admin` (Administrador geral) e `operador` (Recepção e agendamento).
   - [AuditoriaObserver](app/Observers/AuditoriaObserver.php): Registro universal automático em banco (`auditorias`) contendo usuário, IP, User-Agent e snapshots JSON (antes vs depois) com mascaramento de senhas.

---

## 🛠 Stack Tecnológica

- **Backend:** PHP 8.2+ | Laravel 13.x (`declare(strict_types=1);` em toda a base)
- **Banco de Dados:** MySQL 8.0+ / 8.4 (Engine `InnoDB`, charset `utf8mb4_unicode_ci`)
- **Frontend:** Blade Templates + Tailwind CSS + Alpine.js (Stack Laravel Breeze)
- **Bundler:** Vite 8.x
- **Testes:** PHPUnit com SQLite `:memory:` para CI/CD

---

## 💻 Manual 1: Instalação Local (Ambiente Windows com XAMPP)

Este guia orienta a instalação em computadores de desenvolvimento Windows utilizando XAMPP ou WAMP.

### 1.1 Pré-requisitos
- **PHP 8.2 ou superior** (com extensões ativas: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `xml`, `zip`, `intl`).
- **Composer 2.x** ([getcomposer.org](https://getcomposer.org/))
- **Node.js 18+ e npm** ([nodejs.org](https://nodejs.org/))
- **MySQL 8.0+** ativo no XAMPP na porta padrão `3306`.
- **Git for Windows** ([git-scm.com](https://git-scm.com/))

### 1.2 Passo a Passo de Configuração

#### 1. Clonar ou Acessar o Diretório
Abra o PowerShell ou Prompt de Comando e navegue até a pasta `htdocs` do seu XAMPP:
```powershell
cd C:\xampp\htdocs\ceodigital
```

#### 2. Instalar Dependências PHP
```powershell
composer install
```

#### 3. Configurar o Arquivo de Ambiente (`.env`)
Se o arquivo `.env` ainda não existir, copie o modelo:
```powershell
copy .env.example .env
```
Gere a chave da aplicação:
```powershell
php artisan key:generate
```

Abra o arquivo `.env` e confirme as credenciais do seu banco de dados MySQL local:
```ini
APP_NAME="CEO Digital"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_TIMEZONE=America/Maceio
APP_URL=http://localhost:8000
APP_LOCALE=pt_BR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceodigital
DB_USERNAME=root
DB_PASSWORD=
```

#### 4. Criar o Banco de Dados no MySQL
Acesse o MySQL pelo phpMyAdmin (`http://localhost/phpmyadmin`) ou via terminal e crie o banco:
```sql
CREATE DATABASE IF NOT EXISTS ceodigital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 5. Executar Migrações e Alimentar a Base (Seeders)
Execute o comando artisan para construir todas as 9 tabelas e popular especialidades odontológicas oficiais, UBSs e o usuário administrador:
```powershell
php artisan migrate --seed
```

#### 6. Instalar Dependências Frontend e Compilar
```powershell
npm install
npm run build
```
*(Para desenvolvimento ativo com Hot Reload, utilize `npm run dev`)*.

#### 7. Iniciar o Servidor Local
```powershell
php artisan serve
```

Acesse o sistema no navegador:
👉 **URL Pública / Landing Page:** `http://127.0.0.1:8000`  
👉 **Login do Sistema:** `http://127.0.0.1:8000/login`

**Credenciais Administrativas Configuradas:**
- **E-mail:** `rayhenrique@gmail.com`
- **Senha:** `1508rcrc`
*(Observação: a conta padrão do seeder inicial é `admin@ceodigital.gov.br` / `admin123`)*

---

## 🚀 Manual 2: Instalação e Deploy em VPS Hostinger (CloudPanel)

O **CloudPanel** é um painel de controle gratuito de alto desempenho para servidores Linux (Ubuntu/Debian) baseado em Nginx e PHP-FPM, padrão recomendado em instâncias VPS da Hostinger.

### 2.1 Passo a Passo no Painel CloudPanel

#### Passo 1: Criar o Site PHP no CloudPanel
1. Acesse o painel de controle do CloudPanel: `https://seu-ip-vps:8443`.
2. No menu lateral, clique em **Sites** ➔ **Add Site** ➔ **Create a PHP Site**.
3. Preencha as configurações:
   - **Domain Name:** `ceodigital.seudominio.gov.br` (ou o domínio/subdomínio apontado para o IP da VPS).
   - **Site User:** escolha ou crie um usuário (ex: `ceodigital`).
   - **App Template:** selecione **Laravel**.
   - **PHP Version:** selecione **PHP 8.2** ou **PHP 8.3**.
   - **Document Root:** verifique se está apontando obrigatoriamente para a pasta `/public`:
     ```text
     /home/ceodigital/htdocs/ceodigital.seudominio.gov.br/public
     ```
4. Clique em **Create**.

#### Passo 2: Criar o Banco de Dados MySQL
1. No menu lateral do CloudPanel, clique em **Databases** ➔ **Add Database**.
2. Preencha os campos:
   - **Database Name:** `ceodigital_db`
   - **Database User:** `ceodigital_user`
   - **Database User Password:** *Defina uma senha forte e anote.*
3. Clique em **Save**.

---

### 2.2 Passo a Passo no Terminal SSH da VPS

Conecte-se via SSH ao servidor como `root` ou com o usuário do site criado:
```bash
ssh root@IP_DA_SUA_VPS
```

#### 1. Navegar até o diretório do site
```bash
cd /home/ceodigital/htdocs/ceodigital.seudominio.gov.br
```

#### 2. Clonar ou Enviar o Código
Se estiver utilizando Git:
```bash
# Limpa arquivos temporários padrões criados pelo CloudPanel caso existam
rm -rf public/*

# Clona o repositório
git clone https://github.com/seu-usuario/ceodigital.git .
```
*(Ou envie os arquivos do projeto via SFTP/FileZilla para `/home/ceodigital/htdocs/ceodigital.seudominio.gov.br`)*.

#### 3. Configurar as Variáveis de Ambiente de Produção
Copie o arquivo `.env`:
```bash
cp .env.example .env
nano .env
```
Configure com as credenciais criadas no CloudPanel e parâmetros de produção:
```ini
APP_NAME="CEO Digital"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ceodigital.seudominio.gov.br
APP_TIMEZONE=America/Maceio
APP_LOCALE=pt_BR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceodigital_db
DB_USERNAME=ceodigital_user
DB_PASSWORD=SUA_SENHA_SEGURA_DO_CLOUDPANEL

SESSION_DRIVER=database
SESSION_LIFETIME=120
```
Salve e saia (`Ctrl + O`, `Enter`, `Ctrl + X`).

#### 4. Instalar Dependências PHP Otimizadas para Produção
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
```

#### 5. Compilar os Arquivos Frontend (Vite)
Se o Node.js estiver instalado no servidor:
```bash
npm install
npm run build
```
*(Alternativamente, você pode compilar em sua máquina local com `npm run build` e enviar a pasta `public/build` para o servidor).*

#### 6. Executar as Migrações e Seeders em Produção
```bash
php artisan migrate --force
php artisan db:seed --force
```

#### 7. Otimizar Caches do Laravel
Em ambiente de produção, execute os comandos de cache para obter o desempenho máximo:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 8. Ajustar Permissões de Arquivos e Pastas
Para evitar erros `500` de permissão de escrita em logs e cache de templates:
```bash
chown -R ceodigital:ceodigital /home/ceodigital/htdocs/ceodigital.seudominio.gov.br
chmod -R 775 storage bootstrap/cache
```

---

### 2.3 Configurações Finais no CloudPanel

#### 1. Ativar Certificado SSL Gratuito (Let's Encrypt)
1. No painel do CloudPanel, acesse seu site.
2. Clique na aba **SSL/TLS**.
3. Clique em **New Let's Encrypt Certificate**.
4. Marque seu domínio e clique em **Create Certificate**.
O CloudPanel configurará a renovação automática a cada 90 dias e forçará o tráfego seguro HTTPS.

#### 2. Configurar o Cron Job do Agendador do Laravel
Para rotinas automáticas (como limpeza de filas e relatórios):
1. No CloudPanel, vá na aba **Cron Jobs** do seu site.
2. Clique em **Add Cron Job**.
3. Configuração:
   - **Minute:** `*` | **Hour:** `*` | **Day:** `*` | **Month:** `*` | **Weekday:** `*` (ou selecione `Every Minute`)
   - **Command:**
     ```bash
     php /home/ceodigital/htdocs/ceodigital.seudominio.gov.br/artisan schedule:run >> /dev/null 2>&1
     ```
4. Clique em **Save**.

---

## 🧪 Executando os Testes Automatizados

O sistema conta com suíte de testes cobrindo validação de CPF, controle de acesso a perfis restritos, regras de negócio de encaixe, auditoria e fluxo de demanda reprimida:

```bash
php artisan test
```

Saída esperada:
```text
Pass: 35 tests, 94 assertions, 0 failures, 0 errors.
```

---

## 🏛 Estrutura de Diretórios em Destaque

```text
app/
├── Http/
│   ├── Controllers/        # Controllers dedicados (Agenda, Demanda, Relatórios, etc.)
│   ├── Middleware/         # EnsureUserIsAdmin para proteção de rotas admin
│   └── Requests/           # FormRequests dedicados com validações estritas
├── Models/                 # Paciente, Agendamento, DemandaReprimida, Dentista, etc.
├── Observers/              # AuditoriaObserver universal (registro automático de eventos)
├── Policies/               # UserPolicy para controle de autorização
├── Rules/                  # CpfRule com validação matemática de dígitos verificadores
└── Services/               # AgendamentoService (regras atômicas) e RelatorioService
resources/
├── views/
│   ├── agenda/             # Painel operacional de turnos e modais Alpine.js
│   ├── demanda-reprimida/  # Regulação da fila de espera e modal de promoção
│   ├── relatorios/         # Views analíticas e templates formatados para impressão
│   ├── landing.blade.php   # Portal público informativo institucional
│   └── dashboard.blade.php # Painel de recepção e KPIs do dia
routes/
├── web.php                 # Rotas da aplicação protegidas por middleware
└── auth.php                # Fluxo de login restrito
```

---

## 📄 Licença

Este projeto é desenvolvido para utilização em gestão de saúde pública municipal e está licenciado sob os termos da licença [MIT](LICENSE).
