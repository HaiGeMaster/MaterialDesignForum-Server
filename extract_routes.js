const fs = require('fs');
const path = require('path');

// Api.php 文件路径
const apiFilePath = path.join(__dirname, 'src', 'Routes', 'Api.php');
// 输出文件路径
const outputFilePath = path.join(__dirname, 'routes.txt');

// 读取 Api.php 文件
fs.readFile(apiFilePath, 'utf-8', (err, data) => {
  if (err) {
    console.error('读取文件失败:', err);
    return;
  }

  // 正则表达式匹配路由路径
  // 匹配 $collector->get/post/put/delete('/api/...', ...) 格式
  const routeRegex = /\$collector->(get|post|put|delete)\(['"]\/api[^'"]+['"]/g;
  
  const routes = [];
  let match;
  
  while ((match = routeRegex.exec(data)) !== null) {
    // 提取路径部分
    const fullMatch = match[0];
    // 提取 HTTP 方法
    const method = match[1].toUpperCase();
    // 提取路径（去掉引号）
    const routePath = fullMatch.match(/['"]([^'"]+)['"]/)[1];
    
    routes.push(`${method} ${routePath}`);
  }

  // 排序路由
  routes.sort((a, b) => a.localeCompare(b));

  // 写入输出文件
  const content = routes.join('\n');
  
  fs.writeFile(outputFilePath, content, 'utf-8', (err) => {
    if (err) {
      console.error('写入文件失败:', err);
      return;
    }
    console.log(`成功提取 ${routes.length} 条路由路径`);
    console.log(`路由已保存到: ${outputFilePath}`);
  });
});
