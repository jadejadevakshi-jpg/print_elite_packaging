"""
FAQ page blueprint.

Serves `/faq.html` using your existing `faq.html` template.
"""

from flask import Blueprint, render_template


def create_faq_blueprint() -> Blueprint:
    """Create blueprint that serves the FAQ page."""
    bp = Blueprint("pages_faq", __name__)

    @bp.route("/faq.html")
    def faq_html():
        return render_template("faq.html")

    return bp

