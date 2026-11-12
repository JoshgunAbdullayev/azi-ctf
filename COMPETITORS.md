# NovaBank CTF — Yarışmaçı Bələdçisi

ASOIU / ADNSU CyberClub Red Team Təşkilatı

---

## Ssenari

**NovaBank** — bank lisenziyası olduğunu iddia edən, tam funksional görünən bir **saxta rəqəmsal bank platformasıdır**. Sizin roluniz: NovaBank-ın icazəli bir **penetration tester**-isiniz. Tapşırığınız — tətbiqi, API-lərini və əlaqəli infrastrukturunu real bir pentest kimi araşdırmaq, zəiflikləri tapmaq və sübut etmək (flag-lar vasitəsilə).

Bu, HackTheBox / TryHackMe üslubunda **zəncirli (chained)** bir CTF-dir: heç bir addımda sizə birbaşa ipucu verilmir. Hər tapıntı növbəti addıma aparan bir açardır. Diqqətli olun, hər şeyi qeyd edin, və sistematik işləyin.

---

## Necə Başlamaq Olar

1. **Tətbiqə qoşulun:** təşkilatçının verdiyi ünvan (məs. `http://<server-ip>:8080/`).
2. **Komanda qeydiyyatı:** `http://<server-ip>:8080/scoreboard/` ünvanına gedin, komandanızı qeydiyyatdan keçirin.
3. **Lider taxtası:** `http://<server-ip>:8080/scoreboard/scoreboard.php` — canlı xallar, first-blood izi.
4. **Öz komandanız:** `http://<server-ip>:8080/scoreboard/team.php` — həll etdiyiniz flag-ları görün.
5. **Flag submit:** tapdığınız flag-ı scoreboard-dakı müvafiq challenge sahəsinə yapıştırıb göndərin.

**Flag Formatı:** `ADNSU{N}{description_text}` — məsələn `ADNSU1{...}`. Yalnız tam formatı (mötərizələr daxil) daxil edin.

---

## Nə Qədər Flag Var?

**Cəmi 30 flag**, aşağıdakı kateqoriyalarda yayılmış:

- **Recon** — açıq mənbə kəşfiyyatı, source review, fayl/qovluq tapılması
- **Web** — klassik veb zəiflikləri (auth bypass, injection, IDOR, XSS/SSTI növü problemlər, və s.)
- **API** — REST/GraphQL enumerasiya, sxem analizi, parametr manipulyasiyası
- **Crypto** — şifrələmə/hash zəiflikləri, token forgery, encoding analizi
- **AI** — süni intellekt əsaslı funksionallığın (chatbot) təhlükəsizlik testi
- **Cloud** — bulut-tipli metadata və SSRF-yə bağlı ssenarilər
- **Chain** — çoxaddımlı exploit zəncirləri (bir zəiflik növbətisinə aparır)
- **ATO Chain** — tam bir "Account Takeover" kill-chain: 5 ardıcıl flag, hər biri əvvəlkinin nəticəsindən asılıdır

Flag-lar **Easy / Medium / Hard / Expert** çətinlik dərəcələrinə bölünüb və buna uyğun xal daşıyır (aşağıya baxın).

---

## Xallandırma

| Çətinlik | Xal (flag başına) |
|----------|--------------------|
| Easy     | 50                 |
| Medium   | 150                |
| Hard     | 300                |
| Expert   | 700                |

- **First-blood:** bir flag-ı birinci tapan komanda izlənilir və lider taxtasında qeyd olunur (əlavə nüfuz üçün — yarışın adət-ənənəsinə uyğun şəkildə).
- Yanlış submission-lar üçün sürət limiti var — təsadüfi brute-force etməyin, düşünülmüş cəhd edin.
- Flag-ı tapmaq elan kimi sayılır: onu başqa heç bir komandaya göstərməyin və ya paylaşmayın (aşağıdaki qaydalara baxın).

---

## İcazə Verilən Alətlər

Bu CTF real pentest alətləri ilə həll olunmaq üçün nəzərdə tutulub:

- **Burp Suite** (Community Edition kifayətdir) — request/response analizi, proxy, repeater
- **curl / Postman / HTTPie** — manual API testing
- **Python** (requests, pwntools, və s.) — skript yazma, kriptoqrafik hesablamalar
- **CyberChef** — encoding/decoding, hash/cipher analiz
- **dirb / gobuster / ffuf** — directory/fayl brute-force
- **Browser DevTools** — cookie/header/source analizi
- İstədiyiniz digər standart, qanuni pentest alətləri (nmap, sqlmap və s. — amma hədəfə ziyan vermədən, aşağıdaki qaydalara baxın)

---

## İştirak Qaydaları (Rules of Engagement)

Bu qaydaların pozulması diskvalifikasiya ilə nəticələnə bilər.

1. **Əhatə dairəsi (Scope):** Yalnız sizə verilən NovaBank tətbiqi və onun aşkar etdiyiniz alt-xidmətləri (eyni server/port üzərində) əhatə dairəsindədir. Scoreboard platformasının özünə (infrastruktur, server, digər komandaların hesabları) hücum etmək **qətiyyən qadağandır**.
2. **DoS qadağandır:** Serveri çökdürməyə/yavaşlatmağa yönəlmiş kobud brute-force, flood, və ya resurs-tükəndirici hücumlar qadağandır. Rate-limit-lərlə ağıllı işləyin, kobud şəkildə yox.
3. **Yalnız öz mühitiniz:** Scoreboard-da yalnız öz komandanızın hesabı ilə işləyin. Digər komandaların sessiyalarına, cookie-lərinə və ya hesablarına müdaxilə etməyin.
4. **Flag paylaşımı qadağandır:** Flag-ları, həll yollarını və ya ipucu-larını başqa komandalarla (yaxud ictimai kanallarda) paylaşmaq qadağandır.
5. **İnfrastruktura hücum yoxdur:** Təşkilatçı serverinə, şəbəkəyə, Wi-Fi-a, digər iştirakçıların cihazlarına hücum etmək qadağandır — yalnız verilən tətbiq əhatə dairəsindədir.
6. **Sosial mühəndislik qadağandır:** Təşkilatçılara və ya digər komandalara qarşı sosial mühəndislik (fişinq, saxtakarlıq və s.) qadağandır.
7. **Problem tapsanız bildirin:** Əgər CTF-dən kənar (infrastruktur səviyyəsində) real bir problem tapsanız, dərhal təşkilatçılara bildirin — istismar etməyin.

---

## Məsləhətlər

- **Hər şeyi oxuyun:** HTML source, HTTP başlıqları (header-lar), cookie-lər, robots.txt, HTTP cavablarının tam məzmunu.
- **Qeyd aparın:** tapdığınız hər credential, token, endpoint və ID-ni bir yerdə saxlayın — çox şey sonradan lazım olacaq.
- **Zəncirə fikir verin:** bir flag adətən tək başına deyil, növbəti addım üçün açar da verir (yeni endpoint, credential, token və s.).
- **Standart metodologiyaya əməl edin:** recon → enumeration → exploitation → post-exploitation. Tələsməyin.
- **ATO Chain** kateqoriyasındakı 5 flag **ciddi ardıcıldır** — onlara giriş nöqtəsini tapmaq üçün əsas tətbiqi diqqətlə araştırmaq lazımdır.

Uğurlar!
