"""
SQLite database utilities for the backend.

Currently used for:
- Creating the `users` table
- Seeding a demo admin user on first run
"""

import sqlite3
from typing import Optional

from werkzeug.security import generate_password_hash

from detail.config import ADMIN_EMAIL, ADMIN_PASSWORD, DB_PATH


def connect_db() -> sqlite3.Connection:
    """
    Open a connection to the SQLite database.

    We set `row_factory` so rows behave like dicts (e.g. row["email"]).
    """
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db(conn: Optional[sqlite3.Connection] = None) -> None:
    """
    Initialize the database:
    - Create `users` table if it doesn't exist
    - Seed a demo admin user (first run only)
    """
    owns_conn = conn is None
    if owns_conn:
        conn = connect_db()

    assert conn is not None

    cur = conn.cursor()
    cur.execute(
        """
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
        """
    )

    cur.execute("SELECT COUNT(*) AS c FROM users")
    count = cur.fetchone()["c"]
    if count == 0:
        cur.execute(
            "INSERT INTO users (email, password_hash) VALUES (?, ?)",
            (ADMIN_EMAIL, generate_password_hash(ADMIN_PASSWORD)),
        )
        conn.commit()

        print("Seeded demo admin user:")
        print(f"  Email: {ADMIN_EMAIL}")
        print(f"  Password: {ADMIN_PASSWORD}")

    if owns_conn:
        conn.close()

