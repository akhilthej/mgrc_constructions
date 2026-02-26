const fs = require('fs');
const path = require('path');

const outDir = path.join(__dirname, 'out');
const oldPath = path.join(outDir, '_next');
const newPath = path.join(outDir, 'assets'); // Renaming to 'assets' instead of 'next' to be safe

console.log('Starting export fix...');

if (fs.existsSync(oldPath)) {
    if (fs.existsSync(newPath)) {
        fs.rmSync(newPath, { recursive: true, force: true });
    }
    fs.renameSync(oldPath, newPath);
    console.log('Renamed _next to assets');
} else {
    console.log('_next directory not found, skipping rename.');
}

function replaceInFiles(dir, oldStr, newStr) {
    if (!fs.existsSync(dir)) return;
    const files = fs.readdirSync(dir);
    files.forEach(file => {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);
        if (stat.isDirectory()) {
            replaceInFiles(filePath, oldStr, newStr);
        } else if (file.endsWith('.html') || file.endsWith('.js') || file.endsWith('.css') || file.endsWith('.json')) {
            let content = fs.readFileSync(filePath, 'utf8');
            if (content.includes(oldStr)) {
                // Using regex for global replacement
                const regex = new RegExp(oldStr.replace(/\//g, '\\/'), 'g');
                content = content.replace(regex, newStr);
                fs.writeFileSync(filePath, content, 'utf8');
                console.log(`Updated paths in: ${path.relative(outDir, filePath)}`);
            }
        }
    });
}

console.log('Updating file references...');
replaceInFiles(outDir, '/_next/', '/assets/');

console.log('Export fix completed!');
