<?php

return [
    'hour' => $_ENV['CRON_HOUR'] ?? '02',
    'minute' => $_ENV['CRON_MINUTE'] ?? '00',
    'open_food_facts_url' => $_ENV['OPEN_FOOD_FACTS_URL'] ?? 'https://challenges.coode.sh/food/data/json',
    'import_limit_per_file' => (int)($_ENV['IMPORT_LIMIT_PER_FILE'] ?? 100),
];
