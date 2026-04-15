"""
Clients page blueprint.

Serves `/clients.html` using your existing `clients.html` template.
"""

from flask import Blueprint, render_template


def create_clients_blueprint() -> Blueprint:
    """Create blueprint that serves clients page."""
    bp = Blueprint("pages_clients", __name__)

    @bp.route("/clients.html")
    def clients_html():
        return render_template("clients.html")

    return bp

