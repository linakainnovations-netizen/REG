"""Build PWA + logo icon set from docs/assets/logo.png (or saint photo when provided)."""
from PIL import Image
import os

SRC = os.path.join("docs", "assets", "logo.png")
OUT = os.path.join("docs", "assets")

im = Image.open(SRC).convert("RGBA")

# Square crop (center) for icons
w, h = im.size
side = min(w, h)
left, top = (w - side) // 2, (h - side) // 2
sq = im.crop((left, top, left + side, top + side))

# Standard icons (transparent bg kept)
for size in (180, 192, 512):
    sq.resize((size, size), Image.LANCZOS).save(os.path.join(OUT, f"icon-{size}.png"))

# Maskable icon: 10% safe padding on parish navy
NAVY = (15, 23, 42, 255)
mask = Image.new("RGBA", (512, 512), NAVY)
inner = sq.resize((410, 410), Image.LANCZOS)
mask.alpha_composite(inner, (51, 51))
mask.save(os.path.join(OUT, "icon-maskable-512.png"))

print("icons written:", sorted(f for f in os.listdir(OUT) if f.startswith("icon-")))
