"""
Login page helpers.

This file:
- renders `login.html` with an optional error message
- exposes a blueprint so the route `/login.html` exists
"""

from flask import Blueprint, render_template


def render_login_page(*, error: str = "") -> str:
    """Render `login.html` with an optional error message."""
    return render_template("login.html", error=error or "")


def create_login_blueprint() -> Blueprint:
    """Blueprint that serves the login page at `/login.html`."""
    bp = Blueprint("pages_login", __name__)

    @bp.route("/login.html")
    def login_html():
        return render_login_page(error="")

    return bp

