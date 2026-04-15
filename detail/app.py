"""
Flask application factory for the Elite Print site.

Responsibilities:
- Serve your existing HTML templates (`/index.html`, `/about.html`, etc.)
- Serve static assets from `/assets/*` (CSS/JS/images)
- Provide SQLite-backed login at `/login` (frontend POSTs to this route)
"""

import os
import sqlite3
from datetime import timedelta

from flask import Flask, g, redirect

from detail.auth import create_auth_blueprint
from detail.config import APP_SECRET_KEY, DB_PATH
from detail.db import init_db
from detail.about import create_about_blueprint
from detail.clients import create_clients_blueprint
from detail.contact import create_contact_blueprint
from detail.faq import create_faq_blueprint
from detail.index import create_index_blueprint
from detail.login import create_login_blueprint
from detail.product import create_product_blueprint


def create_app() -> Flask:
    """
    Create and configure the Flask app instance.

    Notes:
    - `base_dir` points to the project root, so `render_template("index.html")`
      finds your existing HTML files without changing the UI.
    - DB is initialized (and seeded) once at startup.
    """
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

    app = Flask(
        __name__,
        static_folder=os.path.join(base_dir, "assets"),
        static_url_path="/assets",
        template_folder=base_dir,
    )

    app.secret_key = APP_SECRET_KEY
    app.permanent_session_lifetime = timedelta(days=7)

    @app.before_request
    def _db_connect():
        g.db = sqlite3.connect(DB_PATH)
        g.db.row_factory = sqlite3.Row

    @app.teardown_request
    def _db_close(_exc):
        db = getattr(g, "db", None)
        if db is not None:
            db.close()

    # Seed DB once at startup (creates table + demo admin user).
    init_db()

    # Each page is served by its own blueprint.
    app.register_blueprint(create_index_blueprint())
    app.register_blueprint(create_about_blueprint())
    app.register_blueprint(create_product_blueprint())
    app.register_blueprint(create_clients_blueprint())
    app.register_blueprint(create_faq_blueprint())
    app.register_blueprint(create_contact_blueprint())
    app.register_blueprint(create_login_blueprint())
    app.register_blueprint(create_auth_blueprint())

    @app.route("/")
    def root():
        return redirect("/index.html")

    return app

