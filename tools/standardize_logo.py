"""Standardize logo path site-wide + stage crest into demo tree + rebuild icons."""
import os, shutil
from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OLD = "assets/images/other/saint_logo.png"
NEW = "assets/images/logo/original_logo.jpeg"
count = 0
for dirpath, _dirs, files in os.walk(ROOT):
    if ".git" in dirpath:
        continue
    for f in files:
        if f.endswith((".php", ".html", ".js", ".css", ".json")):
            p = os.path.join(dirpath, f)
            try:
                with open(p, "r", encoding="utf-8") as fh:
                    h = fh.read()
            except UnicodeDecodeError:
                continue
            # also normalize any backslash variant of the new path
            h2 = h.replace("assets\\images\\logo\\original_logo.jpeg", NEW)
            h2 = h2.replace(OLD, NEW)
            if h2 != h:
                with open(p, "w", encoding="utf-8") as fh:
                    fh.write(h2)
                count += 1
print("files updated:", count)

# stage crest into demo tree
src = os.path.join(ROOT, "assets", "images", "logo", "original_logo.jpeg")
dst_dir = os.path.join(ROOT, "docs", "assets", "images", "logo")
os.makedirs(dst_dir, exist_ok=True)
shutil.copyfile(src, os.path.join(dst_dir, "original_logo.jpeg"))
print("crest staged into docs/")

# rebuild PWA icons from the crest
im = Image.open(src).convert("RGBA")
w, h = im.size
side = min(w, h)
L = (w - side) // 2
T = (h - side) // 2
sq = im.crop((L, T, L + side, T + side))
out = os.path.join(ROOT, "docs", "assets")
for size in (180, 192, 512):
    sq.resize((size, size), Image.LANCZOS).save(os.path.join(out, f"icon-{size}.png"))
NAVY = (15, 23, 42, 255)
mask = Image.new("RGBA", (512, 512), NAVY)
mask.alpha_composite(sq.resize((410, 410), Image.LANCZOS), (51, 51))
mask.save(os.path.join(out, "icon-maskable-512.png"))
print("icons rebuilt from crest")
