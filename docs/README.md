# Beyond OS documentation

- `deployment/`: deployment and rollback procedures.
- `patch-notes/`: versioned change notes moved out of the web root.
- `security/`: shared security controls and release requirements.
- `rollback/`: historical source, excluded from clean deployment packages.

Runtime data, generated media, credentials, and user uploads do not belong in Git history.
