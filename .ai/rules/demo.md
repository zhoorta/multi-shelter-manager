---
paths:
  - 'app/Actions/Demo/**'
---

# Demo

## Demo sandboxes copy the template shelter table by table
DEMO_MODE=true (config/demo.php) gives each visitor a throwaway shelter: DemoSandbox::create() copies DEMO_TEMPLATE_SHELTER_ID (the shelter DocumentationDemoSeeder creates) through the ordered TABLES map, with new ids and copied files, and a manager user @demo.focinhos.invalid; demo:prune (every 10 min) deletes it all. A NEW shelter-owned table or file column must be added to DemoSandbox::TABLES or it will not be copied/purged (an orphan row breaks the user delete through created_by FKs). Demo users are blocked by BlockInDemo from profile, security, export and user invitations. demo:prepare seeds once and randomises the example passwords. Run with PUBLIC_PORTAL_ENABLED=false. Image: Dockerfile + docker-compose.demo.yml (SQLite in /data, dev deps kept for Faker).
