import puppeteer from 'puppeteer-core';

const browser = await puppeteer.launch({
    executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
    headless: true,
    args: ['--no-sandbox', '--disable-dev-shm-usage', '--hide-scrollbars'],
});

const page = await browser.newPage();
await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 2 });
await page.goto('http://127.0.0.1:8000/katalog', { waitUntil: 'networkidle0' });

const info = await page.evaluate(() => {
    const links = [...document.querySelectorAll('.borrower-navbar a')].map((el) => {
        const rect = el.getBoundingClientRect();
        const style = getComputedStyle(el);
        return {
            text: el.textContent.trim().replace(/\s+/g, ' '),
            className: el.className,
            display: style.display,
            color: style.color,
            background: style.backgroundColor,
            x: Math.round(rect.x),
            y: Math.round(rect.y),
            width: Math.round(rect.width),
            height: Math.round(rect.height),
        };
    });

    const icon = document.querySelector('.borrower-navbar .bi-journal-bookmark');
    const iconRect = icon?.getBoundingClientRect();

    return {
        links,
        icon: iconRect ? { x: iconRect.x, y: iconRect.y, width: iconRect.width, height: iconRect.height } : null,
        pagination: document.querySelector('.pagination')?.parentElement?.textContent.trim().replace(/\s+/g, ' '),
    };
});

console.log(JSON.stringify(info, null, 2));

await page.screenshot({
    path: 'C:/Users/HP/AppData/Local/Temp/opencode/perpus-fase2/shots/mobile-navbar.png',
    clip: { x: 0, y: 0, width: 390, height: 80 },
});

await browser.close();
