"""
Contact page blueprint.

Serves `/contact.html` using your existing `contact.html` template.
"""

from flask import Blueprint, render_template


def create_contact_blueprint() -> Blueprint:
    """Create blueprint that serves the contact page."""
    bp = Blueprint("pages_contact", __name__)

    @bp.route("/contact.html")
    def contact_html():
        return render_template("contact.html")

    return bp

