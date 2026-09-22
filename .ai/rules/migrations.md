---
paths:
  - 'database/migrations/**'
---

# Migrations

## One create migration per table; edit it in place pre-deploy
Migrations were consolidated to one `create_<table>_table` file per table (0001_01_01_000010..000035, ordered for FK dependencies; framework defaults users/cache/jobs keep Laravel's stock layout). The old monolithic 0001_01_01_000006_create_tables.php and the 2026_09_* add_soft_deletes_and_blameable_* follow-ups no longer exist — their columns are folded into each table's create migration. While the app is undeployed, change a table's schema by editing its create migration and running migrate:fresh; older rule notes mentioning follow-up migrations or create_tables are historical. New tables get a new file placed after their FK dependencies.
