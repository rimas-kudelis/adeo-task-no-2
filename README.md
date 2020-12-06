# Adeo web task #2

(or so the file was named)

## Project Goal

Create a service which returns product recommendations depending on the weather forecast.

Following the requirements and suggestions laid out in the task, the service was created based on Symfony. The service
has exactly one endpoint at the moment:

```
GET /api/products/recommended/:location
```

which returns a series of product recommendations for the next three days, depending on the weather forecast for the
location in question. Location name must match one of the codes used by api.meteo.lt (e.g., `vilnius`, `kaunas`
, `abromiskes` etc.).

## Booting up the project locally

_This section lists commands to be performed in a typical Linux terminal. Commands to be run directly in your host
system terminal are prefixed with `$`, ones to be run in a container shell are prefixed with `>`._

1. Download or clone it from git:
   ```
   $ git clone https://github.com/rimas-kudelis/adeo-task-no-2.git
   ```
   or:
   ```
   $ git clone git@github.com:rimas-kudelis/adeo-task-no-2.git
   ```
2. CD into project dir and boot up the docker containers:
   ```
   $ docker-compose up
   ```
3. (First time only): open the php container shell and install project dependencies:
   ```
   $ docker-compose exec php bash
   > composer install
   ```
4. (First time only) while still in the container shell, initialize the database and seed it with some demo data:
   ```
   > php bin/console doctrine:migrations:migrate
   > php bin/console doctrine:fixtures:load
   ```

After doing this, the service should be accessible from your browser. Just point it to the following URL:

```
http://localhost/api/products/recommended/klaipeda
```

## Running tests

Phpspec tests are written for two classes: Meteo.lt client and product suggester. These tests may be run via the
following command:

```
$ vendor/bin/phpspec run
```

## Development notes

Well, this took me much more time than anticipated. My plan was to do everything in 8 hours, but in reality it took
around 12 (RIP my self-esteem), and that's not even taking the writing of this README into account.

Of course, there are still things that could be improved:

- A separate endpoint could be provided to list all available location names. It would mostly just proxy what meteo.lt
  returns though, so I didn't spend time implementing this.
- Only one dominant weather conditions code is now used for recommendations. In a real-life scenario, suggestions could
  be fine-tuned/weighed even better by searching for products that match multiple weather conditions.
- All responses from meteo.lt are cached for the same amount of time, which is 5 minutes. In reality, the list of
  locations and/or forecast types could be cached for much longer, or even stored locally.
- Installation of composer dependencies and bootstrapping of the database could be done in `Dockerfile`
  or `docker-entrypoint` (I think the latter makes more sense).
- More tests for less common scenarios (e.g. when `na` is the dominant condition code).
- Somewhat unexpectedly, response serialization caused me quite a headache. First, I was the missing PropertyAccess
  component, which meant that the stock ObjectNormalizer was not functioning. Then I was hitting circular references
  with my Product and WeatherCondition entities. I first solved this by serializing WeatherCondition entities into their
  code strings, but afterwards, I disabled their serialization from within products altogether. However, this is done in
  a simplistic way directly from within a controller. In a serious application, this should probably be done via
  serialization groups or other means.
- Disk cache is currently used for caching. In a production environment something else would do better.
