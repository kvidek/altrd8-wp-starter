# Block / Component Backlog

Ideas and specced-out-but-not-yet-built settings components for `App\blocks`. Move an entry into `.claude/commands/create-block.md`'s "First-block infrastructure" table once it's actually implemented, and delete it from here.

## Overlap (settings component)

**Status:** not started — spec only.

**Purpose:** the "section overlap" pattern — one section's edge visually bleeds into its neighbor (e.g. a card popping up out of the section below it, or a hero's shaped bottom edge overlapping the section that follows). Needs three coordinated pieces to actually render correctly, not just a negative margin on its own.

**Fields** (shared clone group, `group_altrd8_wp_starter_component_overlap.json`, same shape as the other Settings-tab components):

- `margin_top` — button_group, choices `none` / `small` / `medium` / `large` (matches the existing Spacing component's token naming — same vocabulary across every settings component, not a separate `sm`/`md`/`lg` scale). Negative top margin on `.o-section`, pulls this section up into the previous one.
- `margin_bottom` — same choices. Negative bottom margin, pulls the *following* section up into this one.
- `padding_top` — same choices. Extra padding on **the block's own root wrapper div** (e.g. `.c-intro-block`, `.c-media-with-content-block` — not `.o-container`, which stays purely about horizontal gutters and is shared by every block whether or not it uses Overlap). Keeps content from touching the edge it's overlapping when `margin_top` is active.
- `padding_bottom` — same, bottom.

**z-index mechanic (the part that makes this actually work, not just cosmetic):** a section with a negative margin must render *behind* whichever neighbor it overlaps into, or its own background paints over that neighbor's content/decoration in the overlap zone. So the consuming block's partial should add an `.o-section--negative` modifier class automatically whenever `margin_top` or `margin_bottom` is not `none` — **not** a separate editor-facing toggle. `.o-section--negative { z-index: -1; }` drops that section below its normal (`z-index: auto`) siblings regardless of DOM order.

This composes cleanly with the `isolation: isolate` already on `.o-section` (added for the bg-gradient component) — that only contains a section's *own children's* stacking, it doesn't affect how sibling `.o-section` elements rank against each other, so the negative z-index on the overlapping section still resolves correctly among siblings.

**Not yet decided:**
- Exact px values for `small`/`medium`/`large` margin and padding — should probably be smaller than the main Spacing component's scale (`small`/`medium`/`large` = `s-60`/`s-90`/`s-125`) since overlap amounts are typically finer, but reusing the same token names either way per the naming decision above. Pick actual values when building.
- `App\blocks\BlockSettings` reader method name (`get_overlap()`, matching the nested-array-not-flattened precedent from `get_bg_gradient()`) and its return shape: likely `['overlap' => ['margin_top' => ..., 'margin_bottom' => ..., 'padding_top' => ..., 'padding_bottom' => ..., 'negative' => bool]]` with `negative` computed from whether either margin is non-`none`, for the partial to conditionally add `.o-section--negative`.
- New SCSS utilities needed: negative-margin variants (e.g. `.u-mt-negative-{size}` / `.u-mb-negative-{size}`) — don't yet exist, `_utilities.spacing.scss` currently only has positive `.u-pt-*`/`.u-pb-*`.
