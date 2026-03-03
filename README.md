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

### Database setup
- `herd php artisan migrate`
- `herd php artisan tinker`
- Add a test tenant:
    ```
    $test = Tenant::create('test', 'Test Music Club Pty Ltd', 'Australia/Perth');
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

### Testing setup
- make a DB for tests eg choirconcierge_test
- create `.env.testing` with DB credentials (copy `.env` first)