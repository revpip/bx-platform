# Business X-Ray Platform

The flagship WordPress plugin for the Business X-Ray ecosystem.

## Purpose

Business X-Ray helps owner-managed businesses identify hidden friction, wasted time, operational risk, weak systems, missed opportunities and practical next actions.

This repository contains the production plugin code for the Business X-Ray Platform.

## Planned Core Modules

- Organisations
- Contacts
- Assessments
- Scoring
- Recommendations
- Reports
- Tasks
- Timeline
- Client portal
- Website scanner
- AI assistance
- Settings
- Launch tools

## Repository Structure

```text
business-xray-platform.php
composer.json
src/
  Core/
  Admin/
  Organisations/
  Assessments/
  Reports/
  Tasks/
  Scanner/
  Settings/
assets/
  css/
  js/
templates/
docs/
tests/
```

## Development Principles

- WordPress-native where appropriate.
- Secure by default.
- Escape output, sanitise input.
- Use nonces for state-changing requests.
- Keep business logic out of templates.
- Keep prompts and report language configurable.
- Build for future SaaS/white-label use.

## Status

Sprint 1 foundation repository.
