# Checklist de Execução Sequencial para IA (TASKS)

## Fase 1: Setup do Ambiente e Migrations
- [x] 1.1 Configurar `.env` para MySQL 8.0, timezone `America/Maceio` e locale `pt_BR`.
- [x] 1.2 Criar migration da tabela `users` adicionando os campos `role` (`admin`, `operador`) e `status_ativo`.
- [x] 1.3 Criar migration da tabela `ubs` com índices e restrições.
- [x] 1.4 Criar migration da tabela `pacientes` com validações de unicidade de CPF.
- [x] 1.5 Criar migration da tabela `especialidades` e `dentistas`.
- [x] 1.6 Criar migration da tabela `dentista_grades` com restrição de chave única por dia e turno.
- [x] 1.7 Criar migration da tabela `agendamentos` com os devidos enums e índices compostos.
- [x] 1.8 Criar migration da tabela `demanda_reprimida`.
- [x] 1.9 Criar migration da tabela `auditorias`.
- [x] 1.10 Implementar seeders com especialidades odontológicas reais do CEO, 1 usuário `admin` padrão e UBSs de exemplo.

## Fase 2: Models, Relacionamentos e Observers
- [x] 2.1 Configurar Model `User` com `$fillable`, casts e método helper `isAdmin(): bool`.
- [x] 2.2 Configurar Model `Ubs` com relacionamento `hasMany(Paciente::class)`.
- [x] 2.3 Configurar Model `Paciente` com casts, mutator para sanitizar CPF (apenas dígitos) e relacionamentos.
- [x] 2.4 Configurar Models `Especialidade`, `Dentista` e `DentistaGrade`.
- [x] 2.5 Configurar Model `Agendamento` com scopes de filtro: `doDia()`, `porTurno()`, `porDentista()`.
- [x] 2.6 Configurar Model `DemandaReprimida` com método para marcação de atendimento.
- [x] 2.7 Criar Observer universal para disparar eventos de gravação automática na tabela `auditorias` para alterações sensíveis.

## Fase 3: Autenticação, Policies e Middlewares
- [x] 3.1 Instalar e configurar Breeze (Blade + Alpine).
- [x] 3.2 Criar Middleware `EnsureUserIsAdmin` para isolar rotas do perfil administrador.
- [x] 3.3 Configurar `UserPolicy` e registrar mapeamento.
- [x] 3.4 Customizar fluxo de login redirecionando para `/dashboard`.

## Fase 4: Form Requests e Camada de Negócios (Services)
- [x] 4.1 Criar regra de validação customizada para CPF brasileiro (`CpfRule`).
- [x] 4.2 Criar `PacienteFormRequest` com validação de CPF único.
- [x] 4.3 Criar `AgendamentoFormRequest` com validação do dia/turno do dentista.
- [x] 4.4 Criar `AgendamentoService` com métodos atômicos (`agendar`, `realizarEncaixe`, `promoverDemandaReprimida`, `atualizarStatusChegada`).
- [x] 4.5 Criar `RelatorioService` para agregação de estatísticas.

## Fase 5: Controllers e Rotas
- [x] 5.1 Implementar `LandingPageController`.
- [x] 5.2 Implementar `DashboardController`.
- [x] 5.3 Implementar CRUD de `UbsController`.
- [x] 5.4 Implementar CRUD de `PacienteController` com busca reativa.
- [x] 5.5 Implementar CRUDs de `EspecialidadeController` e `DentistaController`.
- [x] 5.6 Implementar `AgendaController`.
- [x] 5.7 Implementar `DemandaReprimidaController`.
- [x] 5.8 Implementar `RelatorioController` com view de impressão/PDF.
- [x] 5.9 Implementar `UserController` e `AuditoriaController` protegidos.

## Fase 6: Frontend Blade, Tailwind CSS e Alpine.js
- [x] 6.1 Construir layout público da Landing Page.
- [x] 6.2 Construir layout base do Painel Administrativo.
- [x] 6.3 Construir telas de listagem com componentes de tabela e badges de status.
- [x] 6.4 Implementar tela operacional da Agenda com abas (Manhã, Tarde, Noite).
- [x] 6.5 Adicionar modais em Alpine.js para cadastro de encaixe.
- [x] 6.6 Criar folha de estilos CSS para impressão de relatórios (`@media print`).

## Fase 7: Testes Automatizados
- [x] 7.1 Testar validação de CPF inválido e duplicado.
- [x] 7.2 Testar acesso de `operador` a rotas restritas.
- [x] 7.3 Testar cálculo de encaixe e log de auditoria.
- [x] 7.4 Testar conversão de registro da fila de espera.