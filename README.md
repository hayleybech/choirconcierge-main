# Choir Concierge

## Local installation instructions
### Initial setup
- Clone repo
- Install (and link) php 8.2
- Install node 20
- `nvm use 20`
- `herd composer install`
- `npm install`
- `npm run dev`

### Environment variables
- `herd php artisan key:generate`
- Add `NIGHTWATCH_TOKEN`
- Add `MIX_SENTRY_DSN`
- Enable a local provider for Outgoing mail - Uncomment Mailtrap or Herd
- Add a Google Maps API key - events section will not work without it

### Database setup
- `herd php artisan migrate`
- `herd php artisan tinker`
- Add a test tenant:
    ```
    $test = Tenant::create('test', 'Test Music Club Pty Ltd', 'Australia/Perth', ['has_gratis' => true]);
    $test->domains()->create(['domain' => 'test']);
    $test->ensembles()->create(['name' => 'The Test Tones']);
    $test->ensembles()->create(['name' => 'Test Tones Youth Chorus']);
  ```
- (Optional) Add more test tenants:
    ```
    $foo = Tenant::create('foo', 'Foo Fandango Pty Ltd', 'Australia/Brisbane');
    $foo->domains()->create(['domain' => 'foo']);
    $foo->ensembles()->create(['name' => 'Foo Fandango Chorus']);
    
    $bar = Tenant::create('bar', 'The Bar Barbers Pty Ltd', 'Australia/Sydney');
    $bar->domains()->create(['domain' => 'bar']);
    $bar->ensembles()->create(['name' => 'The Bar Barbers']);
    ```
### Periodic updates
- `herd php artisan boost:update`

### Testing setup
- make a DB for tests eg choirconcierge_test
- create `.env.testing` with DB credentials (copy `.env` first)

## Testing
### Testing the Incoming Mailbox
- Ensure the mailbox@test.choirconcierge.com login details are configured in the .env file
- `herd php artisan schedule:test` - choose `ProcessGroupMailbox`
- `herd php artisan queue:work`
Alternatively, visit this link: http://choirconcierge.test/test/mailbox/process