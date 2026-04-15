"""
Configuration for the Elite Print backend.

Environment variables:
- `ELITEPRINT_SECRET_KEY` (Flask session signing)
- `ELITEPRINT_ADMIN_EMAIL` (seeded demo user)
- `ELITEPRINT_ADMIN_PASSWORD` (seeded demo user)

For local development, we provide defaults if env vars are missing.
"""

import os


def project_base_dir() -> str:
    # `detail/` is inside the project root; we want the root directory.
    return os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


BASE_DIR = project_base_dir()
DB_PATH = os.path.join(BASE_DIR, "eliteprint.db")

APP_SECRET_KEY = os.environ.get("ELITEPRINT_SECRET_KEY", "change-me-dev-only")
ADMIN_EMAIL = os.environ.get("ELITEPRINT_ADMIN_EMAIL", "admin@eliteprint.local").strip().lower()
ADMIN_PASSWORD = os.environ.get("ELITEPRINT_ADMIN_PASSWORD", "Admin@123456")

