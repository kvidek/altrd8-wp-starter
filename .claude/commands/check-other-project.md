# Check Other Project Folder

Use this command to inspect a different local project folder.

## Input

$ARGUMENTS

## Workflow

1. Treat `$ARGUMENTS` as the target folder path.
2. If no path is passed, ask for one absolute folder path.
3. Resolve the path and verify it exists and is a directory.
4. Switch operations to that folder path for subsequent reads/searches.
5. Start with a quick scan:
   - list top-level files/folders
   - identify stack (`package.json`, `composer.json`, `wp-config`, etc.)
   - report key entry points
6. Keep actions read-only unless the user explicitly asks for edits.
7. If access is blocked by sandbox/permissions, ask for approval and retry.

## Output

Return:
- the resolved folder path
- whether access worked
- a short overview of the project structure and detected tech
