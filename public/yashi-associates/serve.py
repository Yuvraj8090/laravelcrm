from http.server import ThreadingHTTPServer, SimpleHTTPRequestHandler
from pathlib import Path
import os


def main():
    root = Path(__file__).resolve().parent
    os.chdir(root)
    server = ThreadingHTTPServer(("127.0.0.1", 8080), SimpleHTTPRequestHandler)
    print("Yashi Associates is running at http://127.0.0.1:8080")
    server.serve_forever()


if __name__ == "__main__":
    main()
