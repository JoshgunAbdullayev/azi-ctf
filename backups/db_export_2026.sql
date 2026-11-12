-- NovaBank Database Export
-- Generated: 2026-02-15 03:00:00 UTC
-- Server: production-db-01
-- Database: novabank_core

-- ============================================
-- TABLE: system_config (partial export)
-- ============================================

CREATE TABLE IF NOT EXISTS system_config (
    id INTEGER PRIMARY KEY,
    config_key TEXT NOT NULL,
    config_value TEXT NOT NULL,
    is_secret INTEGER DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO system_config VALUES (1, 'app_name', 'NovaBank Core Banking', 0, '2024-01-15 09:00:00');
INSERT INTO system_config VALUES (2, 'app_version', '3.2.1-beta', 0, '2026-02-15 11:00:00');
INSERT INTO system_config VALUES (3, 'session_lifetime', '1440', 0, '2024-01-15 09:00:00');
INSERT INTO system_config VALUES (4, 'rate_limit_max', '100', 0, '2024-01-15 09:00:00');
INSERT INTO system_config VALUES (5, 'smtp_host', 'smtp.novabank.com.tr', 0, '2024-06-20 14:00:00');
INSERT INTO system_config VALUES (6, 'smtp_port', '587', 0, '2024-06-20 14:00:00');
INSERT INTO system_config VALUES (7, 'backup_encryption_key', 'NB_BKP_2026_AES256_K3Y!', 1, '2026-01-01 00:00:00');
-- REDACTED: config_value for secret entries
INSERT INTO system_config VALUES (8, 'jwt_signing_key', '[REDACTED]', 1, '2025-08-10 10:00:00');
INSERT INTO system_config VALUES (9, 'hmac_secret', '[REDACTED]', 1, '2025-08-10 10:00:00');
INSERT INTO system_config VALUES (10, 'api_master_key', 'nb_internal_api_8f3k2m9x', 1, '2025-11-20 16:30:00');
INSERT INTO system_config VALUES (11, 'vault_access_seed', 'YTg0MzhkYTc4ZTY3OWY0NGE1Y2ZmOWU0NGViYWNmYmQ=', 1, '2026-02-20 09:00:00');

-- ============================================
-- TABLE: admin_sessions (recent entries)
-- ============================================

CREATE TABLE IF NOT EXISTS admin_sessions (
    id INTEGER PRIMARY KEY,
    admin_id INTEGER,
    session_token TEXT,
    ip_address TEXT,
    user_agent TEXT,
    created_at DATETIME,
    expires_at DATETIME
);

INSERT INTO admin_sessions VALUES (1, 1, 'sess_root_a4f8c2e19b3d7f', '10.0.1.5', 'Mozilla/5.0 Internal Admin Browser', '2026-02-28 09:00:00', '2026-02-28 17:00:00');
INSERT INTO admin_sessions VALUES (2, 2, 'sess_hakan_7b2c9d4e5f1a3', '10.0.1.12', 'Mozilla/5.0 Priority Admin Client', '2026-02-27 14:30:00', '2026-02-27 22:30:00');

-- ============================================
-- TABLE: audit_trail (flagged entries)
-- ============================================

CREATE TABLE IF NOT EXISTS audit_trail (
    id INTEGER PRIMARY KEY,
    action TEXT,
    actor TEXT,
    target TEXT,
    details TEXT,
    severity TEXT,
    created_at DATETIME
);

INSERT INTO audit_trail VALUES (1, 'CONFIG_CHANGE', 'root_admin', 'masak_auto_report', 'Changed from enabled to disabled', 'CRITICAL', '2025-09-01 02:30:00');
INSERT INTO audit_trail VALUES (2, 'TRANSFER_OVERRIDE', 'root_admin', 'NBK-CORP-044', 'Bypassed AML screening for 245000 EUR transfer', 'CRITICAL', '2025-10-02 14:22:00');
INSERT INTO audit_trail VALUES (3, 'LOG_ROTATION', 'system', 'audit_trail', 'Forced log rotation - initiated by root_admin', 'HIGH', '2026-01-15 23:59:59');
INSERT INTO audit_trail VALUES (4, 'BACKUP_ACCESS', 'unknown', 'db_export_2026.sql', 'Unauthorized access to backup file', 'CRITICAL', '2026-03-01 04:15:00');

-- ============================================
-- HIDDEN NOTE (for DB forensics)
-- ============================================
-- During the Q4 2025 internal audit, it was discovered that
-- the MASAK auto-reporting feature was deliberately disabled
-- by an administrator. The encoded evidence reference is:
-- Hex: 41 44 4e 53 55 31 36 7b 64 61 74 61 62 61 73 65
--      5f 62 61 63 6b 75 70 5f 6c 65 61 6b 5f 66 6f 72
--      65 6e 73 69 63 73 5f 38 63 33 61 7d
-- This file should have been encrypted before storage.
-- END OF EXPORT
