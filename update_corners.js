const fs = require('fs');
const path = require('path');

const directory = '/Users/saranavarroreal/Desktop/uptime-server/resources/views/';

function replaceInFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');

    // Replace CSS custom properties
    content = content.replace(/border-radius:\s*1\.5rem;/g, 'border-radius: 0.125rem;');
    content = content.replace(/border-radius:\s*0\.875rem\s*!important;/g, 'border-radius: 0.125rem !important;');

    // Replace Tailwind classes for corners
    content = content.replace(/\brounded-(?:2xl|3xl|xl|lg|md|sm)\b/g, 'rounded-none');
    content = content.replace(/\brounded-\[.*?\]\b/g, 'rounded-none');

    // For buttons and inputs that might use rounded-full, let's keep them rectangular (except specific avatar/dots)
    // We'll replace rounded-full if it's on a button, input, or typical container, but let's be careful.
    // Instead of replacing all rounded-full, let's target specific large rounded-full uses.
    content = content.replace(/\b(px-\d+\s+py-\d+\s+|h-1[0-9]\s+w-1[0-9]\s+|h-[4-9]\s+w-[4-9]\s+|\bbutton\b.*?)rounded-full\b/g, '$1rounded-none');
    
    // Also catch some standard ones
    content = content.replace(/rounded-full/g, (match, offset, string) => {
        // If it's a small dot, keep it
        const context = string.substr(Math.max(0, offset - 30), 60);
        if (context.match(/w-[1-3]\s+h-[1-3]/) || context.match(/h-[1-3]\s+w-[1-3]/)) {
            return 'rounded-full';
        }
        // If it's an avatar or icon container with w-8/h-8 or larger, we can square it
        if (context.match(/w-[4-9]+\s+h-[4-9]+/) || context.match(/h-[4-9]+\s+w-[4-9]+/)) {
            return 'rounded-none';
        }
        if (context.match(/px-\d+\s+py-\d+/)) {
            return 'rounded-none';
        }
        return 'rounded-none';
    });

    // Make sure we didn't break things like `bg-current rounded-full` in dots
    content = content.replace(/h-([1-3]|1\.5|2\.5)\s+w-\1(?:[^>]*?)rounded-none/g, (match) => {
        return match.replace('rounded-none', 'rounded-full');
    });

    // Explicitly fix `rounded-full` on `h-[number] w-[number]` where number <= 3
    fs.writeFileSync(filePath, content, 'utf8');
}

function traverseDirectory(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            traverseDirectory(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            replaceInFile(fullPath);
            console.log('Processed:', fullPath);
        }
    }
}

traverseDirectory(directory);
console.log('Replacement complete.');
