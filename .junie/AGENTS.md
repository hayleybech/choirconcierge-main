<?php
# AGENTS

## Which guidelines to follow
This project uses Laravel Boost, which includes a generalised guidelines file. Always follow that one, except when this files contradicts it.

# Best Practices
# Laravel Controllers
- Check the queries you write for N+1 issues and rewrite them as necessary. Prompt if resolving an N+1 requires a larger refactor.
- Always use FormRequests for controller validation.
- When reading input from the form request, prefer methods that return the validated data not the raw data. 
- Prefer Laravel collections over PHP array functions

## Testing
- Always use Pest format when writing tests.


