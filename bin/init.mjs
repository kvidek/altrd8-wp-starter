#!/usr/bin/env node
/**
 * Turns a fresh copy of this starter theme into a new project.
 *
 *   node bin/init.mjs <project-slug> [--repo <git-url>] [--install] [--keep-script]
 *
 * <project-slug>  kebab-case theme folder name / text domain, e.g. cool-project-2026
 * --repo          git URL of the new project's repository (replaces the starter's URL)
 * --install       run `composer install` and `npm install` afterwards
 * --keep-script   don't delete this script when done (default: it removes itself)
 *
 * The current slug is read from the `Text Domain` header in style.css, so the
 * script works on the starter itself and on any project derived from it. It
 * replaces three casings of that identifier everywhere in the theme:
 *
 *   kebab   cool-project-2026   theme slug, text domain, URLs, package name
 *   snake   cool_project_2026   ACF group/field keys, PHP constants
 *   Pascal  CoolProject2026     PHP class names (e.g. CoolProject2026Assets)
 *
 * It also renames files whose names contain any of them, regenerates the
 * update-checker `Identifier` in style.css, drops starter-only files and runs
 * sanity checks. node_modules/, vendor/ and static/dist/ are never touched.
 */

import { spawnSync } from "node:child_process";
import { randomBytes } from "node:crypto";
import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const THEME_DIR = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const SKIP_DIRS = new Set(["node_modules", "vendor", ".git", path.join("static", "dist")]);
const STARTER_ONLY_FILES = ["BLOCK_BACKLOG.md"];

const fail = (message) => {
    console.error(`\nError: ${message}`);
    process.exit(1);
};

// ---------------------------------------------------------------- arguments
const argv = process.argv.slice(2);
const flag = (name) => argv.includes(name);
const option = (name) => {
    const index = argv.indexOf(name);
    return index === -1 ? null : argv[index + 1] || fail(`${name} needs a value`);
};
const valueArgs = new Set([option("--repo")].filter(Boolean));
const slug = argv.find((arg) => !arg.startsWith("--") && !valueArgs.has(arg));
const newRepoUrl = option("--repo");

if (!slug) {
    fail("usage: node bin/init.mjs <project-slug> [--repo <git-url>] [--install] [--keep-script]");
}
if (!/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/.test(slug)) {
    fail(`"${slug}" is not a valid slug: use lowercase letters, digits and single hyphens, starting with a letter.`);
}

// ---------------------------------------------------------- name derivation
const snake = (s) => s.replace(/-/g, "_");
const pascal = (s) => s.split("-").map((part) => part[0].toUpperCase() + part.slice(1)).join("");

const styleCssPath = path.join(THEME_DIR, "style.css");
const styleCss = fs.readFileSync(styleCssPath, "utf8");
const oldSlug = (styleCss.match(/^Text Domain:\s*(\S+)/m) || [])[1];
if (!oldSlug) {
    fail("could not read the current slug from the `Text Domain` header in style.css.");
}
if (oldSlug === slug) {
    fail(`the theme is already named "${slug}".`);
}

// Longest/most specific first is unnecessary (the three casings never overlap),
// but the repo URL contains the kebab slug, so it must be replaced before it.
const casings = [
    [pascal(oldSlug), pascal(slug)],
    [snake(oldSlug), snake(slug)],
    [oldSlug, slug],
];

const deployPath = path.join(THEME_DIR, "deploy.php");
const oldRepoUrl = fs.existsSync(deployPath)
    ? (fs.readFileSync(deployPath, "utf8").match(/set\('repository',\s*'([^']+)'\)/) || [])[1]
    : null;

console.log(`Renaming "${oldSlug}" -> "${slug}"`);
casings.forEach(([from, to]) => console.log(`  ${from.padEnd(24)} -> ${to}`));
if (newRepoUrl && oldRepoUrl) {
    console.log(`  ${oldRepoUrl} -> ${newRepoUrl}`);
}

// ------------------------------------------------------------- file walking
const isSkipped = (relativePath) =>
    relativePath.split(path.sep).some((_, i, parts) => SKIP_DIRS.has(parts.slice(0, i + 1).join(path.sep)));

const walk = (dir) =>
    fs.readdirSync(dir, { withFileTypes: true }).flatMap((entry) => {
        const absolute = path.join(dir, entry.name);
        if (isSkipped(path.relative(THEME_DIR, absolute))) {
            return [];
        }
        return entry.isDirectory() ? walk(absolute) : [absolute];
    });

const isText = (file) => !fs.readFileSync(file).subarray(0, 8000).includes(0);
const selfPath = fileURLToPath(import.meta.url);

// ----------------------------------------------------- 1. replace file contents
let changedFiles = 0;
for (const file of walk(THEME_DIR)) {
    if (file === selfPath || !isText(file)) {
        continue;
    }
    const original = fs.readFileSync(file, "utf8");
    let updated = original;
    if (newRepoUrl && oldRepoUrl) {
        updated = updated.split(oldRepoUrl).join(newRepoUrl);
    }
    for (const [from, to] of casings) {
        updated = updated.split(from).join(to);
    }
    if (updated !== original) {
        fs.writeFileSync(file, updated);
        changedFiles += 1;
    }
}
console.log(`\nUpdated contents of ${changedFiles} files.`);

// ------------------------------------------------------- 2. rename file paths
let renamedFiles = 0;
for (const file of walk(THEME_DIR)) {
    if (file === selfPath) {
        continue;
    }
    const base = path.basename(file);
    const renamed = casings.reduce((name, [from, to]) => name.split(from).join(to), base);
    if (renamed !== base) {
        fs.renameSync(file, path.join(path.dirname(file), renamed));
        renamedFiles += 1;
    }
}
console.log(`Renamed ${renamedFiles} files.`);

// ------------------------------------- 3. update-checker identifier + cleanup
const newStyleCss = fs
    .readFileSync(styleCssPath, "utf8")
    .replace(/^Identifier:.*$/m, `Identifier: ${randomBytes(16).toString("hex")}`);
fs.writeFileSync(styleCssPath, newStyleCss);
console.log("Generated a new update-checker Identifier in style.css.");

for (const name of STARTER_ONLY_FILES) {
    const target = path.join(THEME_DIR, name);
    if (fs.existsSync(target)) {
        fs.rmSync(target);
        console.log(`Removed starter-only file ${name}.`);
    }
}

// ------------------------------------------------------------ 4. sanity checks
const problems = [];
const files = walk(THEME_DIR);

const lint = spawnSync("php", ["-v"], { stdio: "ignore" });
if (lint.error) {
    console.log("php not found, skipping PHP lint.");
} else {
    for (const file of files.filter((f) => f.endsWith(".php"))) {
        if (spawnSync("php", ["-l", file], { stdio: "ignore" }).status !== 0) {
            problems.push(`PHP syntax error: ${path.relative(THEME_DIR, file)}`);
        }
    }
}

const acfDir = path.join(THEME_DIR, "acf-json");
const acfFiles = fs.existsSync(acfDir) ? fs.readdirSync(acfDir).filter((f) => f.endsWith(".json")) : [];
const definedKeys = new Set();
for (const name of acfFiles) {
    try {
        const json = fs.readFileSync(path.join(acfDir, name), "utf8");
        JSON.parse(json);
        for (const [, key] of json.matchAll(/"key":\s*"((?:group|field)_[^"]+)"/g)) {
            definedKeys.add(key);
        }
    } catch {
        problems.push(`Invalid JSON: acf-json/${name}`);
    }
}
const referencePattern = new RegExp(`(?:group|field)_${snake(slug)}_[a-z0-9_]*[a-z0-9]`, "g");
const scanned = files.filter((f) => /\.(php|json)$/.test(f) && !f.endsWith("package.json") && !f.endsWith("package-lock.json"));
for (const file of scanned) {
    const missing = new Set(
        [...fs.readFileSync(file, "utf8").matchAll(referencePattern)].map(([key]) => key).filter((key) => !definedKeys.has(key)),
    );
    missing.forEach((key) => problems.push(`ACF key "${key}" referenced in ${path.relative(THEME_DIR, file)} is not defined in acf-json/`));
}

const leftovers = files.filter((f) => f !== selfPath && isText(f) && casings.some(([from]) => fs.readFileSync(f, "utf8").includes(from)));
leftovers.forEach((f) => problems.push(`Old name still present in ${path.relative(THEME_DIR, f)}`));

if (problems.length > 0) {
    console.error("\nSanity checks found problems:");
    problems.forEach((problem) => console.error(`  - ${problem}`));
    console.error("\nThe script was kept so you can re-run it after fixing these.");
    process.exit(1);
}
console.log("Sanity checks passed (PHP lint, ACF JSON + key references, no old name left).");

// ------------------------------------------------------------------ 5. install
if (flag("--install")) {
    for (const [command, args] of [["composer", ["install"]], ["npm", ["install"]]]) {
        console.log(`\n$ ${command} ${args.join(" ")}`);
        if (spawnSync(command, args, { cwd: THEME_DIR, stdio: "inherit" }).status !== 0) {
            fail(`\`${command} ${args.join(" ")}\` failed.`);
        }
    }
}

// ------------------------------------------------------------ 6. self-removal
if (!flag("--keep-script")) {
    fs.rmSync(selfPath);
    const binDir = path.dirname(selfPath);
    if (fs.readdirSync(binDir).length === 0) {
        fs.rmdirSync(binDir);
    }
    console.log("\nRemoved bin/init.mjs (pass --keep-script to keep it).");
}

console.log(`
Done. Still to do by hand:
  - set up WordPress, the database and plugins (ACF Pro, ACFE, ...)
  - activate the "${slug}" theme
  - fix wp-cli.yml (path/user) and the webpack proxy host if your local URL differs
  - fill in the {figma_link} placeholder in README.md
  - the brand palette and example blocks are inherited from the starter: adjust them for the new project
`);
