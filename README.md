# Aura.Di config builder

Aura.Di is nice!. Looking at the PR [Disabling autoresolve by default](https://github.com/auraphp/Aura.Di/pull/76), I thought it will be nice to make a way much easier for people to make the configuration.

## Supports

* Constructor params only
* Supports lazyGet for aura/web-kernel

## Drawbacks

* No setter injection
* You still need make the necessary changes needed.

## Installation

```bash
composer require foa/di-config dev-master
```

## Usage

Pass a file 

```bash
vendor/bin/di-config-dump /real/path/to/file.php
```

Pass directory

```bash
vendor/bin/di-config-dump /real/path/to/directory
```
