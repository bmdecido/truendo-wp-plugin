# Implementation Plan: Google Consent Mode v2 Integration

**Branch**: `002-consent-mode-integration` | **Date**: 2025-09-15 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/002-consent-mode-integration/spec.md`

## Execution Flow (/plan command scope)
```
1. Load feature spec from Input path
   → If not found: ERROR "No feature spec at {path}"
2. Fill Technical Context (scan for NEEDS CLARIFICATION)
   → Detect Project Type from context (web=frontend+backend, mobile=app+api)
   → Set Structure Decision based on project type
3. Evaluate Constitution Check section below
   → If violations exist: Document in Complexity Tracking
   → If no justification possible: ERROR "Simplify approach first"
   → Update Progress Tracking: Initial Constitution Check
4. Execute Phase 0 → research.md
   → If NEEDS CLARIFICATION remain: ERROR "Resolve unknowns"
5. Execute Phase 1 → contracts, data-model.md, quickstart.md, agent-specific template file (e.g., `CLAUDE.md` for Claude Code, `.github/copilot-instructions.md` for GitHub Copilot, or `GEMINI.md` for Gemini CLI).
6. Re-evaluate Constitution Check section
   → If new violations: Refactor design, return to Phase 1
   → Update Progress Tracking: Post-Design Constitution Check
7. Plan Phase 2 → Describe task generation approach (DO NOT create tasks.md)
8. STOP - Ready for /tasks command
```

**IMPORTANT**: The /plan command STOPS at step 7. Phases 2-4 are executed by other commands:
- Phase 2: /tasks command creates tasks.md
- Phase 3-4: Implementation execution (manual or via tools)

## Summary

This implementation plan successfully designs Google Consent Mode v2 integration for the TRUENDO WordPress plugin. The solution:

**Architecture**: Extends existing WordPress plugin architecture with minimal changes, adding 3 new settings and 2 new script injection methods following established patterns.

**Key Features**:
- Toggle-based activation with conditional admin interface
- 9 Google consent categories with individual granted/denied defaults
- Configurable wait timeout (500-5000ms) before applying defaults
- Script injection before TRUENDO CMP (priority 5 vs 10) for proper initialization order
- Full backward compatibility with existing plugin functionality

**Technical Approach**: Leverages WordPress Settings API, mirrors existing script injection patterns, reuses page builder compatibility logic, and maintains security through validated sanitization callbacks.

**Implementation Ready**: All technical unknowns resolved, contracts defined, data model specified, and development patterns established. Ready for task generation and implementation phases.

## Technical Context
**Language/Version**: PHP 7.4+ (WordPress compatibility requirement)
**Primary Dependencies**: WordPress Core, WordPress Settings API
**Storage**: WordPress database options table (wp_options)
**Testing**: PHPUnit or WordPress test framework
**Target Platform**: WordPress websites (PHP-based CMS)
**Project Type**: single (WordPress plugin extending existing functionality)
**Performance Goals**: Minimal impact on page load time
**Constraints**: Must follow WordPress Coding Standards, maintain backward compatibility
**Scale/Scope**: Single WordPress plugin with admin interface and frontend script injection

**Additional Technical Context**: Use the existing plugin architecture. Copy the existing logic for inject the CMP script and use it to also inject the consent mode script in a similar way. The actual consent mode script that interfaces with the main cmp script already exist (will be provided later), this script will use the default values defined in the plugin to handle the default consent states, if this is not configured the state should fallback to "denied". The actual callback functionality to handle consent updates has also been completed and will be provided in the Consent mode script. This plugin will only focus on injecting the consent mode script and appending the default consent values to the injected script. Try not to stray from the existing tools, architecture, framework and conventions.

## Constitution Check
*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

**Simplicity**:
- Projects: 1 (WordPress plugin extending existing functionality)
- Using framework directly? (Yes - WordPress APIs directly)
- Single data model? (Yes - consent mode configuration stored in wp_options)
- Avoiding patterns? (Yes - following WordPress plugin patterns, no complex abstractions)

**Architecture**:
- EVERY feature as library? (N/A - WordPress plugin architecture, following existing patterns)
- Libraries listed: N/A (extending existing WordPress plugin)
- CLI per library: N/A (WordPress admin interface instead)
- Library docs: Following WordPress documentation standards

**Testing (NON-NEGOTIABLE)**:
- RED-GREEN-Refactor cycle enforced? (Yes - will create failing tests first)
- Git commits show tests before implementation? (Yes - TDD approach)
- Order: Contract→Integration→E2E→Unit strictly followed? (Yes)
- Real dependencies used? (Yes - actual WordPress environment)
- Integration tests for: new admin fields, script injection, option storage? (Yes)
- FORBIDDEN: Implementation before test, skipping RED phase (Acknowledged)

**Observability**:
- Structured logging included? (WordPress debug logging where applicable)
- Frontend logs → backend? (N/A - frontend script handles its own logging)
- Error context sufficient? (Yes - WordPress error handling patterns)

**Versioning**:
- Version number assigned? (Will follow WordPress plugin versioning)
- BUILD increments on every change? (Yes)
- Breaking changes handled? (Yes - backward compatibility maintained)

## Project Structure

### Documentation (this feature)
```
specs/002-consent-mode-integration/
├── plan.md              # This file (/plan command output)
├── research.md          # Phase 0 output (/plan command)
├── data-model.md        # Phase 1 output (/plan command)
├── quickstart.md        # Phase 1 output (/plan command)
├── contracts/           # Phase 1 output (/plan command)
└── tasks.md             # Phase 2 output (/tasks command - NOT created by /plan)
```

### Source Code (repository root)
```
# WordPress Plugin Structure (existing)
truendo-wp-plugin/
├── truendo.php                    # Main plugin file
├── includes/
│   ├── class-truendo.php         # Core class
│   ├── class-truendo-loader.php  # Hook loader
│   └── truendo_contactform.php   # Contact form integration
├── admin/
│   ├── class-truendo-admin.php   # Admin functionality
│   └── partials/
│       └── truendo-admin-display.php # Admin settings page
├── public/
│   └── class-truendo-public.php  # Frontend functionality
├── rules.json                    # Cookie blocking rules
└── uninstall.php                # Cleanup logic
```

**Structure Decision**: Single project (extending existing WordPress plugin architecture)

## Phase 0: Outline & Research
1. **Extract unknowns from Technical Context** above:
   - Research WordPress Settings API best practices for complex form fields
   - Research script injection patterns in WordPress plugins
   - Research consent mode script integration patterns

2. **Generate and dispatch research agents**:
   - Task: "Research WordPress Settings API for toggle and grouped checkbox controls"
   - Task: "Find best practices for conditional admin form fields in WordPress"
   - Task: "Research Google Consent Mode v2 script implementation patterns"
   - Task: "Analyze existing TRUENDO plugin script injection logic"

3. **Consolidate findings** in `research.md` using format:
   - Decision: [what was chosen]
   - Rationale: [why chosen]
   - Alternatives considered: [what else evaluated]

**Output**: research.md with all technical approaches resolved

## Phase 1: Design & Contracts
*Prerequisites: research.md complete*

1. **Extract entities from feature spec** → `data-model.md`:
   - Consent Mode Configuration entity with fields and validation rules
   - Consent Category entities with granted/denied states
   - Wait Timeout Setting with integer validation

2. **Generate API contracts** from functional requirements:
   - WordPress Settings API contracts for new fields
   - Script injection contracts for consent mode script
   - Output WordPress-specific schemas to `/contracts/`

3. **Generate contract tests** from contracts:
   - Admin form rendering tests
   - Option persistence tests
   - Script injection tests
   - Tests must fail (no implementation yet)

4. **Extract test scenarios** from user stories:
   - Each acceptance scenario → integration test
   - Quickstart test = admin configuration workflow

5. **Update agent file incrementally** (O(1) operation):
   - Run `/scripts/powershell/update-agent-context.ps1 -AgentType claude` for Claude Code
   - Add consent mode implementation context
   - Preserve existing TRUENDO plugin context
   - Update with new technical decisions

**Output**: data-model.md, /contracts/*, failing tests, quickstart.md, CLAUDE.md updates

## Phase 2: Task Planning Approach
*This section describes what the /tasks command will do - DO NOT execute during /plan*

**Task Generation Strategy**:
- Load `/templates/tasks-template.md` as base
- Generate tasks from WordPress plugin modification patterns
- Each new admin field → settings registration task [P]
- Each new option → persistence test task [P]
- Each user story → integration test task
- Script injection tasks following existing patterns
- Implementation tasks to make tests pass

**Ordering Strategy**:
- TDD order: Tests before implementation
- WordPress dependency order: Settings registration → Admin UI → Script injection
- Mark [P] for parallel execution (independent settings)

**Estimated Output**: 20-25 numbered, ordered tasks in tasks.md

**IMPORTANT**: This phase is executed by the /tasks command, NOT by /plan

## Phase 3+: Future Implementation
*These phases are beyond the scope of the /plan command*

**Phase 3**: Task execution (/tasks command creates tasks.md)
**Phase 4**: Implementation (execute tasks.md following constitutional principles)
**Phase 5**: Validation (run tests, execute quickstart.md, performance validation)

## Complexity Tracking
*No constitutional violations identified*

## Progress Tracking
*This checklist is updated during execution flow*

**Phase Status**:
- [x] Phase 0: Research complete (/plan command)
- [x] Phase 1: Design complete (/plan command)
- [x] Phase 2: Task planning complete (/plan command - describe approach only)
- [ ] Phase 3: Tasks generated (/tasks command)
- [ ] Phase 4: Implementation complete
- [ ] Phase 5: Validation passed

**Gate Status**:
- [x] Initial Constitution Check: PASS
- [x] Post-Design Constitution Check: PASS
- [x] All NEEDS CLARIFICATION resolved
- [x] Complexity deviations documented

---
*Based on Constitution v2.1.1 - See `/memory/constitution.md`*