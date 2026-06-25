/**
 * card-screenshot.js
 * Usage: node card-screenshot.js <html-file> <output-png> <width> <height>
 *
 * Renders an HTML file to PNG using Puppeteer + local Chrome.
 * Called by MemberCardExporter via Laravel Process.
 */

const puppeteer = require('./../../node_modules/puppeteer');
const path      = require('path');
const fs        = require('fs');

const [,, htmlFile, outputFile, widthArg, heightArg] = process.argv;

if (!htmlFile || !outputFile) {
  console.error('Usage: node card-screenshot.js <html-file> <output-png> [width] [height]');
  process.exit(1);
}

const width  = parseInt(widthArg  || '800',  10);
const height = parseInt(heightArg || '450',  10);

(async () => {
  const browser = await puppeteer.launch({
    executablePath: '/usr/bin/google-chrome',
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu', '--disable-dev-shm-usage'],
    headless: true,
  });

  try {
    const page = await browser.newPage();
    await page.setViewport({ width, height, deviceScaleFactor: 2 });

    const fileUrl = 'file://' + path.resolve(htmlFile);
    await page.goto(fileUrl, { waitUntil: 'networkidle0', timeout: 15000 });

    await page.screenshot({
      path: outputFile,
      type: 'png',
      clip: { x: 0, y: 0, width, height },
    });
  } finally {
    await browser.close();
  }
})().catch(e => { console.error(e.message); process.exit(1); });
