<?php

return array (
  'providers' => 
  array (
    0 => 'Nova\\Plugins\\PluginServiceProvider',
    1 => 'Nova\\Auth\\AuthServiceProvider',
    2 => 'Nova\\Bus\\BusServiceProvider',
    3 => 'Nova\\Cache\\CacheServiceProvider',
    4 => 'Nova\\Routing\\RoutingServiceProvider',
    5 => 'Nova\\Cookie\\CookieServiceProvider',
    6 => 'Nova\\Database\\DatabaseServiceProvider',
    7 => 'Nova\\Encryption\\EncryptionServiceProvider',
    8 => 'Nova\\Filesystem\\FilesystemServiceProvider',
    9 => 'Nova\\Foundation\\Providers\\FoundationServiceProvider',
    10 => 'Nova\\Hashing\\HashServiceProvider',
    11 => 'Nova\\Language\\LanguageServiceProvider',
    12 => 'Nova\\Mail\\MailServiceProvider',
    13 => 'Nova\\Pagination\\PaginationServiceProvider',
    14 => 'Nova\\Pipeline\\PipelineServiceProvider',
    15 => 'Nova\\Queue\\QueueServiceProvider',
    16 => 'Nova\\Redis\\RedisServiceProvider',
    17 => 'Nova\\Auth\\Reminders\\ReminderServiceProvider',
    18 => 'Nova\\Session\\SessionServiceProvider',
    19 => 'Nova\\Validation\\ValidationServiceProvider',
    20 => 'Nova\\View\\ViewServiceProvider',
    21 => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    22 => 'Nova\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    23 => 'Nova\\Auth\\Reminders\\ConsoleServiceProvider',
    24 => 'Nova\\Cache\\ConsoleServiceProvider',
    25 => 'Nova\\Database\\MigrationServiceProvider',
    26 => 'Nova\\Database\\SeedServiceProvider',
    27 => 'Nova\\Log\\ConsoleServiceProvider',
    28 => 'Nova\\Plugins\\ConsoleServiceProvider',
    29 => 'Nova\\Routing\\ConsoleServiceProvider',
    30 => 'Nova\\Session\\ConsoleServiceProvider',
    31 => 'App\\Providers\\AppServiceProvider',
    32 => 'App\\Providers\\AuthServiceProvider',
    33 => 'App\\Providers\\EventServiceProvider',
    34 => 'App\\Providers\\RouteServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Nova\\Plugins\\PluginServiceProvider',
    1 => 'Nova\\Auth\\AuthServiceProvider',
    2 => 'Nova\\Routing\\RoutingServiceProvider',
    3 => 'Nova\\Cookie\\CookieServiceProvider',
    4 => 'Nova\\Database\\DatabaseServiceProvider',
    5 => 'Nova\\Encryption\\EncryptionServiceProvider',
    6 => 'Nova\\Filesystem\\FilesystemServiceProvider',
    7 => 'Nova\\Foundation\\Providers\\FoundationServiceProvider',
    8 => 'Nova\\Language\\LanguageServiceProvider',
    9 => 'Nova\\Session\\SessionServiceProvider',
    10 => 'Nova\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    11 => 'Nova\\Plugins\\ConsoleServiceProvider',
    12 => 'App\\Providers\\AppServiceProvider',
    13 => 'App\\Providers\\AuthServiceProvider',
    14 => 'App\\Providers\\EventServiceProvider',
    15 => 'App\\Providers\\RouteServiceProvider',
  ),
  'deferred' => 
  array (
    'Nova\\Bus\\Dispatcher' => 'Nova\\Bus\\BusServiceProvider',
    'Nova\\Bus\\Contracts\\DispatcherInterface' => 'Nova\\Bus\\BusServiceProvider',
    'Nova\\Bus\\Contracts\\QueueingDispatcherInterface' => 'Nova\\Bus\\BusServiceProvider',
    'cache' => 'Nova\\Cache\\CacheServiceProvider',
    'cache.store' => 'Nova\\Cache\\CacheServiceProvider',
    'memcached.connector' => 'Nova\\Cache\\CacheServiceProvider',
    'hash' => 'Nova\\Hashing\\HashServiceProvider',
    'mailer' => 'Nova\\Mail\\MailServiceProvider',
    'swift.mailer' => 'Nova\\Mail\\MailServiceProvider',
    'swift.transport' => 'Nova\\Mail\\MailServiceProvider',
    'paginator' => 'Nova\\Pagination\\PaginationServiceProvider',
    'Nova\\Pipeline\\Contracts\\HubInterface' => 'Nova\\Pipeline\\PipelineServiceProvider',
    'queue' => 'Nova\\Queue\\QueueServiceProvider',
    'queue.worker' => 'Nova\\Queue\\QueueServiceProvider',
    'queue.listener' => 'Nova\\Queue\\QueueServiceProvider',
    'queue.failer' => 'Nova\\Queue\\QueueServiceProvider',
    'command.queue.work' => 'Nova\\Queue\\QueueServiceProvider',
    'command.queue.listen' => 'Nova\\Queue\\QueueServiceProvider',
    'command.queue.restart' => 'Nova\\Queue\\QueueServiceProvider',
    'command.queue.subscribe' => 'Nova\\Queue\\QueueServiceProvider',
    'queue.connection' => 'Nova\\Queue\\QueueServiceProvider',
    'redis' => 'Nova\\Redis\\RedisServiceProvider',
    'auth.password' => 'Nova\\Auth\\Reminders\\ReminderServiceProvider',
    'auth.password.broker' => 'Nova\\Auth\\Reminders\\ReminderServiceProvider',
    'validator' => 'Nova\\Validation\\ValidationServiceProvider',
    'view' => 'Nova\\View\\ViewServiceProvider',
    'view.finder' => 'Nova\\View\\ViewServiceProvider',
    'view.engine.resolver' => 'Nova\\View\\ViewServiceProvider',
    'template' => 'Nova\\View\\ViewServiceProvider',
    'template.compiler' => 'Nova\\View\\ViewServiceProvider',
    'command.clear-compiled' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.command.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.console.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.event.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.down' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.environment' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.handler.command' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.handler.event' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.job.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.key.generate' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.listener.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.model.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.optimize' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.policy.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.provider.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.request.make' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.route.list' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.serve' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.tinker' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.up' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.view.clear' => 'Nova\\Foundation\\Providers\\ForgeServiceProvider',
    'command.auth.reminders' => 'Nova\\Auth\\Reminders\\ConsoleServiceProvider',
    'command.cache.clear' => 'Nova\\Cache\\ConsoleServiceProvider',
    'command.cache.table' => 'Nova\\Cache\\ConsoleServiceProvider',
    'migrator' => 'Nova\\Database\\MigrationServiceProvider',
    'migration.repository' => 'Nova\\Database\\MigrationServiceProvider',
    'command.migrate' => 'Nova\\Database\\MigrationServiceProvider',
    'command.migrate.rollback' => 'Nova\\Database\\MigrationServiceProvider',
    'command.migrate.reset' => 'Nova\\Database\\MigrationServiceProvider',
    'command.migrate.refresh' => 'Nova\\Database\\MigrationServiceProvider',
    'command.migrate.install' => 'Nova\\Database\\MigrationServiceProvider',
    'migration.creator' => 'Nova\\Database\\MigrationServiceProvider',
    'command.migrate.make' => 'Nova\\Database\\MigrationServiceProvider',
    'seeder' => 'Nova\\Database\\SeedServiceProvider',
    'command.seed' => 'Nova\\Database\\SeedServiceProvider',
    'command.log.clear' => 'Nova\\Log\\ConsoleServiceProvider',
    'command.controller.make' => 'Nova\\Routing\\ConsoleServiceProvider',
    'command.middleware.make' => 'Nova\\Routing\\ConsoleServiceProvider',
    'command.session.database' => 'Nova\\Session\\ConsoleServiceProvider',
  ),
  'when' => 
  array (
    'Nova\\Bus\\BusServiceProvider' => 
    array (
    ),
    'Nova\\Cache\\CacheServiceProvider' => 
    array (
    ),
    'Nova\\Hashing\\HashServiceProvider' => 
    array (
    ),
    'Nova\\Mail\\MailServiceProvider' => 
    array (
    ),
    'Nova\\Pagination\\PaginationServiceProvider' => 
    array (
    ),
    'Nova\\Pipeline\\PipelineServiceProvider' => 
    array (
    ),
    'Nova\\Queue\\QueueServiceProvider' => 
    array (
    ),
    'Nova\\Redis\\RedisServiceProvider' => 
    array (
    ),
    'Nova\\Auth\\Reminders\\ReminderServiceProvider' => 
    array (
    ),
    'Nova\\Validation\\ValidationServiceProvider' => 
    array (
    ),
    'Nova\\View\\ViewServiceProvider' => 
    array (
    ),
    'Nova\\Foundation\\Providers\\ForgeServiceProvider' => 
    array (
    ),
    'Nova\\Auth\\Reminders\\ConsoleServiceProvider' => 
    array (
    ),
    'Nova\\Cache\\ConsoleServiceProvider' => 
    array (
    ),
    'Nova\\Database\\MigrationServiceProvider' => 
    array (
    ),
    'Nova\\Database\\SeedServiceProvider' => 
    array (
    ),
    'Nova\\Log\\ConsoleServiceProvider' => 
    array (
    ),
    'Nova\\Routing\\ConsoleServiceProvider' => 
    array (
    ),
    'Nova\\Session\\ConsoleServiceProvider' => 
    array (
    ),
  ),
);
