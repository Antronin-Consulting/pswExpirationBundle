# Antronin Consulting Password Expiration Bundle

This Symfony bundle provides a solution to enforce a password expiration policy for your users. It allows you to configure a password lifetime, warn users before their password expires, and check if a password has already expired.

## Features

- Configurable password lifetime.
- Configurable warning period before password expiration.
- An interface (`PasswordExpirationUserInterface`) to easily integrate with your User entity.
- A service (`PasswordExpirationChecker`) for checking password status.
- Twig functions (`is_password_expired`, `is_password_nearing_expiration`) to display warnings in your templates.

## Installation

Make sure Composer is installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require antronin-consulting/psw-expiration-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```console
composer require antronin-consulting/psw-expiration-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    AntroninConsulting\PswExpirationBundle\AntroninConsultingPswExpirationBundle::class => ['all' => true],
];
```

## Configuration

The bundle comes with a sensible default configuration. You can override it by creating a configuration file at `config/packages/psw_expiration.yaml`.

```yaml
# config/packages/psw_expiration.yaml
psw_expiration:
    # (Optional) The units for the below settings. Can be 'days', 'hours', 'weeks', 'months'
    # Default: days
    unit: 'days'

    # (Optional) Number of units after which a password expires.
    # Default: 90
    lifetime: 90

    # (Optional) Number of units before expiration to start showing warnings.
    # Default: 14
    warning_threshold: 14
```

## Usage

### 1. Implement the Interface

Your User entity must implement the `PasswordExpirationUserInterface`. This requires you to add a `lastPasswordChange` property and the corresponding getter/setter methods.

Here is an example using Doctrine ORM:

```php
// src/Entity/User.php
namespace App\Entity;

use AntroninConsulting\PswExpirationBundle\Security\PasswordExpirationUserInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
class User implements UserInterface, PasswordExpirationUserInterface
{
    // ... other properties

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastPasswordChange = null;

    public function getLastPasswordChange(): ?DateTimeInterface
    {
        return $this->lastPasswordChange;
    }

    public function setLastPasswordChange(DateTimeInterface $lastPasswordChange): void
    {
        $this->lastPasswordChange = $lastPasswordChange instanceof DateTimeImmutable
            ? $lastPasswordChange
            : DateTimeImmutable::createFromMutable($lastPasswordChange);
    }

    // ... other methods
}
```

**Important**: Whenever a user changes their password, you must update the `lastPasswordChange` date.

### 2. Display Warnings in Templates

You can use the provided Twig functions to check the user's password status and display a warning if necessary.

```twig
{# templates/base.html.twig #}
{% if app.user and is_granted('IS_AUTHENTICATED_FULLY') %}
    {% if is_password_expired(app.user) %}
        <div class="alert alert-danger">Your password has expired. Please change it immediately.</div>
    {% elseif is_password_nearing_expiration(app.user) %}
        <div class="alert alert-warning">Your password will expire soon. Please consider changing it.</div>
    {% endif %}
{% endif %}
```

## Translations

The bundle uses translatable enum cases for time units. If you want to display these units in your templates, you can provide translations for them. The translation domain is `PswExpirationBundle`.

Here are the keys you can translate:

- `unit.hours`
- `unit.days`
- `unit.weeks`
- `unit.months`

For example, to provide French translations, create a file like `translations/PswExpirationBundle.fr.yaml`:

```yaml
# translations/PswExpirationBundle.fr.yaml
unit.hours: heure(s)
unit.days: jour(s)
unit.weeks: semaine(s)
unit.months: mois
```
