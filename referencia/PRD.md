# Product Requirements Document (PRD) - CEO Digital

## 1. Visão Geral do Produto
O **CEO Digital** é um sistema web monolítico desenvolvido para informatizar e centralizar o agendamento de consultas especializadas do Centro de Especialidades Odontológicas (CEO). O sistema substitui os livros de registro em papel, controlando a distribuição de vagas por turnos, a absorção de demandas espontâneas/encaixes, o gerenciamento rigoroso da demanda reprimida (fila de espera) e o monitoramento do absenteísmo (faltas) por UBS de origem. Adequado para implantação em secretarias municipais de saúde, prioriza a estabilidade e rastreabilidade.

---

## 2. Perfis de Acesso e Matriz de Permissões (RBAC)

| Módulo / Recurso | Perfil Operador | Perfil Administrador |
| :--- | :---: | :---: |
| Landing Page Institucional | Visualizar | Visualizar |
| Dashboard Operacional | Visualizar métricas | Visualizar métricas |
| Gestão de UBSs | Visualizar / Cadastrar / Editar | CRUD Completo |
| Gestão de Pacientes | Visualizar / Cadastrar / Editar | CRUD Completo |
| Gestão de Especialidades | Apenas Visualizar | CRUD Completo |
| Gestão de Dentistas e Grades | Apenas Visualizar | CRUD Completo |
| Agenda (Agendar, Encaixar, Status) | Total Acesso Operacional | Total Acesso Operacional |
| Demanda Reprimida (Fila) | Criar / Promover / Cancelar | CRUD Completo |
| Relatórios Gerenciais | Gerar e Exportar (PDF) | Gerar e Exportar (PDF) |
| Gestão de Usuários do Sistema | ❌ Sem Acesso | CRUD Completo |
| Redefinição de Senhas | ❌ Sem Acesso | Total |
| Trilha de Auditoria | ❌ Sem Acesso | Visualização Completa |

---

## 3. Requisitos Funcionais por Módulo

### 3.1. Landing Page Institucional
- **RF01:** Exibir apresentação visual moderna sobre o CEO, missão e horários de funcionamento.
- **RF02:** Listar especialidades odontológicas ativas com descrição acessível.
- **RF03:** Exibir rodapé discreto com link para a tela de autenticação (`/login`).

### 3.2. Autenticação e Segurança
- **RF04:** Login via e-mail e senha com suporte a "Lembrar-me" e proteção contra brute-force (`throttle`).
- **RF05:** Controle rigoroso de acesso via Policies do Laravel baseado na role (`admin`, `operador`).

### 3.3. Dashboard Operacional
- **RF06:** Indicadores do dia atual: total de agendamentos no dia, total por turno (Manhã, Tarde, Noite), taxa de faltas do mês e pacientes em espera.
- **RF07:** Tabela com os atendimentos em andamento e próximos do turno corrente.

### 3.4. Módulo de UBS (Unidades Básicas de Saúde)
- **RF08:** Cadastro com os campos: `nome` (obrigatório, único), `endereco`, `diretor`, `contato`.

### 3.5. Módulo de Pacientes
- **RF09:** Cadastro completo: `cpf` (obrigatório, 11 dígitos, cálculo matemático de validação e único), `cns` (opcional, 15 dígitos), `nome_completo`, `data_nascimento`, `sexo` (M, F, Outro), `endereco`, `ubs_id` (origem), `telefone_1`, `telefone_2`, `nome_acs` (Agente Comunitário de Saúde).
- **RF10:** Busca rápida unificada por Nome, CPF ou Cartão SUS.

### 3.6. Módulo de Especialidades e Dentistas
- **RF11:** Cadastro de Especialidades Odontológicas (`nome`, `descricao`, `status_ativo`).
- **RF12:** Cadastro de Dentistas: `nome_completo`, `cro` (obrigatório/único), `especialidade_id`, `telefone`, `status_ativo`.
- **RF13:** Configuração de Grade Operacional por Dentista: definição dos dias da semana (Segunda a Sábado) e turnos (`manha`, `tarde`, `noite`) em que o profissional atua, com parametrização de capacidade padrão de vagas por turno.

### 3.7. Módulo de Agenda
- **RF14:** Estrutura baseada em slots de turnos operacionais:
  - **Manhã:** 08:00 às 12:00.
  - **Tarde:** 13:00 às 17:00.
  - **Noite:** 18:00 às 22:00.
  - **Dias:** Segunda-feira a Sábado.
- **RF15:** Tipo de Agendamento:
  - `Agendamento Normal`: paciente previamente encaminhado e marcado com antecedência.
  - `Encaixe`: inclusão extraordinária para o turno selecionado, permitido ultrapassar o teto sugerido do turno com aviso visual ao operador.
  - `Demanda Espontânea`: atendimento emergencial/triagem direta no mesmo dia.
- **RF16:** Fluxo de Status do Atendimento:
  - `Agendado` -> `Presente` -> `Em Atendimento` -> `Concluído` / `Falta` / `Cancelado`.
- **RF17:** Ordenação por ordem de chegada no turno quando marcado como `Presente`.

### 3.8. Demanda Reprimida (Lista de Espera)
- **RF18:** Registro de pacientes aguardando vaga por `especialidade_id`, `ubs_id`, `turno_preferencial` e data de entrada.
- **RF19:** Classificação de prioridade: `Normal` ou `Urgente`.
- **RF20:** Ação de promoção: converter registro da fila diretamente em um agendamento na agenda do profissional, marcando a fila como `Atendida`.

### 3.9. Relatórios Gerenciais
- **RF21:** Filtros universais: período (data inicial e final), especialidade, dentista, UBS de origem e status.
- **RF22:** Relatórios disponíveis:
  - Absenteísmo: índice e relação nominal de faltas por UBS e especialidade.
  - Produção: total de atendimentos concluídos por dentista e especialidade.
  - Demanda Reprimida: total acumulado aguardando chamada e tempo médio de espera.
- **RF23:** Exportação em formato de impressão otimizada e download em PDF (via DomPDF/Snappy).

### 3.10. Auditoria Interna (Exclusivo Admin)
- **RF24:** Registro imutável de ações sensíveis: quem criou, editou, cancelou ou alterou status de agendamentos e cadastros críticos, com snapshot do estado anterior e novo (JSON), endereço IP e timestamp.

---

## 4. Requisitos Não-Funcionais (RNF)
- **RNF01 - Performance:** Páginas da agenda e listagens devem carregar em menos de 800ms com índices compostos aplicados no MySQL.
- **RNF02 - Usabilidade:** Interface desktop responsiva, clean, construída com Tailwind CSS + Alpine.js.
- **RNF03 - Integridade de Dados:** Validação estrita de CPF impedindo strings duplicadas ou com formatação divergente (gravação limpa de 11 dígitos numéricos).
- **RNF04 - Isolamento:** Acesso a rotas administrativas protegido por middleware e policies no nível de controller/action.