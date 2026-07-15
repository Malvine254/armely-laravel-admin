<?php

return [
    'assortment_size' => 3000,
    'image_enrichment_batch' => 50,

    // Balanced quotas prevent software, cables, and a single large feed category
    // from consuming the storefront. All eligible products remain searchable.
    'category_quotas' => [
        '01' => 550, // laptops
        '02' => 400, // desktops/workstations
        '03' => 320, // monitors
        '04' => 380, // networking
        '05' => 230, // servers/storage
        '06' => 220, // printers/scanners
        '07' => 130, // memory/storage upgrades
        '08' => 180, // docks/hubs
        '09' => 100, // peripherals
        '10' => 130, // power/UPS
        '11' => 100, // conferencing
        '12' => 40,  // cables/adapters
        '13' => 80,  // security hardware
        '14' => 140, // tablets/mobile
    ],

    'preferred_brands' => [
        'DELL', 'HP', 'HEWLETT PACKARD', 'LENOVO', 'APPLE', 'MICROSOFT',
        'CISCO', 'ARUBA', 'FORTINET', 'APC', 'EPSON', 'CANON', 'BROTHER',
        'SAMSUNG', 'PANASONIC', 'GETAC', 'LEXMARK', 'XEROX', 'LOGITECH',
    ],
];
