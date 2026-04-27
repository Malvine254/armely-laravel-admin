MariaDB Aria Recovery for XAMPP

Summary
- Steps used to diagnose and repair Aria corruption in the `mysql` system database on a Windows XAMPP install.
- Keep this file as the canonical reference for future recoveries.

Prerequisites
- XAMPP installed (MySQL/MariaDB binaries at `C:\xampp\mysql\bin`).
- Admin access to stop/start XAMPP services and access filesystem.

High-level steps
1. Stop MySQL in the XAMPP Control Panel.
2. Backup the `data` directory (very important):

   Open an elevated Command Prompt (cmd.exe) and run:

   ```cmd
   robocopy "C:\xampp\mysql\data" "C:\xampp\mysql\data_backup_YYYYMMDD" /MIR
   ```

   Or copy the folder manually to a safe location.

3. Run `mysqlcheck` to find and attempt automated repairs (run from cmd.exe to avoid PowerShell quoting issues):

   ```cmd
   cd C:\xampp\mysql\bin
   mysqlcheck.exe -u root --auto-repair --databases mysql --force
   ```

   Note which tables are reported as `Corrupt` (e.g. `mysql.db`, `mysql.global_priv`, `mysql.roles_mapping`, etc.).

4. Use `aria_chk` to recover `.MAI` Aria index/data files flagged as corrupt. For each corrupted table file (example `db.MAI`):

   ```cmd
   aria_chk.exe --recover --force "C:\xampp\mysql\data\mysql\db.MAI"
   aria_chk.exe --recover --force "C:\xampp\mysql\data\mysql\roles_mapping.MAI"
   ```

   Repeat for each `.MAI` file `mysqlcheck` flagged.

5. Re-run `mysqlcheck` to confirm the repairs:

   ```cmd
   mysqlcheck.exe -u root --auto-repair --databases mysql --force
   ```

6. Start MySQL from XAMPP Control Panel and verify connectivity. If `root@localhost` or other users are missing, recreate them after successful repair.

Commands used during troubleshooting (reference)
- `mysqlcheck.exe -u root --auto-repair --databases mysql --force`
- `aria_chk.exe --recover --force <full-path-to-.MAI>`

If recovery fails
- Restore `C:\xampp\mysql\data\mysql` from a known-good backup if available.
- If no backup exists and Aria is irrecoverable, export what you can (InnoDB .ibd recovery is out of scope here), reinstall MariaDB/XAMPP, then import SQL dumps if you have them.

Notes & precautions
- Always backup `C:\xampp\mysql\data` before running recovery operations.
- Prefer cmd.exe (XAMPP shell) on Windows to avoid quoting/argument parsing problems.
- When `mysqld` is run with `--skip-grant-tables`, `CREATE USER` / `GRANT` statements will fail; use `--init-file` only when the server is not in skip mode and you need to run SQL at startup.

Contacts
- If you want, I can commit this file to git and open a PR, or run the recovery steps interactively next time you need them.
