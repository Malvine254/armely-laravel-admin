<?php

return [
    'assortment_size' => 10000,
    'image_enrichment_batch' => 50,

    // Balanced quotas prevent software, cables, and a single large feed category
    // from consuming the storefront. All eligible products remain searchable.
    'category_quotas' => [
        '01' => 1830, // laptops
        '02' => 1330, // desktops/workstations
        '03' => 1070, // monitors
        '04' => 1270, // networking
        '05' => 770,  // servers/storage
        '06' => 730,  // printers/scanners
        '07' => 430,  // memory/storage upgrades
        '08' => 600,  // docks/hubs
        '09' => 330,  // peripherals
        '10' => 430,  // power/UPS
        '11' => 330,  // conferencing
        '12' => 130,  // cables/adapters
        '13' => 270,  // security hardware
        '14' => 480,  // tablets/mobile
    ],

    'preferred_brands' => [
        'DELL', 'HP', 'HEWLETT PACKARD', 'LENOVO', 'APPLE', 'MICROSOFT',
        'CISCO', 'ARUBA', 'FORTINET', 'APC', 'EPSON', 'CANON', 'BROTHER',
        'SAMSUNG', 'PANASONIC', 'GETAC', 'LEXMARK', 'XEROX', 'LOGITECH',
    ],
];
