const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
  });
}

walkDir(path.join(__dirname, 'src'), function(filePath) {
  if (!filePath.endsWith('.tsx') && !filePath.endsWith('.ts')) return;
  if (filePath.includes('CustomText.tsx')) return;
  if (filePath.includes('themed-text.tsx')) return; // ignore existing themed-text

  let content = fs.readFileSync(filePath, 'utf8');
  let originalContent = content;

  // Regex to find: import { ..., Text, ... } from 'react-native'
  // It's tricky. Let's just do a simpler approach.
  
  // If the file uses <Text> but imports it from react-native
  if (content.includes("from 'react-native'") && content.match(/\bText\b/)) {
    // 1. Remove Text from react-native import
    content = content.replace(/(import\s+{[^}]*)\bText\b\s*,?\s*([^}]*}\s+from\s+['"]react-native['"])/g, (match, p1, p2) => {
      // clean up commas
      let p1Clean = p1.replace(/,\s*$/, '');
      let p2Clean = p2.replace(/^\s*,/, '');
      return p1Clean + (p1Clean.endsWith('{') || p2Clean.startsWith('}') ? '' : ', ') + p2Clean;
    });

    // 2. Fix empty imports like import { } from 'react-native'
    content = content.replace(/import\s*{\s*}\s*from\s*['"]react-native['"];?\n?/g, '');

    // 3. Add CustomText import if we actually removed it
    if (content !== originalContent) {
      // Calculate relative path to components/CustomText
      let depth = filePath.split(path.sep).length - path.join(__dirname, 'src').split(path.sep).length;
      let relPath = depth === 1 ? './components/CustomText' : '../'.repeat(depth - 1) + 'components/CustomText';
      // Just use alias @/components/CustomText since it's expo router
      content = `import { Text } from '@/components/CustomText';\n` + content;
      
      fs.writeFileSync(filePath, content, 'utf8');
      console.log('Patched: ' + filePath);
    }
  }
});
