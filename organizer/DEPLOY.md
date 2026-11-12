# NovaBank CTF — Təşkilatçı Deploy Bələdçisi

**GİZLİDİR — tərkibində bütün flag dəyərləri var. Yarışmaçılara göstərməyin.**

Bu sənəd tədbir üçün NovaBank CTF-in necə işə salınacağını, sıfırlanacağını, scoreboard-un idarə olunacağını və flag inventarını əhatə edir. Həll yolları üçün `CTF_WALKTHROUGH.md`-ə baxın.

---

## 1. Tələblər

- **PHP 8.0+** (PHP 8.x tövsiyə olunur), aşağıdakı extension-larla:
  - `pdo_sqlite` (SQLite dəstəyi)
  - `libxml` (XXE challenge-i üçün)
- Windows üzərində `C:\xampp\php\php.exe` istifadə edilib (XAMPP-ın PHP-si). Hər hansı PHP 8 CLI binary-si işləyər.
- Əlavə runtime asılılığı yoxdur (Composer/npm lazım deyil) — hamısı saf PHP + fayl-əsaslı SQLite.

---

## 2. İşə Salma

Layihə qovluğundan (`C:\cyberclub_ctf`):

```
C:\xampp\php\php.exe -S localhost:8080 serve.php
```

Sonra brauzerdə açın: `http://localhost:8080/`

- **Run faylı `serve.php`-dır** (köhnə sənədlərdə `router.php` yazılıb — bu artıq düzəldilib, amma diqqətli olun).
- `.claude/launch.json`-da eyni konfiqurasiya `novabank-ctf` adı ilə qeyd olunub (port 8080).
- Bütün database-lər (`database.sqlite`, `scoreboard/scoreboard.sqlite`, `nb-id-recover-a3f9/act2.sqlite`) **ilk HTTP request-də avtomatik yaranır və seed olunur**. Əlavə miqrasiya addımı lazım deyil.
- Tədbirdən əvvəl bir dəfə `http://localhost:8080/` açaraq bütün seed-lərin düzgün yarandığını yoxlayın (həmçinin `/scoreboard/` və `/nb-id-recover-a3f9/forgot.php` kimi alt-xidmətlərə bir sorğu göndərib onların da seed etdiyini yoxlayın).

### Tədbir günü üçün tövsiyə

- Serveri tədbirdən azı 15-20 dəqiqə əvvəl işə salın ki, bütün DB-lər seed olsun və siz sınaqdan keçirə biləsiniz.
- Serveri bir terminalda açıq saxlayın (`Ctrl+C` ilə dayandırmayın). Arıza halında restart edin — DB-lər artıq mövcud olduğu üçün seed təkrarlanmayacaq (sıfırlamaq lazımdırsa aşağıya baxın).

---

## 3. Sıfırlama Proseduru

Tam təzə başlanğıc üçün (məs. məşq raundundan sonra, əsl tədbirdən əvvəl):

1. Serveri dayandırın (`Ctrl+C`).
2. Bu faylları silin (olarsa — onların `-wal`/`-shm` yoldaşları da daxil):
   - `database.sqlite`, `database.sqlite-wal`, `database.sqlite-shm`
   - `scoreboard\scoreboard.sqlite` (+ `-wal`/`-shm`)
   - `nb-id-recover-a3f9\act2.sqlite` (+ `-wal`/`-shm`)
3. Serveri yenidən işə salın: `C:\xampp\php\php.exe -S localhost:8080 serve.php`
4. `http://localhost:8080/` açaraq əsas DB-nin yenidən seed olduğunu yoxlayın.

**Qeyd:** Yalnız `scoreboard.sqlite`-ı silmək scoreboard-u sıfırlayır (komandalar, submission-lar) — əsas tətbiq (`database.sqlite`) və ACT II (`act2.sqlite`) bundan təsirlənmir. Hər DB müstəqil sıfırlana bilər.

---

## 4. Scoreboard — İdarəetmə

- **Əsas URL:** `http://localhost:8080/scoreboard/` — challenge siyahısı/submission
- **Canlı lider taxtası:** `/scoreboard/scoreboard.php`
- **Komanda görünüşü:** `/scoreboard/team.php`
- **Admin panel:** `/scoreboard/admin.php`

### Admin parolunu DƏYİŞİN

Admin parolu `scoreboard/config.php` faylındakı `SB_ADMIN_PASSWORD` konstantıdır. **Default dəyər `Adnsu-SB-Admin#2026!x7`-dir — tədbirdən ƏVVƖL mütləq dəyişdirin.** Faylı açıb konstantı yeni, güclü bir parolla əvəz edin.

### Admin panelin imkanları

- Komandaların və submission-ların siyahısı
- Komanda sıfırlama və ya silmə
- Yarışı dondurma/bağlama (close/re-open)

### Xallandırma konfiqurasiyası

`scoreboard/config.php`-dəki `SB_TIER_POINTS`:

| Tier | Xal |
|------|-----|
| Easy | 50 |
| Medium | 150 |
| Hard | 300 |
| Expert | 700 |

Yanlış submission limiti: 8 cəhd / 60 saniyə / komanda.

---

## 5. Flag-ları Redaktə Etmə (`flags.json`)

Scoreboard-un submission məntiqi flag dəyərlərini **koddan deyil**, `scoreboard/flags.json` faylından oxuyur — **hər request-də**, deməli redaktələr **canlıdır** (server restartı lazım deyil).

Struktur:
```json
{"challenges":[
  {"number": 1, "title": "...", "category": "Recon", "tier": "Easy", "flag": "ADNSU1{...}"},
  ...
]}
```

- İstəyə bağlı sahələr: `points` (tier default-unu override edir), `hidden: true` (siyahıda göstərilməz/submit edilə bilməz).
- `number`, `flag` və ya `tier` olmayan entry-lər atlanılır.
- Bir flag-ın xalını artıq həll olunduqdan sonra dəyişmək, əvvəlki həll edənlərin aldığı xalı DƏYİŞMİR (hər submission öz vaxtının xalını saxlayır).
- Yeni flag əlavə etmək üçün sadəcə massivə yeni obyekt əlavə edin (bağlayıcı `]`-dan əvvəl, vergülləri unutmayın). Kodda heç bir dəyişiklik lazım deyil.

**ƏHƏMİYYƏTLİ:** `serve.php` artıq `/scoreboard/flags.json`-u bloklayır (403) ki, yarışmaçılar bu faylı birbaşa yükləyib bütün flag-ları görə bilməsinlər. Bunu yoxlayın: `curl http://localhost:8080/scoreboard/flags.json` → 403 qaytarmalıdır. Əgər 200 qaytarırsa, `serve.php`-dəki `$blocked` massivində bu qaydanın olduğunu təsdiqləyin:
```php
'#^/scoreboard/(flags\.json|inc/)#i',
```

---

## 6. Çoxlu İştirakçı üçün Hosting Qeydləri

Tək PHP built-in server (`php -S`) **single-threaded**-dir (xüsusən Windows-da) — çoxlu komanda eyni anda sorğu göndərəndə sıra ilə işlənir. Kiçik/orta tədbir (10-30 komanda) üçün adətən kifayətdir, amma nəzərə alın:

- **Tövsiyə olunan sadə yanaşma:** bir paylaşılan server (bu qovluqdan), bütün komandalar eyni `http://<server-ip>:8080/` ünvanına qoşulur. Sadədir, amma single-threaded olduğu üçün yüksək yüklənmədə gecikmə ola bilər (xüsusən FLAG 25 race-condition challenge-i zamanı — bu artıq nəzərə alınıb və tək-thread mühitdə də işləyir).
- **Alternativ (daha etibarlı, əgər vaxt/resurs varsa):** tətbiqi bir konteyner daxilində (məsələn Docker + `php:8-cli` baza image-i, `php -S 0.0.0.0:8080 serve.php` ilə) işə salıb, hər komandaya və ya komanda qruplarına ayrı instance vermək — bu, təcrid (isolation) və performansı yaxşılaşdırar. **Bu sənəd heç bir Dockerfile yaratmır** — yalnız bir istiqamət qeydidir; implementasiya lazım olarsa ayrıca planlaşdırılmalıdır.
- Hər halda, server(lər) yalnız yarışmaçıların şəbəkəsindən əlçatan olmalıdır, internetdən yox (əks halda kənar şəxslər də qoşula bilər).
- `logs/`, `includes/`, `private/` kimi qovluqları server-level (firewall/reverse-proxy) səviyyəsində əlavə qorumaq olar, amma `serve.php` onsuz da bunları bloklayır — əlavə qat kimi düşünün, əsas müdafiə kimi yox.

---

## 7. Tam Flag İnventarı (30 flag)

| # | Başlıq | Tier | Kateqoriya | Xal | Flag |
|---|--------|------|------------|-----|------|
| 1 | HTML Source Code Review | Easy | Recon | 50 | `ADNSU1{html_source_recon_91f2}` |
| 2 | IDOR + Log Analysis | Medium | Web | 150 | `ADNSU2{idor_account_data_leak_a7c4}` |
| 3 | SQL Injection (Blind) Auth Bypass | Medium | Web | 150 | `ADNSU3{sqli_auth_bypass_3d8e}` |
| 4 | LFI / Path Traversal | Medium | Web | 150 | `ADNSU4{lfi_private_file_disclosure_55ab}` |
| 5 | Rate Limit Bypass + Bruteforce | Medium | Web | 150 | `ADNSU5{rate_limit_bypass_bruteforce_c9f1}` |
| 6 | API Enumeration + Key Discovery | Medium | API | 150 | `ADNSU6{api_enumeration_access_bypass_6e2d}` |
| 7 | HMAC-SHA256 Token Forgery | Hard | Crypto | 300 | `ADNSU7{hmac_token_forgery_7c2d9a}` |
| 8 | Hex/XOR Cipher | Hard | Crypto | 300 | `ADNSU8{x0r_br34k_1t}` |
| 9 | GraphQL Introspection Abuse | Hard | API | 300 | `ADNSU9{graphql_introspection_abuse_88fa}` |
| 10 | OAuth Debug Header Leak | Hard | Web | 300 | `ADNSU10{oauth_email_verification_bypass_2aa7}` |
| 11 | SSRF → Internal Config → RCE Chain | Expert | Chain | 700 | `ADNSU11{ssrf_to_rce_exploit_chain_f13c}` |
| 12 | Mass Assignment | Medium | API | 150 | `ADNSU12{mass_assignment_privilege_escalation_7f3a}` |
| 13 | JWT Algorithm Confusion (alg:none) | Hard | Crypto | 300 | `ADNSU13{jwt_algorithm_none_bypass_4d2e}` |
| 14 | XXE (XML External Entity) | Hard | Web | 300 | `ADNSU14{xxe_external_entity_injection_9b1c}` |
| 15 | Cookie Tampering | Easy | Web | 50 | `ADNSU15{cookie_tampering_role_escalation_5e8d}` |
| 16 | Database Backup Forensics | Easy | Recon | 50 | `ADNSU16{database_backup_leak_forensics_8c3a}` |
| 17 | Debug Panel + API Key Correlation | Medium | Recon | 150 | `ADNSU17{debug_panel_api_key_exposure_3f7b}` |
| 18 | SSTI (Server-Side Template Injection) | Hard | Web | 300 | `ADNSU18{ssti_template_injection_code_exec_6d4f}` |
| 23 | AI Prompt Injection (BankGPT) | Medium | AI | 150 | `ADNSU23{ai_prompt_injection_data_exfil_c7d3}` |
| 25 | Race Condition Double Spending | Hard | Web | 300 | `ADNSU25{race_condition_double_spend_b4nk_8f2e}` |
| 26 | PHP Deserialization Gadget Chain | Expert | Chain | 700 | `ADNSU26{php_deserialization_gadget_chain_9e4f}` |
| 27 | SSRF via Webhook (Cloud Metadata Theft) | Hard | Cloud | 300 | `ADNSU27{ssrf_cloud_metadata_credential_theft_4a7b}` |
| 28 | PHP Type Juggling (Magic Hash) | Medium | Web | 150 | `ADNSU28{php_type_juggling_magic_hash_0e_bypass_d8f1}` |
| 29 | Prototype Pollution / Config Override | Hard | API | 300 | `ADNSU29{prototype_pollution_config_override_7c2a}` |
| 30 | Log Poisoning + LFI Chain | Expert | Chain | 700 | `ADNSU30{log_poisoning_lfi_rce_chain_e5a2}` |
| 31 | Host Header / Reset Poisoning (ATO 1/5) | Medium | ATO Chain | 150 | `ADNSU31{host_header_reset_poisoning_a3f9}` |
| 32 | Reset Token Account Takeover (ATO 2/5) | Medium | ATO Chain | 150 | `ADNSU32{reset_token_account_takeover_b7e4}` |
| 33 | MFA Client-Trust Header Bypass (ATO 3/5) | Hard | ATO Chain | 300 | `ADNSU33{mfa_client_trust_header_bypass_7c21}` |
| 34 | CORS Credentialed Origin Reflection (ATO 4/5) | Hard | ATO Chain | 300 | `ADNSU34{cors_credentialed_origin_reflection_5b8e}` |
| 35 | JWT kid Path Traversal / Key Confusion (ATO 5/5, FİNAL) | Expert | ATO Chain | 700 | `ADNSU35{jwt_kid_path_traversal_key_confusion_9d4c}` |

### Yekun cədvəl

| Tier | Flag sayı | Xal/flag | Cəm xal |
|------|-----------|----------|---------|
| Easy | 3 | 50 | 150 |
| Medium | 11 | 150 | 1650 |
| Hard | 12 | 300 | 3600 |
| Expert | 4 | 700 | 2800 |
| **CƏMİ** | **30** | | **8200** |

Bu dəyərlər `scoreboard/flags.json`-dakı real konfiqurasiya ilə üst-üstə düşməlidir — tədbirdən əvvəl yoxlayın (`number`, `tier`, `flag` sahələrini bu cədvəllə tutuşdurun).

---

## 8. Son Yoxlama Siyahısı (Tədbirdən Əvvəl)

- [ ] `serve.php` ilə server işə salındı, `http://localhost:8080/` açılır
- [ ] Bütün 3 SQLite DB avtomatik yarandı (əsas, scoreboard, act2)
- [ ] `scoreboard/config.php`-də `SB_ADMIN_PASSWORD` dəyişdirildi
- [ ] `curl http://localhost:8080/scoreboard/flags.json` → 403 (bloklanıb)
- [ ] `scoreboard/flags.json`-dakı bütün 30 flag yuxarıdakı cədvəllə üst-üstə düşür
- [ ] Flag 8-in dəyəri `ADNSU8{x0r_br34k_1t}`-dir (əvvəllər qırılmışdı, indi düzəldilib)
- [ ] ATO zənciri (31-35) üçün `nb-id-recover-a3f9/`, `nb-mfa-gw-7c21/`, `nb-cors-edge-5b8e/`, `nb-jwks-vault-9d4c/` qovluqları əlçatandır
- [ ] `robots.txt` və `serve.php`-dəki blok qaydaları yoxlanıldı (həssas fayllar 403 qaytarır)
- [ ] `COMPETITORS.md` yarışmaçılara paylaşıldı (bu sənəd — `DEPLOY.md` — YOX)
- [ ] Server yarışmaçıların şəbəkəsindən əlçatandır, açıq internetdən yox

---

*ASOIU CyberClub — Təşkilatçı daxili sənədi. Bu faylı yarışmaçılarla paylaşmayın.*
