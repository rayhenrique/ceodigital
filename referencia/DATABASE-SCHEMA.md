# Database Architecture & Relational Schema - MySQL 8.0

## Resumo dos Relacionamentos

* **UBS** possui muitos **Pacientes** (1:N)
* **Paciente** possui muitos **Agendamentos** (1:N) e muitos registros em **Demanda Reprimida** (1:N)
* **Especialidade** possui muitos **Dentistas** (1:N), muitos **Agendamentos** (1:N) e muitos registros em **Demanda Reprimida** (1:N)
* **Dentista** possui muitas **Grades** de atendimento (1:N) e muitos **Agendamentos** (1:N)
* **Usuário** (User) registra muitos **Agendamentos** e **Auditorias** (1:N)

---

## Estrutura Detalhada das Tabelas

### 1. `users`
Contas com acesso ao painel do sistema.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `name`: `VARCHAR(255) NOT NULL`
- `email`: `VARCHAR(255) UNIQUE NOT NULL`
- `password`: `VARCHAR(255) NOT NULL`
- `role`: `ENUM('admin', 'operador') NOT NULL DEFAULT 'operador'`
- `status_ativo`: `BOOLEAN NOT NULL DEFAULT TRUE`
- `remember_token`: `VARCHAR(100) NULL`
- `created_at`, `updated_at`: `TIMESTAMP NULL`

### 2. `ubs`
Unidades Básicas de Saúde encaminhadoras.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `nome`: `VARCHAR(255) UNIQUE NOT NULL`
- `endereco`: `VARCHAR(255) NULL`
- `diretor`: `VARCHAR(255) NULL`
- `contato`: `VARCHAR(100) NULL`
- `created_at`, `updated_at`: `TIMESTAMP NULL`

### 3. `pacientes`
Cadastro único de pacientes do município.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `ubs_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES ubs(id) ON DELETE RESTRICT`
- `cpf`: `VARCHAR(11) UNIQUE NOT NULL` (apenas dígitos numéricos)
- `cns`: `VARCHAR(15) NULL` (Cartão Nacional de Saúde)
- `nome_completo`: `VARCHAR(255) NOT NULL`
- `data_nascimento`: `DATE NOT NULL`
- `sexo`: `ENUM('M', 'F', 'Outro') NOT NULL`
- `endereco`: `TEXT NULL`
- `telefone_1`: `VARCHAR(20) NOT NULL`
- `telefone_2`: `VARCHAR(20) NULL`
- `nome_acs`: `VARCHAR(255) NULL`
- `created_at`, `updated_at`: `TIMESTAMP NULL`
- **Índices:** `INDEX idx_pacientes_nome (nome_completo)`, `INDEX idx_pacientes_cpf (cpf)`

### 4. `especialidades`
Especialidades odontológicas oferecidas pelo CEO.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `nome`: `VARCHAR(150) UNIQUE NOT NULL`
- `descricao`: `TEXT NULL`
- `status_ativo`: `BOOLEAN NOT NULL DEFAULT TRUE`
- `created_at`, `updated_at`: `TIMESTAMP NULL`

### 5. `dentistas`
Profissionais de saúde do quadro do CEO.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `especialidade_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES especialidades(id) ON DELETE RESTRICT`
- `nome_completo`: `VARCHAR(255) NOT NULL`
- `cro`: `VARCHAR(30) UNIQUE NOT NULL`
- `telefone`: `VARCHAR(20) NULL`
- `status_ativo`: `BOOLEAN NOT NULL DEFAULT TRUE`
- `created_at`, `updated_at`: `TIMESTAMP NULL`

### 6. `dentista_grades`
Configuração de escala de atendimento do dentista por turno e dia.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `dentista_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES dentistas(id) ON DELETE CASCADE`
- `dia_semana`: `TINYINT UNSIGNED NOT NULL` (1 = Segunda, 2 = Terça, ..., 6 = Sábado)
- `turno`: `ENUM('manha', 'tarde', 'noite') NOT NULL`
- `vagas_padrao`: `SMALLINT UNSIGNED NOT NULL DEFAULT 8`
- `created_at`, `updated_at`: `TIMESTAMP NULL`
- **Chave Única:** `UNIQUE KEY uk_dentista_escala (dentista_id, dia_semana, turno)`

### 7. `agendamentos`
Registros diários de atendimento.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `paciente_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES pacientes(id) ON DELETE RESTRICT`
- `dentista_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES dentistas(id) ON DELETE RESTRICT`
- `especialidade_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES especialidades(id) ON DELETE RESTRICT`
- `user_id`: `BIGINT UNSIGNED NOT NULL` (operador criador) -> `FOREIGN KEY REFERENCES users(id) ON DELETE RESTRICT`
- `data_agendamento`: `DATE NOT NULL`
- `turno`: `ENUM('manha', 'tarde', 'noite') NOT NULL`
- `tipo`: `ENUM('normal', 'encaixe', 'espontanea') NOT NULL DEFAULT 'normal'`
- `status`: `ENUM('agendado', 'presente', 'em_atendimento', 'concluido', 'falta', 'cancelado') NOT NULL DEFAULT 'agendado'`
- `horario_chegada`: `TIME NULL`
- `observacao`: `TEXT NULL`
- `created_at`, `updated_at`: `TIMESTAMP NULL`
- **Índices Compostos:**
  - `INDEX idx_agenda_busca (data_agendamento, turno, dentista_id)`
  - `INDEX idx_agenda_status (data_agendamento, status)`

### 8. `demanda_reprimida`
Fila de espera para procedimentos especializados sem vaga imediata.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `paciente_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES pacientes(id) ON DELETE CASCADE`
- `especialidade_id`: `BIGINT UNSIGNED NOT NULL` -> `FOREIGN KEY REFERENCES especialidades(id) ON DELETE RESTRICT`
- `turno_preferencial`: `ENUM('qualquer', 'manha', 'tarde', 'noite') NOT NULL DEFAULT 'qualquer'`
- `prioridade`: `ENUM('normal', 'urgente') NOT NULL DEFAULT 'normal'`
- `status`: `ENUM('aguardando', 'agendado', 'desistente') NOT NULL DEFAULT 'aguardando'`
- `data_solicitacao`: `DATE NOT NULL`
- `observacoes`: `TEXT NULL`
- `created_at`, `updated_at`: `TIMESTAMP NULL`
- **Índice:** `INDEX idx_espera_status (especialidade_id, status, prioridade)`

### 9. `auditorias`
Trilha de auditoria das ações do sistema.
- `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
- `user_id`: `BIGINT UNSIGNED NULL` -> `FOREIGN KEY REFERENCES users(id) ON DELETE SET NULL`
- `acao`: `VARCHAR(100) NOT NULL` (ex: `agendamento.criado`, `agendamento.cancelado`, `paciente.atualizado`)
- `tabela_afetada`: `VARCHAR(100) NOT NULL`
- `registro_id`: `BIGINT UNSIGNED NOT NULL`
- `dados_anteriores`: `JSON NULL`
- `dados_novos`: `JSON NULL`
- `ip_address`: `VARCHAR(45) NULL`
- `user_agent`: `TEXT NULL`
- `created_at`: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`