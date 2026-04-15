"""
Project entry point.

Runs the Flask server defined in `detail/app.py`.
Frontend files remain as-is (HTML/CSS/JS in the project root and `assets/`).
"""

from detail.app import create_app


if __name__ == "__main__":
    app = create_app()
    app.run(host="127.0.0.1", port=5000, debug=True)

