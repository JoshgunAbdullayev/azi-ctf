# NovaBank - Core Banking Platform

## Overview

NovaBank is a licensed digital banking institution operating under BRSA (Banking Regulation and Supervision Agency - Turkey) regulatory framework since 2008. This document provides an overview of the platform's core banking operations and infrastructure.

## Banking Operations

### Retail Banking

- Current and savings accounts with tiered interest rate structures
- Consumer lending: personal loans, auto financing, mortgage products (up to 120 months)
- Credit and debit card issuance with contactless payment support
- Standing orders, direct debits, and automated bill payments
- Foreign currency accounts (USD, EUR, GBP)

### Corporate Banking

- Business accounts with multi-signatory authorization workflows
- Trade finance: letters of credit, bank guarantees, import/export financing
- Commercial lending, revolving credit facilities, and project financing
- Treasury and cash management solutions
- POS terminal integration for merchants

### Digital Channels

- Internet banking portal with real-time transaction processing
- Mobile banking application (iOS/Android) with biometric authentication
- Open Banking API integration (PSD2 compliant)
- SWIFT gpi, EFT, and RTGS payment rails

## Transaction Processing

All transactions follow a strict end-to-end workflow:

1. **Initiation** - Customer request via authenticated channel
2. **Validation** - Input sanitization, limit checks, and balance verification
3. **Screening** - AML/CFT and sanctions list filtering
4. **Authorization** - Multi-factor approval based on risk scoring
5. **Settlement** - Real-time or batch processing depending on rail
6. **Reconciliation** - Automated matching and exception handling
7. **Audit** - Immutable logging for regulatory compliance

Settlement windows: 08:30 - 17:30 (TRT, UTC+3)
SWIFT cut-off: 15:00 UTC | Instant payments: 24/7/365

## Core Infrastructure

| Component      | Details                        |
| -------------- | ------------------------------ |
| Runtime        | PHP 8.x                        |
| Database       | SQLite3 (WAL journaling)       |
| Authentication | Session-based + bcrypt hashing |
| Transport      | HTTPS / TLS 1.3                |
| Compliance     | BRSA, KVKK (GDPR), PCI-DSS L1  |

## Risk Management

- Real-time fraud detection engine with ML-based anomaly scoring
- Transaction velocity and frequency monitoring
- Device fingerprinting and geo-location analysis
- Adaptive multi-factor authentication for high-value operations
- 24/7 SOC monitoring with automated incident response

## Regulatory Compliance

NovaBank A.S. operates under BRSA license **#48291** and is covered by SDIF (Savings Deposit Insurance Fund). All eligible deposits are insured up to 400,000 TRY per customer per institution, in accordance with current regulations.

Annual external audit by KPMG Turkey. PCI-DSS Level 1 certified.

## Service Level Targets

| Metric                    | Target   |
| ------------------------- | -------- |
| System Availability       | 99.95%   |
| Transaction Latency (P99) | < 500ms  |
| RTO (Recovery Time)       | < 4 hrs  |
| RPO (Recovery Point)      | < 15 min |

## Contact

- Technical Support: technical@novabank.com.tr
- Security Incidents: security@novabank.com.tr
- General Inquiries: info@novabank.com.tr

## License

Proprietary software of NovaBank A.S. Unauthorized use, reproduction, or distribution is strictly prohibited. All rights reserved.

---

<!-- Internal QA - Build: 20260215-nightly-rc3 -->
<!-- Deploy hash: a4f8c2e19b -->
<!-- ENV: staging-internal | ref: ADNSU1{html_source_recon_91f2} -->
<!-- Migrated from legacy CMS (Jira: NBK-4821) -->
