# Contexto e Regras do Projeto - CEO Digital (.cursorrules)

## Stack Tecnológica
- Backend: PHP 8.2+ | Laravel 13.x (Strict typing: declare(strict_types=1);)
- Banco de Dados: MySQL 8.0+
- Frontend: Blade Templates + Tailwind CSS + Alpine.js
- Padrões: PSR-12, Clean Code, Single Responsibility Principle

## Diretrizes de Arquitetura e Engenharia
1. NUNCA coloque regras de negócio pesadas dentro de Controllers, Rotas web.php ou Views Blade.
2. Toda lógica de validação de formulário DEVE residir em classes FormRequest dedicadas (`app/Http/Requests`).
3. Toda manipulação de dados complexa ou transações envolvendo múltiplos models DEVE ser encapsulada em Services (`app/Services`).
4. Utilize sempre `DB::transaction()` ao executar mutações conjuntas (ex: converter Demanda Reprimida em Agendamento).
5. Rotas administrativas DEVEM ser protegidas rigorosamente pelo middleware de perfil `admin` e pelas Policies correspondentes.
6. Tratamento de CPF: sanitize antes de validar e persistir. Armazene SEMPRE apenas os 11 dígitos numéricos (`VARCHAR(11)`).

## Convenções de Nomenclatura
- Models: PascalCase e singular (ex: `Paciente`, `DemandaReprimida`).
- Tabelas: snake_case e plural (ex: `pacientes`, `demanda_reprimida`).
- Controllers: PascalCase sufixado com Controller (ex: `AgendaController`).
- Migrations: snake_case descritivo padrão Laravel (ex: `create_agendamentos_table`).
- Services: PascalCase sufixado com Service (ex: `AgendamentoService`).

## Segurança e Performance
- Evite problemas de N+1 queries. Utilize Eager Loading (`with(['paciente', 'dentista', 'especialidade'])`) explicitamente nas queries de listagem e agenda.
- Utilize queries preparadas pelo Eloquent para evitar SQL Injection.
- Toda view pública ou privada deve contar com tags `@csrf` em formulários POST/PUT/DELETE.

## Fluxo de Trabalho e Execução
- Ao executar ou implementar qualquer item descrito no checklist `TASKS.md`, atualize o arquivo marcando a tarefa como concluída `[x]`.
- Mantenha respostas técnicas diretas, focadas no código solicitado e com comentários em português brasileiro.