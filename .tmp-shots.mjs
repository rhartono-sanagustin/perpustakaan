import puppeteer from 'puppeteer-core';
import fs from 'node:fs/promises';

const base = 'http://127.0.0.1:8000';
const outDir = 'C:/Users/HP/AppData/Local/Temp/opencode/perpus-fase2/shots';
const chrome = 'C:/Program Files/Google/Chrome/Application/chrome.exe';

await fs.mkdir(outDir, { recursive: true });

const browser = await puppeteer.launch({
    executablePath: chrome,
    headless: true,
    args: ['--no-sandbox', '--disable-dev-shm-usage', '--hide-scrollbars'],
});

const page = await browser.newPage();
page.on('console', (message) => {
    if (message.type() === 'error') {
        console.log(`BROWSER ERROR: ${message.text()}`);
    }
});

async function shot(name, path, viewport, fullPage = true) {
    await page.setViewport(viewport);
    await page.goto(base + path, { waitUntil: 'networkidle0' });
    await new Promise((resolve) => setTimeout(resolve, 350));
    await page.screenshot({ path: `${outDir}/${name}.png`, fullPage });
    console.log(`saved ${name}.png`);
}

const desktop = { width: 1440, height: 1000 };
const mobile = { width: 390, height: 844 };

await shot('desktop-katalog', '/katalog', desktop);
await shot('desktop-detail', '/katalog/laskar-pelangi', desktop);
await shot('mobile-katalog', '/katalog', mobile);
await shot('mobile-detail', '/katalog/laskar-pelangi', mobile);

await page.setViewport(desktop);
await page.goto(`${base}/login`, { waitUntil: 'networkidle0' });
await page.type('#login', 'admin@perpus.test');
await page.type('#password', 'password');
await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click('button[type="submit"]'),
]);
console.log(`logged in at ${page.url()}`);

await shot('desktop-admin-dashboard', '/admin/dashboard', desktop);
await shot('desktop-admin-books', '/admin/books', desktop);
await shot('desktop-admin-book-form', '/admin/books/1/edit', desktop);
await shot('desktop-admin-copies', '/admin/books/1/copies', desktop);
await shot('desktop-admin-categories', '/admin/categories', desktop);
await shot('desktop-admin-authors', '/admin/authors', desktop);
await shot('desktop-admin-publishers', '/admin/publishers', desktop);

await shot('mobile-admin-dashboard', '/admin/dashboard', mobile);
await shot('mobile-admin-books', '/admin/books', mobile);

await browser.close();
