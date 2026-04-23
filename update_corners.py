import os
import re

directory = "/Users/saranavarroreal/Desktop/uptime-server/resources/views/"

def process_file(path):
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original = content

    content = re.sub(r'border-radius:\s*1\.5rem;', 'border-radius: 0; /* serious */', content)
    content = re.sub(r'border-radius:\s*0\.875rem\s*!important;', 'border-radius: 0 !important; /* serious */', content)

    content = re.sub(r'\brounded-(?:2xl|3xl|xl|lg|md|sm)\b', 'rounded-none', content)
    content = re.sub(r'\brounded-\[.*?\]', 'rounded-none', content)

    # First turn all rounded-full into rounded-none
    content = re.sub(r'\brounded-full\b', 'rounded-none', content)

    # Restore small dots
    def restore_dots(m):
        return m.group(1) + 'rounded-full'

    # things like "h-2 w-2 rounded-none"
    content = re.sub(r'(h-(?:[1-3]|1\.5|2\.5)\s+w-(?:[1-3]|1\.5|2\.5)[^>]+?)rounded-none', restore_dots, content)
    
    # things like "rounded-none animate-pulse"
    def restore_dots2(m):
        return 'rounded-full' + m.group(1)
    content = re.sub(r'rounded-none([^>]+?h-(?:[1-3]|1\.5|2\.5)\s+w-(?:[1-3]|1\.5|2\.5))', restore_dots2, content)

    if content != original:
        with open(path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {path}")

for root, dirs, files in os.walk(directory):
    for f in files:
        if f.endswith('.blade.php'):
            process_file(os.path.join(root, f))
print("Done")
