"""
Authentication blueprint.

Endpoints:
- POST `/login`  : Validate credentials against SQLite (`users` table).
- POST/GET `/logout`: Clear session and redirect to `/login.html`.
"""

from flask import Blueprint, g, redirect, request, session, url_for
from werkzeug.security import check_password_hash

from detail.login import render_login_page


def create_auth_blueprint() -> Blueprint:
    """Create and return the auth blueprint with login/logout routes."""
    bp = Blueprint("auth", __name__)

    @bp.route("/login", methods=["POST"])
    def login_post():
        """Handle login form POST and create a session on success."""
        email = (request.form.get("email") or "").strip().lower()
        password = request.form.get("password") or ""

        if not email or len(password) < 8:
            return render_login_page(error="Use a valid email and a password of at least 8 characters."), 400

        cur = g.db.cursor()
        cur.execute("SELECT * FROM users WHERE email = ?", (email,))
        user = cur.fetchone()

        if user is None or not check_password_hash(user["password_hash"], password):
            return render_login_page(error="Invalid email or password."), 401

        session.permanent = True
        session["user_email"] = email
        return redirect("/index.html")

    @bp.route("/logout", methods=["POST", "GET"])
    def logout():
        """Clear the session and redirect to the login page."""
        session.clear()
        return redirect("/login.html")

    return bp

