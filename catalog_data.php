<?php
// Single source for the catalogue, shared by catalog.php and community_detail.php.

/** Make a URL-safe id from an item name, e.g. "Ohyul photocard" -> "ohyul-photocard". */
function slugify(string $text): string
{
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-');
}

/** All catalogue categories with their items. Prices are sample data (RM). */
function get_catalog(): array
{
    return [
        'Albums and mixtapes' => [
            'glyph' => '♪',
            'items' => [
                ['Shot Callers EP (sealed)',               'Debut EP, brand new',              69.00],
                ['Shot Callers EP (opened)',               'Includes poster, no photocard',    45.00],
                ['Training Day EP (sealed)',               'Mini album, brand new',            72.00],
                ['Training Day EP (opened)',               'Includes one random photocard',    55.00],
                ['4SHOBOIZ Mixtape (sealed)',              'Pre-debut mixtape, brand new',     58.00],
                ['4SHOBOIZ Vol. 2: 4SHOVILLE (sealed)',    'Latest mixtape, brand new',        62.00],
            ],
        ],
        'Fan gear and accessories' => [
            'glyph' => '★',
            'items' => [
                ['Slogan banner',            'Fan-made, 90 x 30 cm',            28.00],
                ['Acrylic keyring set',      'Set of 4, one per member',        32.00],
                ['Photocard binder (A5)',    '40 pockets, brown cover',         39.00],
                ['Cupsleeve kit',            'Printed sleeves for fan cafes',   15.00],
                ['Shottie tote bag',         'Canvas, fan-made design',         35.00],
                ['Lanyard and charm set',    'For concerts and meetups',        22.00],
            ],
        ],
        'Photocards and collectibles' => [
            'glyph' => '♥',
            'items' => [
                ['Ohyul photocard',          'Single card, near mint',          12.00],
                ['Ryul photocard',           'Single card, near mint',          12.00],
                ['Woojin photocard',         'Single card, near mint',          12.00],
                ['Louis photocard',          'Single card, near mint',          12.00],
                ['Full group set',           'All 4 members, same album',       42.00],
                ['Sleeves and toploaders',   'Pack of 50, protects your cards', 14.00],
            ],
        ],
    ];
}